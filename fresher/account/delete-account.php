<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $confirm = sanitize($_POST['confirm_delete'] ?? '');
    
    if ($confirm !== 'DELETE') {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Please type "DELETE" to confirm account deletion.'
        ];
        redirect('settings.php#danger');
    }
    
    $user_id = getUserId();
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Delete user data (cascade will handle related tables)
        // But we want to keep some data for analytics, so we'll soft delete
        
        // Anonymize user data
        $stmt = $pdo->prepare("UPDATE users SET 
                               full_name = 'Deleted User',
                               email = CONCAT('deleted_', id, '@deleted.skillsharehub.com'),
                               password = '',
                               bio = NULL,
                               title = NULL,
                               skills = NULL,
                               interests = NULL,
                               phone = NULL,
                               location = NULL,
                               website = NULL,
                               social_links = NULL,
                               avatar = NULL,
                               is_active = 0,
                               is_verified = 0,
                               reset_token = NULL,
                               reset_token_expiry = NULL,
                               remember_token = NULL,
                               deleted_at = NOW()
                               WHERE id = ?");
        $stmt->execute([$user_id]);
        
        // Log deletion
        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, description, data) 
                               VALUES (?, 'delete_account', 'Account permanently deleted', ?)");
        $stmt->execute([$user_id, json_encode(['deleted_at' => date('Y-m-d H:i:s')])]);
        
        $pdo->commit();
        
        // Clear session and logout
        $_SESSION = [];
        session_destroy();
        
        header('Location: ../../index.php?deleted=1');
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'An error occurred while deleting your account. Please try again.'
        ];
        redirect('settings.php#danger');
    }
} else {
    redirect('settings.php');
}
?>