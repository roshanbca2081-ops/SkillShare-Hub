<?php
// Redirect to main messages page with user_id parameter
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($user_id) {
    header('Location: index.php?user_id=' . $user_id);
} else {
    header('Location: index.php');
}
exit();
?>