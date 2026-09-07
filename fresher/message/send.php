<?php
// Quick message send endpoint
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

if ($receiver_id && !empty($message)) {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $message]);
    
    // Create notification for receiver
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                           VALUES (?, 'message', 'New Message', 
                                   CONCAT(?, ' sent you a message'), 
                                    'fresher/message/index.php?user_id=' || ?)");
    $stmt->execute([$receiver_id, getUserName(), $user_id]);
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>