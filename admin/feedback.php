<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Feedback & Reviews';
$pdo = getDB();
$search = $_GET['search'] ?? '';
$mentorFilter = $_GET['mentor'] ?? '';

$sql = "SELECT r.*, u.full_name as user_name, m.full_name as mentor_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN users m ON r.mentor_id = m.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR m.full_name LIKE ? OR r.comment LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($mentorFilter) {
    $sql .= " AND r.mentor_id = ?";
    $params[] = $mentorFilter;
}

$sql .= " ORDER BY r.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll();

$totalReviews = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$avgRating = (float)$pdo->query("SELECT COALESCE(AVG(rating), 0) FROM reviews")->fetchColumn();

$mentors = $pdo->query("SELECT id, full_name FROM users WHERE role = 'mentor' ORDER BY full_name")->fetchAll();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Feedback & Reviews</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-star"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($avgRating, 1); ?> / 5</div>
                <div class="admin-stat-label">Average Rating</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-star-half-alt"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalReviews); ?></div>
                <div class="admin-stat-label">Total Reviews</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search reviews..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="mentor" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Mentors</option>
                    <?php foreach ($mentors as $mentor): ?>
                    <option value="<?php echo $mentor['id']; ?>" <?php echo $mentorFilter == $mentor['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($mentor['full_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($search || $mentorFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>feedback.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Mentor</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-star"></i><p>No reviews found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>#<?php echo $review['id']; ?></td>
                            <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($review['mentor_name']); ?></td>
                            <td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star" style="color:<?php echo $i <= $review['rating'] ? '#f59e0b' : '#e2e8f0'; ?>;"></i>
                                <?php endfor; ?>
                                <span style="font-size:0.8rem;color:#64748b;">(<?php echo number_format($review['rating'], 1); ?>)</span>
                            </td>
                            <td><?php echo htmlspecialchars(truncate($review['comment'] ?: 'No comment', 100)); ?></td>
                            <td><?php echo admin_time_ago($review['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $review['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php?action=delete_review', 'Delete this review?')"><i class="fas fa-trash"></i></button>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>

