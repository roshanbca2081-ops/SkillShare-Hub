<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Messages';
$pdo = getDB();

$sql = "SELECT m.*, t.subject, u1.full_name as from_name, u2.full_name as to_name FROM messages m JOIN message_threads t ON m.thread_id = t.id JOIN users u1 ON m.from_user = u1.id JOIN users u2 ON m.to_user = u2.id ORDER BY m.created_at DESC LIMIT 100";
$messages = $pdo->query($sql)->fetchAll();

$totalMessages = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unreadMessages = (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
$totalThreads = (int)$pdo->query("SELECT COUNT(*) FROM message_threads")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Message Monitoring</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-envelope"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalMessages); ?></div>
                <div class="admin-stat-label">Total Messages</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-envelope-open"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($unreadMessages); ?></div>
                <div class="admin-stat-label">Unread</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-comments"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalThreads); ?></div>
                <div class="admin-stat-label">Conversations</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5><i class="fas fa-envelope" style="color:#3b82f6;"></i> Recent Messages</h5>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thread</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-envelope"></i><p>No messages found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td>#<?php echo $msg['id']; ?></td>
                            <td><?php echo htmlspecialchars($msg['subject'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($msg['from_name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['to_name']); ?></td>
                            <td><?php echo htmlspecialchars(truncate($msg['content'], 80)); ?></td>
                            <td>
                                <span class="admin-badge <?php echo $msg['is_read'] ? 'admin-badge-secondary' : 'admin-badge-warning'; ?>">
                                    <?php echo $msg['is_read'] ? 'Read' : 'Unread'; ?>
                                </span>
                            </td>
                            <td><?php echo admin_time_ago($msg['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

