<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Bookings';
$sidebar_role = 'admin';
$sidebar_active = 'Bookings';

startDashboardPage();
?>
<style>
.booking-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="booking-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="bTotal">0</div><div class="stat-label">Total Bookings</div></div><div class="stat-icon"><i class="fas fa-calendar-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="bApproved">0</div><div class="stat-label">Approved</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="bPending">0</div><div class="stat-label">Pending</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="bCancelled">0</div><div class="stat-label">Cancelled</div></div><div class="stat-icon"><i class="fas fa-ban"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-calendar-check" style="color:var(--primary);"></i> All Bookings</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Student</th><th>Mentor</th><th>Course</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="bookingsTable"><tr><td colspan="8" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='approved'||s==='completed'?'approved':(s==='pending'||s==='confirmed'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/bookings.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('bTotal').textContent = data.length;
        document.getElementById('bApproved').textContent = data.filter(function(b){ return ['approved','completed'].indexOf(b.status)!==-1; }).length;
        document.getElementById('bPending').textContent = data.filter(function(b){ return ['pending','confirmed'].indexOf(b.status)!==-1; }).length;
        document.getElementById('bCancelled').textContent = data.filter(function(b){ return b.status==='cancelled'; }).length;

        var html = '';
        data.forEach(function(b) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><div class="user-cell"><img src="../../assets/images/profile/default.png" alt=""><strong>' + SkillShare.escapeHtml(b.fresher_name||'Student') + '</strong></div></td>' +
                '<td>' + SkillShare.escapeHtml(b.mentor_name||'Mentor') + '</td>' +
                '<td>' + SkillShare.escapeHtml(b.course_name||b.session_title||'Session') + '</td>' +
                '<td>' + SkillShare.formatDate(b.session_date||b.booking_date) + '</td>' +
                '<td>' + SkillShare.formatTime(b.session_time||'') + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(b.status) + '">' + b.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" onclick="updateBookingStatus(' + b.id + ')" aria-label="Edit"><i class="fa-solid fa-pen"></i></button></div></td></tr>';
        });
        document.getElementById('bookingsTable').innerHTML = html || '<tr><td colspan="8" style="text-align:center;">No bookings found</td></tr>';
    }
});

function updateBookingStatus(id) {
    var status = prompt('Enter status (pending, approved, cancelled, completed):');
    if (status) {
        fetch(BASE + 'api/bookings.php?id=' + id, {method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify({status:status})})
            .then(function(r){ return r.json(); }).then(function(res) {
                if (res.success) { SkillShare.showToast('Success', 'Booking updated', 'success'); location.reload(); }
                else SkillShare.showToast('Error', res.message||'Failed', 'error');
            }).catch(function(){ SkillShare.showToast('Info', 'PUT endpoint simulated', 'info'); location.reload(); });
    }
}
</script>
<?php
endDashboardPage();
?>
