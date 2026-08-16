<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Profile';
$sidebar_role = 'fresher';
$sidebar_active = 'Profile';

startDashboardPage();
?>

<style>
.profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; }
.profile-sidebar { display: flex; flex-direction: column; gap: 16px; }
.profile-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 24px; text-align: center;
}
.profile-avatar {
    width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
    border: 3px solid var(--glass-border); margin-bottom: 12px;
}
.profile-name { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); }
.profile-role { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
.profile-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
.profile-stat { text-align: center; }
.profile-stat-value { font-size: 1.2rem; font-weight: 700; color: var(--primary-400); }
.profile-stat-label { font-size: 0.7rem; color: var(--text-muted); }
.profile-menu { list-style: none; padding: 0; margin: 0; }
.profile-menu li { margin-bottom: 4px; }
.profile-menu a {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    border-radius: var(--radius-md); color: var(--text-secondary);
    text-decoration: none; font-size: 0.85rem; transition: all 0.3s ease;
}
.profile-menu a:hover, .profile-menu a.active { background: rgba(59,130,246,0.1); color: var(--primary-400); }
.profile-menu a i { width: 18px; text-align: center; }

.profile-content { display: flex; flex-direction: column; gap: 20px; }
.panel-header-row { display: flex; justify-content: space-between; align-items: center; }
.avatar-upload { position: relative; display: inline-block; }
.avatar-upload input[type="file"] { display: none; }
.avatar-upload-label {
    position: absolute; bottom: 4px; right: 4px; width: 32px; height: 32px;
    background: var(--gradient-primary); border-radius: 50%; display: flex;
    align-items: center; justify-content: center; color: #fff; cursor: pointer;
    border: 2px solid rgba(10,10,30,0.8); font-size: 0.8rem;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group-full { grid-column: 1 / -1; }
@media (max-width: 768px) {
    .profile-layout { grid-template-columns: 1fr; }
    .form-grid { grid-template-columns: 1fr; }
}
</style>

<div class="profile-layout">
    <div class="profile-sidebar">
        <div class="profile-card">
            <img src="https://ui-avatars.com/100/?background=3b82f6&color=fff" alt="Profile" class="profile-avatar" id="profileAvatar">
            <div class="profile-name" id="profileName">Loading...</div>
            <div class="profile-role" id="profileRole">Fresher</div>
            <div class="profile-stats">
                <div class="profile-stat"><div class="profile-stat-value" id="statCourses">0</div><div class="profile-stat-label">Courses</div></div>
                <div class="profile-stat"><div class="profile-stat-value" id="statCerts">0</div><div class="profile-stat-label">Certificates</div></div>
            </div>
        </div>
        <div class="card" style="padding:8px;">
            <ul class="profile-menu">
                <li><a href="#" class="active" onclick="return false;"><i class="fas fa-user"></i> Personal Info</a></li>
                <li><a href="<?php echo BASE_URL; ?>dashboard/fresher/settings.php" onclick="return false;"><i class="fas fa-shield-halved"></i> Security</a></li>
                <li><a href="<?php echo BASE_URL; ?>dashboard/fresher/settings.php" onclick="return false;"><i class="fas fa-bell"></i> Notifications</a></li>
            </ul>
        </div>
    </div>

    <div class="profile-content">
        <div class="card" style="padding:24px;">
            <div class="panel-header-row">
                <h4 style="margin:0;"><i class="fas fa-id-card" style="color:var(--primary-400);margin-right:8px;"></i> Personal Information</h4>
                <div class="avatar-upload">
                    <img src="https://ui-avatars.com/100/?background=3b82f6&color=fff" alt="Profile" class="profile-avatar" id="editAvatar" style="width:60px;height:60px;">
                    <label for="avatarInput" class="avatar-upload-label"><i class="fas fa-camera"></i></label>
                    <input type="file" id="avatarInput" accept="image/*">
                </div>
            </div>
            <form id="profileForm" style="margin-top:20px;">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" name="full_name" id="fullName"></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" id="email" disabled></div>
                    <div class="form-group"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone" id="phone"></div>
                    <div class="form-group"><label class="form-label">Institution</label><input type="text" class="form-control" name="institution" id="institution"></div>
                    <div class="form-group"><label class="form-label">Academic Field</label>
                        <select class="form-control" name="academic_field_id" id="academicField"><option value="">Select field</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">Course</label>
                        <select class="form-control" name="course_id" id="course"><option value="">Select course</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">Education Level</label><input type="text" class="form-control" name="education_level" id="educationLevel"></div>
                    <div class="form-group"><label class="form-label">Graduation Year</label><input type="number" class="form-control" name="graduation_year" id="graduationYear"></div>
                    <div class="form-group form-group-full"><label class="form-label">Bio</label><textarea class="form-control" name="bio" id="bio" rows="3"></textarea></div>
                    <div class="form-group form-group-full"><label class="form-label">Interests</label><input type="text" class="form-control" name="interests" id="interests"></div>
                </div>
                <div style="margin-top:20px;display:flex;gap:12px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    <button type="button" class="btn btn-outline" id="resetBtn"><i class="fas fa-undo"></i> Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var userId = <?php echo getUserId(); ?>;

function renderProfile(user) {
    if (!user) { document.getElementById('profileName').textContent = 'User'; return; }
    document.getElementById('profileName').textContent = user.full_name || 'User';
    document.getElementById('profileRole').textContent = user.field_name || 'Fresher';
    document.getElementById('fullName').value = user.full_name || '';
    document.getElementById('email').value = user.email || '';
    document.getElementById('phone').value = user.phone || '';
    document.getElementById('institution').value = user.institution || '';
    document.getElementById('educationLevel').value = user.education_level || '';
    document.getElementById('graduationYear').value = user.graduation_year || '';
    document.getElementById('bio').value = user.bio || '';
    document.getElementById('interests').value = user.interests || '';
    document.getElementById('academicField').value = user.academic_field_id || '';
    document.getElementById('course').value = user.course_id || '';

    if (user.profile_picture && user.profile_picture !== 'default.png') {
        var avatarUrl = BASE + 'frontend/assets/images/profile/' + user.profile_picture;
        document.getElementById('profileAvatar').src = avatarUrl;
        document.getElementById('editAvatar').src = avatarUrl;
    }
}

SkillShare.apiFetch(BASE + 'api/me.php').then(function(res) {
    if (res.success && res.data) renderProfile(res.data);
});

SkillShare.apiFetch(BASE + 'api/dashboard/index.php').then(function(res) {
    if (res.success && res.data) {
        document.getElementById('statCourses').textContent = res.data.enrolled_courses || 0;
        document.getElementById('statCerts').textContent = res.data.certificates || 0;
    }
});

SkillShare.apiFetch(BASE + 'api/fields.php?action=list').then(function(res) {
    if (res.success) {
        var sel = document.getElementById('academicField');
        res.data.forEach(function(f) {
            var opt = document.createElement('option');
            opt.value = f.id; opt.textContent = f.name;
            sel.appendChild(opt);
        });
    }
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this);
    var avatarFile = document.getElementById('avatarInput').files[0];
    if (avatarFile) fd.append('profile_picture', avatarFile);

    fetch(BASE + 'api/profile.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Profile updated successfully', 'success'); }
            else { SkillShare.showToast('Error', res.message || 'Update failed', 'error'); }
        });
});

document.getElementById('resetBtn').addEventListener('click', function() {
    SkillShare.apiFetch(BASE + 'api/me.php').then(function(res) {
        if (res.success && res.data) renderProfile(res.data);
    });
});

document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files[0]) document.getElementById('profileForm').dispatchEvent(new Event('submit'));
});
</script>

<?php
endDashboardPage();
