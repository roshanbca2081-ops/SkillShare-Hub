<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Profile';
$sidebar_role = 'mentor';
$sidebar_active = 'Profile';

startDashboardPage();
?>

<style>
.profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; }
@media (max-width: 900px) { .profile-layout { grid-template-columns: 1fr; } }
.profile-sidebar { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 24px; text-align: center; height: fit-content; }
.profile-sidebar img { width: 100px; height: 100px; border-radius: var(--radius-full); object-fit: cover; border: 3px solid var(--primary-500); margin-bottom: 12px; }
.profile-sidebar h3 { font-size: 1.1rem; margin-bottom: 4px; }
.profile-sidebar p { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 12px; }
.profile-sidebar .rating { color: var(--warning); font-size: 0.9rem; margin-bottom: 8px; }
.profile-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 16px; }
.profile-stat { background: rgba(255,255,255,0.03); border-radius: var(--radius-md); padding: 10px; }
.profile-stat .num { font-weight: 700; font-size: 1.1rem; color: var(--primary-400); }
.profile-stat .lbl { font-size: 0.7rem; color: var(--text-muted); }
.panel { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 16px; }
.panel h4 { font-size: 1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
.form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; font-weight: 500; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); font-size: 0.9rem; outline: none; transition: all 0.3s ease; }
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--primary-500); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.form-group textarea { resize: vertical; min-height: 80px; }
.btn-primary { padding: 10px 20px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.85rem; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(59,130,246,0.3); }
.avail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; }
.avail-item { background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 12px; }
.avail-item label { display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; }
.avail-item input { width: 100%; padding: 6px 10px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 6px; color: var(--text-primary); font-size: 0.85rem; }
</style>

<div class="profile-layout">
    <div class="profile-sidebar" id="profileSidebar">
        <p style="color:var(--text-muted);">Loading profile...</p>
    </div>
    <div>
        <div class="panel">
            <h4><i class="fas fa-user" style="color:var(--primary-400);"></i> Personal Information</h4>
            <form id="profileForm">
                <div class="form-grid">
                    <div class="form-group"><label>Full Name</label><input type="text" name="full_name" id="profFullName"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" id="profEmail" disabled></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone" id="profPhone"></div>
                    <div class="form-group"><label>Academic Field</label><input type="text" name="field" id="profField" disabled></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Bio</label><textarea name="bio" id="profBio" rows="3"></textarea></div>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>

        <div class="panel" id="mentorExtraPanel">
            <h4><i class="fas fa-briefcase" style="color:var(--secondary-400);"></i> Professional Details</h4>
            <form id="mentorForm">
                <div class="form-grid">
                    <div class="form-group"><label>Specialization</label><input type="text" name="specialization" id="mentorSpec"></div>
                    <div class="form-group"><label>Experience (Years)</label><input type="number" name="experience_years" id="mentorExp"></div>
                    <div class="form-group"><label>Current Position</label><input type="text" name="current_position" id="mentorPos"></div>
                    <div class="form-group"><label>Current Company</label><input type="text" name="current_company" id="mentorComp"></div>
                    <div class="form-group"><label>Qualification</label><input type="text" name="qualification" id="mentorQual"></div>
                    <div class="form-group"><label>Certifications</label><input type="text" name="certifications" id="mentorCerts"></div>
                    <div class="form-group"><label>Languages</label><input type="text" name="languages" id="mentorLang"></div>
                    <div class="form-group"><label>Expertise Areas</label><input type="text" name="expertise_areas" id="mentorExpertise"></div>
                    <div class="form-group"><label>Portfolio URL</label><input type="url" name="portfolio_url" id="mentorPortfolio"></div>
                    <div class="form-group"><label>LinkedIn URL</label><input type="url" name="linkedin_url" id="mentorLinkedin"></div>
                    <div class="form-group"><label>GitHub URL</label><input type="url" name="github_url" id="mentorGithub"></div>
                    <div class="form-group"><label>Website URL</label><input type="url" name="website_url" id="mentorWebsite"></div>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Professional Details</button>
            </form>
        </div>

        <div class="panel">
            <h4><i class="fas fa-calendar-days" style="color:var(--success);"></i> Availability</h4>
            <form id="availabilityForm">
                <div class="avail-grid" id="availGrid"></div>
                <button type="submit" class="btn-primary" style="margin-top:14px;"><i class="fas fa-save"></i> Save Availability</button>
            </form>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderSidebar(user, extra) {
    var avatar = user.profile_picture && user.profile_picture !== 'default.png'
        ? BASE + 'frontend/assets/images/profile/' + user.profile_picture
        : 'https://ui-avatars.com/100/' + encodeURIComponent(user.full_name) + '?background=3b82f6&color=fff';
    var rating = parseFloat(extra.rating || 0).toFixed(1);
    var students = extra.total_students || 0;
    var courses = extra.courses_count || 0;
    document.getElementById('profileSidebar').innerHTML =
        '<img src="' + avatar + '" alt="Profile"><h3>' + SkillShare.escapeHtml(user.full_name) + '</h3>' +
        '<p>' + SkillShare.escapeHtml(extra.specialization || 'Mentor') + '</p>' +
        '<div class="rating"><i class="fas fa-star"></i> ' + rating + ' rating</div>' +
        '<div class="profile-stats"><div class="profile-stat"><div class="num">' + students + '</div><div class="lbl">Students</div></div>' +
        '<div class="profile-stat"><div class="num">' + courses + '</div><div class="lbl">Courses</div></div></div>';
}

SkillShare.apiFetch(BASE + 'api/profile.php').then(function(res) {
    if (res.success && res.data) {
        var d = res.data;
        renderSidebar(d, d);
        document.getElementById('profFullName').value = d.full_name || '';
        document.getElementById('profEmail').value = d.email || '';
        document.getElementById('profPhone').value = d.phone || '';
        document.getElementById('profField').value = d.field_name || '';
        document.getElementById('profBio').value = d.bio || '';
        document.getElementById('mentorSpec').value = d.specialization || '';
        document.getElementById('mentorExp').value = d.experience_years || '';
        document.getElementById('mentorPos').value = d.current_position || '';
        document.getElementById('mentorComp').value = d.current_company || '';
        document.getElementById('mentorQual').value = d.qualification || '';
        document.getElementById('mentorCerts').value = d.certifications || '';
        document.getElementById('mentorLang').value = d.languages || '';
        document.getElementById('mentorExpertise').value = d.expertise_areas || '';
        document.getElementById('mentorPortfolio').value = d.portfolio_url || '';
        document.getElementById('mentorLinkedin').value = d.linkedin_url || '';
        document.getElementById('mentorGithub').value = d.github_url || '';
        document.getElementById('mentorWebsite').value = d.website_url || '';
        var days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        var avail = d.availability || [];
        var dayMap = {};
        avail.forEach(function(a) { dayMap[a.day_of_week] = a; });
        var html = '';
        days.forEach(function(day) {
            var a = dayMap[day] || {};
            html += '<div class="avail-item"><label style="text-transform:capitalize;">' + day + '</label>' +
                '<input type="time" name="avail[' + day + '][start]" value="' + (a.start_time || '09:00') + '">' +
                '<input type="time" name="avail[' + day + '][end]" value="' + (a.end_time || '17:00') + '" style="margin-top:4px;">' +
                '<input type="checkbox" name="avail[' + day + '][available]" ' + (a.is_available ? 'checked' : '') + ' style="margin-top:4px;"> Available</div>';
        });
        document.getElementById('availGrid').innerHTML = html;
    }
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/profile.php', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Profile updated', 'success'); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

document.getElementById('mentorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/profile.php', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Professional details saved', 'success'); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

document.getElementById('availabilityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/profile.php', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Availability updated', 'success'); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});
</script>

<?php
endDashboardPage();
