<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Bookings';
$sidebar_role = 'mentor';
$sidebar_active = 'Bookings';

startDashboardPage();
?>

<style>
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 20px; }
.stat-box { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 14px 16px; text-align: center; }
.stat-box .num { font-family: var(--font-heading); font-weight: 700; font-size: 1.4rem; }
.stat-box .lbl { font-size: 0.75rem; color: var(--text-muted); }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.user-cell { display: flex; align-items: center; gap: 10px; }
.user-cell img { width: 36px; height: 36px; border-radius: var(--radius-full); object-fit: cover; }
.user-cell strong { font-size: 0.85rem; }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: capitalize; }
.status-badge.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-badge.status-confirmed { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
.status-badge.status-completed { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.btn-sm { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.btn-sm:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.btn-sm.danger:hover { background: var(--danger); border-color: var(--danger); color: #fff; }
</style>

<div class="stat-grid" id="bookingStats">
    <div class="stat-box"><div class="num" id="bTotal">0</div><div class="lbl">Total</div></div>
    <div class="stat-box"><div class="num" id="bApproved" style="color:var(--success);">0</div><div class="lbl">Approved</div></div>
    <div class="stat-box"><div class="num" id="bPending" style="color:var(--warning);">0</div><div class="lbl">Pending</div></div>
    <div class="stat-box"><div class="num" id="bCancelled" style="color:var(--danger);">0</div><div class="lbl">Cancelled</div></div>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Session</th><th>Date</th><th>Time</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="bookingsBody"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
function renderBookings(list) {
    var tbody = document.getElementById('bookingsBody');
    var stats = { total: 0, approved: 0, pending: 0, cancelled: 0 };
    if (!list || !list.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);">No bookings found</td></tr>'; document.getElementById('bTotal').textContent = '0'; return; }
    var html = '';
    list.forEach(function(b) {
        stats.total++;
        if (b.status === 'confirmed' || b.status === 'completed') stats.approved++;
        else if (b.status === 'pending') stats.pending++;
        else if (b.status === 'cancelled') stats.cancelled++;
        var actions = '';
        if (b.status === 'pending') {
            actions = '<button class="btn-sm" onclick="acceptBooking(' + b.id + ')">Accept</button>' +
                      '<button class="btn-sm danger" onclick="rejectBooking(' + b.id + ')">Reject</button>';
        } else if (b.status === 'confirmed') {
            actions = '<button class="btn-sm" onclick="SkillShare.showToast(\'Session\',\'Viewing session details\',\'info\')">View Session</button>';
        }
        html += '<tr><td><div class="user-cell"><img src="' + (b.fresher_avatar ? BASE + 'frontend/assets/images/profile/' + b.fresher_avatar : 'https://ui-avatars.com/36/FF?background=3b82f6&color=fff') + '" alt=""><strong>' + SkillShare.escapeHtml(b.fresher_name || 'Student') + '</strong></div></td>' +
            '<td>' + SkillShare.escapeHtml(b.session_title) + '</td>' +
            '<td>' + SkillShare.formatDate(b.session_date) + '</td>' +
            '<td>' + SkillShare.formatTime(b.session_time) + '</td>' +
            '<td>' + SkillShare.formatCurrency(b.total_amount) + '</td>' +
            '<td><span class="status-badge status-' + b.status + '">' + b.status + '</span></td>' +
            '<td><div style="display:flex;gap:6px;flex-wrap:wrap;">' + actions + '</div></td></tr>';
    });
    tbody.innerHTML = html;
    document.getElementById('bTotal').textContent = stats.total;
    document.getElementById('bApproved').textContent = stats.approved;
    document.getElementById('bPending').textContent = stats.pending;
    document.getElementById('bCancelled').textContent = stats.cancelled;
}

function acceptBooking(id) {
    SkillShare.confirm('Accept this booking?').then(function(ok) {
        if (!ok) return;
        SkillShare.apiFetch(BASE + 'api/bookings.php?action=accept&id=' + id, { method: 'POST' }).then(function(res) {
            if (res.success) { SkillShare.showToast('Accepted', 'Booking accepted successfully', 'success'); loadBookings(); }
            else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
        });
    });
}

function rejectBooking(id) {
    var reason = prompt('Reason for rejection (optional):');
    SkillShare.apiFetch(BASE + 'api/bookings.php?action=reject&id=' + id, { method: 'POST', body: { reason: reason || '' } }).then(function(res) {
        if (res.success) { SkillShare.showToast('Rejected', 'Booking rejected', 'success'); loadBookings(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
}

function loadBookings() {
    SkillShare.apiFetch(BASE + 'api/bookings.php?action=list').then(function(res) {
        if (res.success) renderBookings(res.data);
    });
}

loadBookings();
</script>

<?php
endDashboardPage();
