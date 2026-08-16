<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Academic Fields';
$pdo = getDB();
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM academic_fields WHERE 1=1";
$params = [];
if ($search) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY sort_order ASC, name ASC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$fields = $stmt->fetchAll();

$totalFields = (int)$pdo->query("SELECT COUNT(*) FROM academic_fields")->fetchColumn();
$activeFields = (int)$pdo->query("SELECT COUNT(*) FROM academic_fields WHERE status = 'active'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Academic Field Management</h3>
        <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openFieldModal()"><i class="fas fa-plus"></i> Add Field</button>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-layer-group"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalFields); ?></div>
                <div class="admin-stat-label">Total Fields</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($activeFields); ?></div>
                <div class="admin-stat-label">Active Fields</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search fields..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if ($search): ?>
                <a href="<?php echo ADMIN_URL; ?>academic-fields.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Slug</th>
                        <th>Color</th>
                        <th>Courses</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fields)): ?>
                    <tr><td colspan="6" class="admin-empty-state"><i class="fas fa-layer-group"></i><p>No academic fields found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($fields as $field): 
                            $courseCount = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE academic_field_id = " . (int)$field['id'])->fetchColumn();
                        ?>
                        <tr>
                            <td>
                                <div class="admin-user-cell">
                                    <div class="admin-stat-icon" style="background:<?php echo htmlspecialchars($field['color'] ?: '#3b82f6'); ?>;color:#fff;width:36px;height:36px;border-radius:8px;font-size:1rem;">
                                        <i class="fas <?php echo htmlspecialchars($field['icon'] ?: 'fa-book'); ?>"></i>
                                    </div>
                                    <strong><?php echo htmlspecialchars($field['name']); ?></strong>
                                </div>
                            </td>
                            <td><code><?php echo htmlspecialchars($field['slug']); ?></code></td>
                            <td><span style="display:inline-block;width:24px;height:24px;border-radius:6px;background:<?php echo htmlspecialchars($field['color'] ?: '#3b82f6'); ?>;"></span> <?php echo htmlspecialchars($field['color'] ?: '#3b82f6'); ?></td>
                            <td><?php echo number_format($courseCount); ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($field['status']); ?>"><?php echo htmlspecialchars($field['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn edit" title="Edit" onclick="editField(<?php echo $field['id']; ?>, '<?php echo htmlspecialchars($field['name']); ?>', '<?php echo htmlspecialchars($field['slug']); ?>', '<?php echo htmlspecialchars($field['color'] ?: '#3b82f6'); ?>', '<?php echo htmlspecialchars($field['icon'] ?: 'fa-book'); ?>', '<?php echo htmlspecialchars($field['status']); ?>')"><i class="fas fa-pen"></i></button>
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $field['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php?action=delete_field', 'Delete this field?')"><i class="fas fa-trash"></i></button>
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

<!-- Field Modal -->
<div class="admin-modal-overlay" id="fieldModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5 id="fieldModalTitle">Add Academic Field</h5>
            <button class="admin-modal-close" onclick="adminCloseModal('fieldModal')">&times;</button>
        </div>
        <div class="admin-modal-body">
            <form id="fieldForm">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="field_id" id="field_id">
                <div class="admin-form-group">
                    <label class="admin-form-label">Field Name</label>
                    <input type="text" name="name" id="field_name" class="admin-form-control" required placeholder="e.g. Information Technology">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Slug</label>
                    <input type="text" name="slug" id="field_slug" class="admin-form-control" required placeholder="e.g. information-technology">
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Color</label>
                        <input type="color" name="color" id="field_color" class="admin-form-control" value="#3b82f6" style="height:42px;padding:4px;">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Icon (FontAwesome class)</label>
                        <input type="text" name="icon" id="field_icon" class="admin-form-control" value="fa-book" placeholder="fa-book">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Status</label>
                    <select name="status" id="field_status" class="admin-form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="adminCloseModal('fieldModal')">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveField()">Save Field</button>
        </div>
    </div>
</div>

<script>
function openFieldModal() {
    document.getElementById('fieldModalTitle').textContent = 'Add Academic Field';
    document.getElementById('fieldForm').reset();
    document.getElementById('field_id').value = '';
    document.getElementById('field_color').value = '#3b82f6';
    document.getElementById('field_icon').value = 'fa-book';
    adminOpenModal('fieldModal');
}

function editField(id, name, slug, color, icon, status) {
    document.getElementById('fieldModalTitle').textContent = 'Edit Academic Field';
    document.getElementById('field_id').value = id;
    document.getElementById('field_name').value = name;
    document.getElementById('field_slug').value = slug;
    document.getElementById('field_color').value = color;
    document.getElementById('field_icon').value = icon;
    document.getElementById('field_status').value = status;
    adminOpenModal('fieldModal');
}

function saveField() {
    var id = document.getElementById('field_id').value;
    var data = {
        name: document.getElementById('field_name').value,
        slug: document.getElementById('field_slug').value,
        color: document.getElementById('field_color').value,
        icon: document.getElementById('field_icon').value,
        status: document.getElementById('field_status').value
    };
    
    var url = AdminPanel.baseUrl + 'actions/field-actions.php?action=save';
    if (id) url += '&id=' + id;
    
    AdminUtils.apiFetch(url, {method: 'POST', body: data}).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', res.message || 'Field saved', 'success');
            adminCloseModal('fieldModal');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}

document.getElementById('field_name').addEventListener('input', function() {
    if (!document.getElementById('field_id').value) {
        document.getElementById('field_slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

