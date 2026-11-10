<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireMentor();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'The revocation request could not be verified.'];
    redirect('index.php');
}

$certificateId = (int)($_POST['id'] ?? 0);
$stmt = $pdo->prepare("UPDATE certificates c
                       JOIN courses cr ON cr.id = c.course_id
                       SET c.is_valid = 0, c.updated_at = NOW()
                       WHERE c.id = ? AND cr.mentor_id = ? AND c.is_valid = 1");
$stmt->execute([$certificateId, getUserId()]);
$_SESSION['alert'] = $stmt->rowCount() > 0
    ? ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Certificate revoked successfully.']
    : ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'Certificate not found or already revoked.'];
redirect('index.php');
