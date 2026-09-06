<?php
require_once __DIR__ . '/../../config/database.php'; require_once __DIR__ . '/../../config/session.php'; require_once __DIR__ . '/../../config/functions.php'; require_once __DIR__ . '/../../config/auth.php'; requireAdmin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { $id=(int)($_POST['id']??0); if($id>0){ $stmt=$pdo->prepare('SELECT COUNT(*) FROM courses WHERE field_id=?'); $stmt->execute([$id]); if((int)$stmt->fetchColumn()===0){$stmt=$pdo->prepare('DELETE FROM academic_fields WHERE id=?');$stmt->execute([$id]);} } }
header('Location: index.php'); exit;
?>
