<?php
// AJAX endpoint for getting resource details
require_once '../../../config/database.php';
require_once '../../../config/session.php';
require_once '../../../config/functions.php';
require_once '../../../config/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = getUserId();
$resource_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$resource_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid resource ID']);
    exit();
}

// Check access
$stmt = $pdo->prepare("SELECT r.*, c.title as course_title 
                       FROM resources r 
                       JOIN courses c ON r.course_id = c.id 
                       JOIN enrollments e ON e.course_id = c.id 
                       WHERE r.id = ? AND e.fresher_id = ? AND e.status != 'dropped'");
$stmt->execute([$resource_id, $user_id]);
$resource = $stmt->fetch();

if (!$resource) {
    echo json_encode(['success' => false, 'message' => 'Resource not found or access denied']);
    exit();
}

// Determine file icon
$file_icon = 'file';
if (strpos($resource['file_type'], 'pdf') !== false) $file_icon = 'file-pdf';
elseif (strpos($resource['file_type'], 'doc') !== false) $file_icon = 'file-word';
elseif (strpos($resource['file_type'], 'ppt') !== false) $file_icon = 'file-powerpoint';
elseif (strpos($resource['file_type'], 'video') !== false) $file_icon = 'file-video';
elseif (strpos($resource['file_type'], 'audio') !== false) $file_icon = 'file-audio';
elseif (strpos($resource['file_type'], 'zip') !== false) $file_icon = 'file-archive';
elseif (in_array($resource['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $file_icon = 'file-image';

// Get file size
$file_size = '';
$file_path = '../../../' . $resource['file_url'];
if (file_exists($file_path)) {
    $bytes = filesize($file_path);
    if ($bytes >= 1073741824) {
        $file_size = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $file_size = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $file_size = number_format($bytes / 1024, 2) . ' KB';
    } else {
        $file_size = $bytes . ' B';
    }
}

echo json_encode([
    'success' => true,
    'id' => $resource['id'],
    'title' => $resource['title'],
    'description' => $resource['description'],
    'file_url' => $resource['file_url'],
    'file_type' => $resource['file_type'],
    'file_icon' => $file_icon,
    'file_size' => $file_size,
    'course_title' => $resource['course_title'],
    'download_count' => $resource['download_count'],
    'created_at' => $resource['created_at']
]);
?>