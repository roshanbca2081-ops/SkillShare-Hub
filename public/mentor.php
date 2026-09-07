<?php
$page_title = 'Mentors by Field, Course & Skills';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
$field_id = isset($_GET['field_id']) ? (int)$_GET['field_id'] : 0;
$skill = isset($_GET['skill']) ? sanitize($_GET['skill']) : null;

$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM courses WHERE mentor_id = u.id AND status = 'active') as course_count,
          (SELECT AVG(rating) FROM ratings WHERE mentor_id = u.id) as avg_rating,
          (SELECT COUNT(*) FROM ratings WHERE mentor_id = u.id) as review_count,
          (SELECT f.name FROM courses c 
           JOIN academic_fields f ON c.field_id = f.id 
           WHERE c.mentor_id = u.id AND c.status = 'active' 
           ORDER BY c.created_at DESC LIMIT 1) as field_name,
          (SELECT f.id FROM courses c 
           JOIN academic_fields f ON c.field_id = f.id 
           WHERE c.mentor_id = u.id AND c.status = 'active' 
           ORDER BY c.created_at DESC LIMIT 1) as field_id
          FROM users u 
          WHERE u.role = 'mentor' AND u.is_active = 1";

$params = [];

if ($search) {
    $query .= " AND (u.full_name LIKE ? OR u.bio LIKE ? OR u.skills LIKE ? OR u.title LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($field_id) {
    $query .= " AND (SELECT f.id FROM courses c 
                    JOIN academic_fields f ON c.field_id = f.id 
                    WHERE c.mentor_id = u.id AND c.status = 'active' 
                    ORDER BY c.created_at DESC LIMIT 1) = ?";
    $params[] = $field_id;
}

if ($skill) {
    $query .= " AND u.skills LIKE ?";
    $params[] = "%$skill%";
}

$query .= " ORDER BY (SELECT f.name FROM courses c 
                      JOIN academic_fields f ON c.field_id = f.id 
                      WHERE c.mentor_id = u.id AND c.status = 'active' 
                      ORDER BY c.created_at DESC LIMIT 1) ASC, 
              avg_rating DESC, course_count DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$mentors = $stmt->fetchAll();

$all_skills = [];
foreach ($mentors as $m) {
    if (!empty($m['skills'])) {
        $skills = array_map('trim', explode(',', $m['skills']));
        foreach ($skills as $s) {
            $s = ucfirst(strtolower($s));
            if (!isset($all_skills[$s])) {
                $all_skills[$s] = 0;
            }
            $all_skills[$s]++;
        }
    }
}
arsort($all_skills);
$top_skills = array_slice($all_skills, 0, 20, true);

$mentors_by_field = [];
foreach ($mentors as $mentor) {
    $field_name = $mentor['field_name'] ?? 'Other';
    if (!isset($mentors_by_field[$field_name])) {
        $mentors_by_field[$field_name] = [
            'field_id' => $mentor['field_id'] ?? 0,
            'mentors' => []
        ];
    }
    $mentors_by_field[$field_name]['mentors'][] = $mentor;
}

$selected_field = $field_id ? $fields[array_search($field_id, array_column($fields, 'id'))] : null;
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center mb-5">
            <h1 class="display-4 fw-bold">Find Your Mentor</h1>
            <p class="text-muted">Browse mentors by academic field, course, or skills</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search mentors by name, expertise..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="field_id" class="form-select">
                                <option value="">All Fields</option>
                                <?php foreach ($fields as $field): ?>
                                    <option value="<?php echo $field['id']; ?>" <?php echo $field_id == $field['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($field['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="skill" class="form-select">
                                <option value="">All Skills</option>
                                <?php foreach ($top_skills as $skill_name => $count): ?>
                                    <option value="<?php echo htmlspecialchars($skill_name); ?>" <?php echo ($skill ?? '') === $skill_name ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($skill_name); ?> (<?php echo $count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Filter
                            </button>
                        </div>
                        <?php if ($search || $field_id || $skill): ?>
                            <div class="col-md-2">
                                <a href="mentor.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-2"></i> Clear
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($selected_field): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-filter me-2"></i>
                    Showing mentors in <strong><?php echo htmlspecialchars($selected_field['name']); ?></strong>
                    <a href="mentor.php" class="alert-link ms-3">Show all fields</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($mentors_by_field)): ?>
        <?php foreach ($mentors_by_field as $field_name => $field_data): ?>
            <div class="mb-5">
                <div class="d-flex align-items-center mb-3">
                    <h2 class="h4 mb-0 me-3"><?php echo htmlspecialchars($field_name); ?></h2>
                    <span class="badge bg-primary"><?php echo count($field_data['mentors']); ?> mentor<?php echo count($field_data['mentors']) > 1 ? 's' : ''; ?></span>
                </div>
                
                <div class="row g-4">
                    <?php foreach ($field_data['mentors'] as $mentor): 
                        $mentor_skills = !empty($mentor['skills']) ? array_map('trim', explode(',', $mentor['skills'])) : [];
                        $mentor_courses = $pdo->prepare("SELECT id, title, slug FROM courses WHERE mentor_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 3");
                        $mentor_courses->execute([$mentor['id']]);
                        $courses = $mentor_courses->fetchAll();
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?php echo getAvatar($mentor); ?>" class="rounded-circle me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($mentor['full_name']); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars($mentor['title'] ?? 'Mentor'); ?></small>
                                    </div>
                                </div>
                                
                                <p class="text-muted small mb-3"><?php echo htmlspecialchars($mentor['bio'] ?? 'Experienced mentor ready to help you learn.'); ?></p>
                                
                                <?php if ($mentor['avg_rating']): ?>
                                <div class="text-warning mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= round($mentor['avg_rating']) ? '' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <small class="text-muted"><?php echo number_format($mentor['avg_rating'], 1); ?> (<?php echo $mentor['review_count'] ?? 0; ?> reviews)</small>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($mentor_skills)): ?>
                                <div class="mb-3">
                                    <?php foreach (array_slice($mentor_skills, 0, 4) as $skill_item): ?>
                                        <span class="badge bg-light text-dark me-1 mb-1"><?php echo htmlspecialchars(ucfirst(strtolower($skill_item))); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($courses)): ?>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Courses:</small>
                                    <?php foreach ($courses as $course): ?>
                                        <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="badge bg-primary text-decoration-none me-1 mb-1">
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="text-muted small">
                                        <i class="fas fa-book-open me-1"></i> <?php echo $mentor['course_count'] ?? 0; ?> courses
                                    </span>
                                    <a href="mentor-profile.php?id=<?php echo $mentor['id']; ?>" class="btn btn-sm btn-primary">
                                        View Profile <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="row">
            <div class="col-12 text-center py-5">
                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                <h5>No mentors found</h5>
                <p class="text-muted"><?php echo $search || $field_id || $skill ? 'Try adjusting your filters.' : 'Check back later for new mentors.'; ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
