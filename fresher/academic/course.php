<?php
$page_title = 'Courses by Field';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$field_id = isset($_GET['field']) ? (int)$_GET['field'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Resolve only active fields and send invalid/stale field links back to the list.
$field = null;
if ($field_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM academic_fields WHERE id = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$field_id]);
    $field = $stmt->fetch();
    if (!$field) {
        $_SESSION['alert'] = [
            'type' => 'warning',
            'icon' => 'exclamation-triangle',
            'message' => 'That academic field is unavailable. Choose an active field below.',
        ];
        redirect('fiels.php');
    }
}

$query = "SELECT c.*, u.full_name as mentor_name, u.avatar 
          FROM courses c 
          JOIN users u ON c.mentor_id = u.id 
          WHERE c.status = 'active'";
$params = [];

if ($field_id) {
    $query .= " AND c.field_id = ?";
    $params[] = $field_id;
}

if ($search) {
    $query .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY c.featured DESC, c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Get all fields for filter
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();
$courseRecommendations = require '../../config/course-recommendations.php';
$recommendations = $field ? ($courseRecommendations[$field['name']] ?? []) : [];
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <?php if ($field): ?>
                        <?php echo htmlspecialchars($field['name']); ?> Courses
                    <?php else: ?>
                        All Courses
                    <?php endif; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="fiels.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> All Fields
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="hidden" name="field" value="<?php echo $field_id; ?>">
                        <input type="text" name="search" class="form-control" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        <?php if ($search): ?>
                            <a href="course.php?field=<?php echo $field_id; ?>" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <select name="field" class="form-select" onchange="this.form.submit()">
                            <option value="">All Fields</option>
                            <?php foreach ($fields as $f): ?>
                            <option value="<?php echo $f['id']; ?>" <?php echo $field_id == $f['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($f['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Courses Grid -->
            <div class="row g-4">
                <?php foreach ($courses as $course): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="course-card">
                        <div class="course-card-image">
                            <img src="<?php echo htmlspecialchars(getCourseThumbnail($course), ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo $course['title']; ?>">
                            <?php if ($course['featured']): ?>
                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">Featured</span>
                            <?php endif; ?>
                            <?php if ($course['price'] == 0): ?>
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">Free</span>
                            <?php endif; ?>
                        </div>
                        <div class="course-card-body">
                            <h6 class="course-card-title"><?php echo htmlspecialchars($course['title']); ?></h6>
                            <div class="course-card-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($course['mentor_name']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo $course['duration']; ?>h</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary"><?php echo getCourseLevel($course['level']); ?></span>
                                <span class="badge bg-info"><?php echo $course['total_students'] ?? 0; ?> students</span>
                            </div>
                        </div>
                        <div class="course-card-footer">
                            <span class="course-card-price">
                                <?php if ($course['discount_price']): ?>
                                    <span class="original">$<?php echo number_format($course['price'], 2); ?></span>
                                    $<?php echo number_format($course['discount_price'], 2); ?>
                                <?php else: ?>
                                    $<?php echo number_format($course['price'], 2); ?>
                                <?php endif; ?>
                            </span>
                            <a href="../../public/course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($courses)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>No published courses found</h5>
                    <p class="text-muted">Try adjusting your search or filters, or explore the suggested learning paths below.</p>
                    <a href="course.php<?php echo $field_id ? '?field=' . (int)$field_id : ''; ?>" class="btn btn-primary">Clear Search</a>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($recommendations): ?>
            <section class="mt-5" aria-labelledby="learningPathSuggestions">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                    <div>
                        <span class="badge text-bg-info mb-2">CURATED ROADMAP IDEAS</span>
                        <h2 class="h4 mb-1" id="learningPathSuggestions">In-demand learning paths for <?php echo htmlspecialchars($field['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p class="text-muted small mb-0">These are suggested topics, not published courses. An approved mentor and course content are needed before enrollment is available.</p>
                    </div>
                    <span class="badge text-bg-secondary"><?php echo count($recommendations); ?> suggestions</span>
                </div>
                <div class="row g-3">
                    <?php foreach ($recommendations as $recommendation): ?>
                    <div class="col-md-6 col-xl-4">
                        <article class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <span class="badge bg-primary-subtle text-primary">Suggested</span>
                                    <span class="badge bg-secondary-subtle text-secondary"><?php echo htmlspecialchars(ucfirst($recommendation['level']), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <h3 class="h6 card-title"><?php echo htmlspecialchars($recommendation['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="card-text small text-muted mb-2"><?php echo htmlspecialchars($recommendation['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <small class="text-muted"><i class="fas fa-clock me-1" aria-hidden="true"></i>Approx. <?php echo (int)$recommendation['hours']; ?> learning hours</small>
                            </div>
                        </article>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>