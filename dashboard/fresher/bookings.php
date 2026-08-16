<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Bookings';
$sidebar_role = 'fresher';
$sidebar_active = 'Bookings';

startDashboardPage();
?>

<style>
.booking-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.booking-stat {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 16px 20px;
}
.booking-stat-value { font-size: 1.4rem; font-weight: 700; color: var(--text-primary); }
.booking-stat-label { font-size: 0.75rem; color: var(--text-muted); }
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.filter-select {
    background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full);
    padding: 8px 16px; color: var(--text-secondary); font-size: 0.85rem; outline: none;
}
.table-responsive-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.data-table th { color: var(--text-muted); font-weight: 500; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.user-cell { display: flex; align-items: center; gap: 10px; }
.user-cell img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
.status-badge {
    padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
}
.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-confirmed { background: rgba(34,197,94,0.15); color: var(--success); }
.status-completed { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
.table-actions { display: flex; gap: 6px; }
.icon-btn {
    width: 32px; height: 32px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease; font-size: 0.8rem;
}
.icon-btn:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
.empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 12px; }
.modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000;
    display: none; align-items: center; justify-content: center; padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: rgba(20,20,40,0.95); border: 1px solid var(--glass-border); border-radius: var(--radius-lg);
    padding: 24px; max-width: 520px; width: 100%; max-height: 90vh; overflow-y: auto;
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-close { background: none; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; }
</style>

<div class="booking-stats" id="bookingStats">
    <div class="booking-stat"><div class="booking-stat-value" id="statTotal">0</div><div class="booking-stat-label">Total</div></div>
    <div class="booking-stat"><div class="booking-stat-value" id="statPending">0</div><div class="booking-stat-label">Pending</div></div>
    <div class="booking-stat"><div class="booking-stat-value" id="statConfirmed">0</div><div class="booking-stat-label">Confirmed</div></div>
    <div class="booking-stat"><div class="booking-stat-value" id="statCompleted">0</div><div class="booking-stat-label">Completed</div></div>
</div>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-calendar-check" style="color:var(--primary-400);margin-right:8px;"></i> My Bookings</h3>
    <div style="display:flex;gap:10px;align-items:center;">
        <select class="filter-select" id="statusFilter"><option value="">All Status</option><option value="pending">Pending</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
        <button class="btn btn-primary btn-sm" onclick="openBookingModal()"><i class="fas fa-plus"></i> New Booking</button>
    </div>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead><tr><th>Booking</th><th>Mentor</th><th>Date</th><th>Time</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="bookingsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="bookingModal">
    <div class="modal-box">
        <div class="modal-header"><h4 style="margin:0;">New Booking</h4><button class="modal-close" onclick="closeBookingModal()">&times;</button></div>
        <form id="bookingForm">
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Session Title</label><input type="text" class="form-control" name="session_title" required></div>
                <div class="form-group"><label class="form-label">Mentor</label><select class="form-control" name="mentor_id" id="modalMentor" required><option value="">Select mentor</option></select></div>
                <div class="form-group"><label class="form-label">Date</label><input type="date" class="form-control" name="session_date" required></div>
                <div class="form-group"><label class="form-label">Time</label><input type="time" class="form-control" name="session_time" required></div>
                <div class="form-group"><label class="form-label">Duration (min)</label><input type="number" class="form-control" name="duration" value="60"></div>
                <div class="form-group"><label class="form-label">Description</label><textarea class="form-control" name="session_description" rows="2"></textarea></div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-calendar-check"></i> Create Booking</button>
        </form>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderBookings(bookings) {
    var tbody = document.getElementById('bookingsTable');
    if (!bookings.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;"><i class="fas fa-calendar-times" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No bookings found</td></tr>'; return; }
    var html = '';
    bookings.forEach(function(b) {
        var actions = '';
        if (b.meeting_link) { actions += '<button class="icon-btn" title="Join" onclick="window.open(\'' + SkillShare.escapeHtml(b.meeting_link) + '\',\'_blank\')"><i class="fas fa-video"></i></button>'; }
        actions += '<button class="icon-btn" title="View" onclick="viewBooking(' + b.id + ')"><i class="fas fa-eye"></i></button>';
        if (b.payment_status === 'pending' && b.status === 'pending') {
            actions += '<button class="icon-btn" title="Pay Now" onclick="payNow(' + b.id + ')" style="color:var(--success);"><i class="fas fa-credit-card"></i></button>';
        }
        if (b.status === 'pending' || b.status === 'confirmed') {
            actions += '<button class="icon-btn" title="Cancel" onclick="cancelBooking(' + b.id + ')" style="color:var(--danger);"><i class="fas fa-ban"></i></button>';
        }
        html += '<tr>' +
            '<td><strong>' + SkillShare.escapeHtml(b.session_title) + '</strong><div style="font-size:0.75rem;color:var(--text-muted);">#' + SkillShare.escapeHtml(b.booking_number || '') + '</div></td>' +
            '<td><div class="user-cell"><img src="' + (b.mentor_avatar ? BASE + 'frontend/assets/images/profile/' + b.mentor_avatar : 'https://ui-avatars.com/32/?background=3b82f6&color=fff') + '" alt="">' + SkillShare.escapeHtml(b.mentor_name) + '</div></td>' +
            '<td>' + SkillShare.formatDate(b.session_date) + '</td>' +
            '<td>' + SkillShare.formatTime(b.session_time) + '</td>' +
            '<td>' + SkillShare.formatCurrency(b.total_amount) + '</td>' +
            '<td><span class="status-badge status-' + b.status + '">' + b.status + '</span></td>' +
            '<td><div class="table-actions">' + actions + '</div></td></tr>';
    });
    tbody.innerHTML = html;
}

function updateStats(data) {
    document.getElementById('statTotal').textContent = data.length;
    document.getElementById('statPending').textContent = data.filter(function(b){ return b.status === 'pending'; }).length;
    document.getElementById('statConfirmed').textContent = data.filter(function(b){ return b.status === 'confirmed'; }).length;
    document.getElementById('statCompleted').textContent = data.filter(function(b){ return b.status === 'completed'; }).length;
}

function loadBookings(status) {
    var params = '?action=list' + (status ? '&status=' + status : '');
    SkillShare.apiFetch(BASE + 'api/bookings.php' + params).then(function(res) {
        if (res.success) { renderBookings(res.data); updateStats(res.data); }
    });
}

document.getElementById('statusFilter').addEventListener('change', function() { loadBookings(this.value); });

function viewBooking(id) {
    SkillShare.apiFetch(BASE + 'api/bookings.php?action=show&id=' + id).then(function(res) {
        if (res.success) { SkillShare.showToast('Booking', res.data.session_title + ' - ' + res.data.status, 'info'); }
    });
}

function cancelBooking(id) {
    if (!SkillShare.confirm('Cancel this booking?' )) return;
    fetch(BASE + 'api/bookings.php?action=cancel&id=' + id, { method: 'POST' })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Booking cancelled', 'success'); loadBookings(); }
            else { SkillShare.showToast('Error', res.message, 'error'); }
        });
}

function payNow(id) {
    window.location.href = BASE + 'payment/esewa-initiate.php?booking_id=' + id;
}

function openBookingModal(mentorId) {
    document.getElementById('bookingModal').classList.add('active');
    var sel = document.getElementById('modalMentor');
    if (sel.options.length <= 1) {
        SkillShare.apiFetch(BASE + 'api/mentors.php?action=list').then(function(res) {
            if (res.success) {
                res.data.forEach(function(m) {
                    var opt = document.createElement('option');
                    opt.value = m.id; opt.textContent = m.full_name;
                    sel.appendChild(opt);
                });
                if (mentorId) sel.value = mentorId;
            }
        });
    } else if (mentorId) {
        sel.value = mentorId;
    }
}
function closeBookingModal() { document.getElementById('bookingModal').classList.remove('active'); }

var urlParams = new URLSearchParams(window.location.search);
var mentorIdParam = urlParams.get('mentor_id');
if (mentorIdParam) {
    openBookingModal(mentorIdParam);
}

document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this);
    fetch(BASE + 'api/bookings.php?action=create', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { SkillShare.showToast('Success', res.message, 'success'); closeBookingModal(); loadBookings(); }
            else { SkillShare.showToast('Error', res.message, 'error'); }
        });
});

loadBookings();
</script>

<?php
endDashboardPage();
