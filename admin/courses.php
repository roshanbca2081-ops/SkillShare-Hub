<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Courses';
$pdo = getDB();
$search = $_GET['search'] ?? '';
$fieldFilter = $_GET['field'] ?? '';

$sql = "SELECT c.*, af.name as field_name FROM courses c LEFT JOIN academic_fields af ON c.academic_field_id = af.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($fieldFilter) {
    $sql .= " AND c.academic_field_id = ?";
    $params[] = $fieldFilter;
}

$sql .= " ORDER BY c.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$allFields = $pdo->query("SELECT * FROM academic_fields ORDER BY name")->fetchAll();
$totalCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$activeCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();
$inactiveCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'inactive'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Course Management</h3>
        <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openCourseModal()"><i class="fas fa-plus"></i> Add Course</button>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-book-open"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalCourses); ?></div>
                <div class="admin-stat-label">Total Courses</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($activeCourses); ?></div>
                <div class="admin-stat-label">Active</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(255,255,255,0.05);color:var(--text-muted);"><i class="fas fa-eye-slash"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($inactiveCourses); ?></div>
                <div class="admin-stat-label">Inactive</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="field" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Fields</option>
                    <?php foreach ($allFields as $f): ?>
                    <option value="<?php echo $f['id']; ?>" <?php echo $fieldFilter == $f['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($f['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($search || $fieldFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>courses.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Field</th>
                        <th>Level</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                    <tr><td colspan="6" class="admin-empty-state"><i class="fas fa-book-open"></i><p>No courses found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td>
                                <div class="admin-user-cell">
                                    <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;width:36px;height:36px;border-radius:8px;font-size:0.9rem;">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($course['name']); ?></strong>
                                        <div class="user-email"><?php echo htmlspecialchars(truncate($course['description'] ?: '', 60)); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($course['field_name'] ?: 'N/A'); ?></td>
                            <td><span class="admin-badge admin-badge-primary"><?php echo htmlspecialchars($course['level'] ?: 'beginner'); ?></span></td>
                            <td><?php echo $course['rating'] ? number_format($course['rating'], 1) . ' / 5' : 'N/A'; ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($course['status']); ?>"><?php echo htmlspecialchars($course['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn edit" title="Edit" onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars($course['name']); ?>', '<?php echo htmlspecialchars($course['academic_field_id']); ?>', '<?php echo htmlspecialchars($course['level']); ?>', '<?php echo htmlspecialchars($course['status']); ?>')"><i class="fas fa-pen"></i></button>
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $course['id']; ?>, '<?php echo ADMIN_URL; ?>actions/course-actions.php?action=delete', 'Delete this course?')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Course Modal -->
<div class="admin-modal-overlay" id="courseModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5 id="courseModalTitle">Add Course</h5>
            <button class="admin-modal-close" onclick="adminCloseModal('courseModal')">&times;</button>
        </div>
        <div class="admin-modal-body">
            <form id="courseForm">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="course_id" id="course_id">
                <div class="admin-form-group">
                    <label class="admin-form-label">Course Name</label>
                    <input type="text" name="name" id="course_name" class="admin-form-control" required>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Description</label>
                    <textarea name="description" id="course_description" class="admin-form-control" rows="3"></textarea>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Academic Field</label>
                        <select name="academic_field_id" id="course_field" class="admin-form-control" required>
                            <option value="">Select Field</option>
                            <?php foreach ($allFields as $f): ?>
                            <option value="<?php echo $f['id']; ?>"><?php echo htmlspecialchars($f['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Level</label>
                        <select name="level" id="course_level" class="admin-form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Status</label>
                    <select name="status" id="course_status" class="admin-form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="adminCloseModal('courseModal')">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveCourse()">Save Course</button>
        </div>
    </div>
</div>

<script>
function openCourseModal() {
    document.getElementById('courseModalTitle').textContent = 'Add Course';
    document.getElementById('courseForm').reset();
    document.getElementById('course_id').value = '';
    adminOpenModal('courseModal');
}

function editCourse(id, name, fieldId, level, status) {
    document.getElementById('courseModalTitle').textContent = 'Edit Course';
    document.getElementById('course_id').value = id;
    document.getElementById('course_name').value = name;
    document.getElementById('course_field').value = fieldId;
    document.getElementById('course_level').value = level;
    document.getElementById('course_status').value = status;
    adminOpenModal('courseModal');
}

function saveCourse() {
    var id = document.getElementById('course_id').value;
    var form = document.getElementById('courseForm');
    var formData = new FormData(form);
    var data = {};
    formData.forEach(function(v, k) { data[k] = v; });
    
    var url = AdminPanel.baseUrl + 'actions/course-actions.php?action=save';
    if (id) url += '&id=' + id;
    
    AdminUtils.apiFetch(url, {method: 'POST', body: data}).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', res.message || 'Course saved', 'success');
            adminCloseModal('courseModal');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

