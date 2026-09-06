<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$search = trim((string)($_GET['search'] ?? ''));
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
	$stmt = $pdo->prepare(
		"SELECT u.id, u.full_name, u.avatar, u.bio, u.title, u.skills,
				(SELECT COUNT(*) FROM courses c WHERE c.mentor_id = u.id AND c.status = 'active') AS course_count,
				(SELECT COALESCE(AVG(r.rating), 0) FROM ratings r WHERE r.mentor_id = u.id) AS avg_rating,
				(SELECT COUNT(*) FROM ratings r WHERE r.mentor_id = u.id) AS review_count
		 FROM users u
		 WHERE u.id = ? AND u.role = 'mentor' AND u.is_active = 1"
	);
	$stmt->execute([$id]);
	$mentor = $stmt->fetch();

	http_response_code($mentor ? 200 : 404);
	echo json_encode($mentor ?: ['error' => 'Mentor not found']);
	exit;
}

$sql =
	"SELECT u.id, u.full_name, u.avatar, u.bio, u.title, u.skills,
			(SELECT COUNT(*) FROM courses c WHERE c.mentor_id = u.id AND c.status = 'active') AS course_count,
			(SELECT COALESCE(AVG(r.rating), 0) FROM ratings r WHERE r.mentor_id = u.id) AS avg_rating,
			(SELECT COUNT(*) FROM ratings r WHERE r.mentor_id = u.id) AS review_count
	 FROM users u
	 WHERE u.role = 'mentor' AND u.is_active = 1";

$params = [];
if ($search !== '') {
	$sql .= " AND (u.full_name LIKE ? OR u.bio LIKE ? OR u.skills LIKE ? OR u.title LIKE ?)";
	$term = '%' . $search . '%';
	$params = [$term, $term, $term, $term];
}

$sql .= ' ORDER BY avg_rating DESC, course_count DESC, u.full_name ASC LIMIT 50';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(['mentors' => $stmt->fetchAll()]);
?>
