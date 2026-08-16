<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Interview Preparation';
$pdo = getDB();
$categoryFilter = $_GET['category'] ?? '';
$difficultyFilter = $_GET['difficulty'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM interview_questions WHERE 1=1";
$params = [];

if ($categoryFilter) {
    $sql .= " AND category = ?";
    $params[] = $categoryFilter;
}
if ($difficultyFilter) {
    $sql .= " AND difficulty = ?";
    $params[] = $difficultyFilter;
}
if ($search) {
    $sql .= " AND question LIKE ?";
    $params[] = "%$search%";
}

$sql .= " ORDER BY created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$questions = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM interview_questions WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$totalQuestions = (int)$pdo->query("SELECT COUNT(*) FROM interview_questions")->fetchColumn();
$pendingQuestions = (int)$pdo->query("SELECT COUNT(*) FROM interview_questions WHERE status = 'pending'")->fetchColumn();
$approvedQuestions = (int)$pdo->query("SELECT COUNT(*) FROM interview_questions WHERE status = 'approved'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Interview Preparation</h3>
        <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openQuestionModal()"><i class="fas fa-plus"></i> Add Question</button>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-comments"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalQuestions); ?></div>
                <div class="admin-stat-label">Total Questions</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($pendingQuestions); ?></div>
                <div class="admin-stat-label">Pending</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($approvedQuestions); ?></div>
                <div class="admin-stat-label">Approved</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search questions..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $categoryFilter === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="difficulty" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Difficulty</option>
                    <option value="easy" <?php echo $difficultyFilter === 'easy' ? 'selected' : ''; ?>>Easy</option>
                    <option value="medium" <?php echo $difficultyFilter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="hard" <?php echo $difficultyFilter === 'hard' ? 'selected' : ''; ?>>Hard</option>
                </select>
                <?php if ($search || $categoryFilter || $difficultyFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>interview.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Category</th>
                        <th>Difficulty</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($questions)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-comments"></i><p>No questions found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($questions as $q): ?>
                        <tr>
                            <td>#<?php echo $q['id']; ?></td>
                            <td><?php echo htmlspecialchars(truncate($q['question'], 80)); ?></td>
                            <td><span class="admin-badge admin-badge-primary"><?php echo htmlspecialchars($q['category'] ?: 'General'); ?></span></td>
                            <td><span class="admin-badge <?php echo $q['difficulty'] === 'easy' ? 'admin-badge-success' : ($q['difficulty'] === 'medium' ? 'admin-badge-warning' : 'admin-badge-danger'); ?>"><?php echo htmlspecialchars($q['difficulty']); ?></span></td>
                            <td><?php echo htmlspecialchars($q['company'] ?: 'N/A'); ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($q['status']); ?>"><?php echo htmlspecialchars($q['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ($q['status'] === 'pending'): ?>
                                    <button class="admin-action-btn approve" title="Approve" onclick="updateQuestionStatus(<?php echo $q['id']; ?>, 'approved')"><i class="fas fa-check"></i></button>
                                    <button class="admin-action-btn reject" title="Reject" onclick="updateQuestionStatus(<?php echo $q['id']; ?>, 'rejected')"><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $q['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php?action=delete_question', 'Delete this question?')"><i class="fas fa-trash"></i></button>
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
function updateQuestionStatus(id, status) {
    if (!AdminPanel.confirm('Update question to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/interview-actions.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Question updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}

function openQuestionModal() {
    AdminPanel.showToast('Info', 'Add question via API or form integration', 'info');
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

