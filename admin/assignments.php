<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Assignments';
$pdo = getDB();

$sql = "SELECT a.*, u.full_name as mentor_name, c.name as course_name FROM assignments a JOIN users u ON a.mentor_id = u.id LEFT JOIN courses c ON a.course_id = c.id ORDER BY a.created_at DESC LIMIT 100";
$assignments = $pdo->query($sql)->fetchAll();

$totalAssignments = (int)$pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
$totalSubmissions = (int)$pdo->query("SELECT COUNT(*) FROM assignment_submissions")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Assignment Management</h3>
        <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openAssignmentModal()"><i class="fas fa-plus"></i> View All</button>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-file-pen"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalAssignments); ?></div>
                <div class="admin-stat-label">Total Assignments</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-upload"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalSubmissions); ?></div>
                <div class="admin-stat-label">Submissions</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5><i class="fas fa-file-pen" style="color:#3b82f6;"></i> Recent Assignments</h5>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Mentor</th>
                        <th>Course</th>
                        <th>Deadline</th>
                        <th>Attachment</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                    <tr><td colspan="8" class="admin-empty-state"><i class="fas fa-file-pen"></i><p>No assignments found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td>#<?php echo $assignment['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($assignment['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($assignment['mentor_name']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['course_name'] ?: 'N/A'); ?></td>
                            <td><?php echo admin_format_datetime($assignment['deadline']); ?></td>
                            <td>
                                <?php if ($assignment['attachment']): ?>
                                <a href="<?php echo BASE_URL . 'uploads/' . htmlspecialchars($assignment['attachment']); ?>" target="_blank" class="admin-btn admin-btn-sm admin-btn-secondary"><i class="fas fa-download"></i></a>
                                <?php else: ?>
                                N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo admin_format_date($assignment['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn view" title="View Submissions" onclick="viewSubmissions(<?php echo $assignment['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $assignment['id']; ?>, '<?php echo ADMIN_URL; ?>actions/assignment-actions.php?action=delete', 'Delete this assignment?')"><i class="fas fa-trash"></i></button>
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

<script>
function viewSubmissions(assignmentId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', AdminPanel.apiUrl + 'assignments.php?action=submissions&assignment_id=' + assignmentId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success && res.data) {
                var msg = 'Submissions for Assignment #' + assignmentId + ':\n\n';
                res.data.forEach(function(s) {
                    msg += '- ' + (s.user_name || 'User ' + s.user_id) + ': ' + (s.status || 'submitted') + ' | File: ' + (s.file_path || 'N/A') + '\n';
                });
                alert(msg || 'No submissions found');
            } else {
                AdminPanel.showToast('Info', 'No submissions found or API not available', 'info');
            }
        }
    };
    xhr.send();
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

