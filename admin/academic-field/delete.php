<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
	$_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'The delete request could not be verified. Please try again.'];
	header('Location: index.php');
	exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id < 1) {
	$_SESSION['alert'] = ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'Choose a valid academic field to delete.'];
	header('Location: index.php');
	exit;
}

try {
	$pdo->beginTransaction();

	// Never let a database cascade remove courses or learner work with a field.
		$fkStmt = $pdo->prepare('SELECT COUNT(*)
							 FROM information_schema.KEY_COLUMN_USAGE kcu
							 JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
							   ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
							  AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
							 WHERE kcu.REFERENCED_TABLE_SCHEMA = DATABASE()
							   AND kcu.REFERENCED_TABLE_NAME = ?
															 AND kcu.REFERENCED_COLUMN_NAME = ?
															 AND rc.DELETE_RULE = ?');
		$fkStmt->execute(['academic_fields', 'id', 'CASCADE']);
		$cascadeCount = (int)$fkStmt->fetchColumn();

		if ($cascadeCount > 0) {
		$pdo->rollBack();
				$_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'This database is configured to cascade-delete records with this field. The field was not deleted; deactivate it instead or update those relationships first.'];
		header('Location: index.php');
		exit;
	}

	$stmt = $pdo->prepare('DELETE FROM academic_fields WHERE id = ?');
	$stmt->execute([$id]);
	$deleted = $stmt->rowCount();
	$pdo->commit();

	$_SESSION['alert'] = $deleted
		? ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Academic field deleted. Related course and research records were preserved without the field assignment.']
		: ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'Academic field not found.'];
} catch (PDOException $e) {
	if ($pdo->inTransaction()) {
		$pdo->rollBack();
	}
	error_log('Academic field deletion failed: ' . $e->getMessage());
	$_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'The academic field could not be deleted because it is still referenced. Deactivate the field or reassign its records, then try again.'];
}

header('Location: index.php');
exit;
?>
