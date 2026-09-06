<?php
$page_title = 'Manage Questions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get all questions with filters
$field_id = isset($_GET['field']) ? (int)$_GET['field'] : 0;
$difficulty = isset($_GET['difficulty']) ? sanitize($_GET['difficulty']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT q.*, f.name as field_name 
          FROM interview_questions q 
          LEFT JOIN academic_fields f ON q.field_id = f.id 
          WHERE q.mentor_id = ?";

$params = [$user_id];

if ($field_id) {
    $query .= " AND q.field_id = ?";
    $params[] = $field_id;
}

if ($difficulty) {
    $query .= " AND q.difficulty = ?";
    $params[] = $difficulty;
}

if ($search) {
    $query .= " AND (q.question LIKE ? OR q.answer LIKE ? OR q.category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY q.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$questions = $stmt->fetchAll();

// Get fields for filter
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">All Questions</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search questions..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="field" class="form-select">
                                <option value="">All Fields</option>
                                <?php foreach ($fields as $field): ?>
                                <option value="<?php echo $field['id']; ?>" <?php echo $field_id == $field['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($field['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="difficulty" class="form-select">
                                <option value="">All Difficulties</option>
                                <option value="easy" <?php echo $difficulty === 'easy' ? 'selected' : ''; ?>>Easy</option>
                                <option value="medium" <?php echo $difficulty === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="hard" <?php echo $difficulty === 'hard' ? 'selected' : ''; ?>>Hard</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Results -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (!empty($questions)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Category</th>
                                        <th>Field</th>
                                        <th>Difficulty</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($questions as $question): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(substr($question['question'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($question['category'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($question['field_name'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $question['difficulty'] === 'easy' ? 'success' : 
                                                    ($question['difficulty'] === 'medium' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($question['difficulty']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $question['is_published'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $question['is_published'] ? 'Published' : 'Draft'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?php echo $question['id']; ?>" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?toggle=1&id=<?php echo $question['id']; ?>" class="btn btn-info">
                                                    <i class="fas fa-<?php echo $question['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                </a>
                                                <a href="?delete=1&id=<?php echo $question['id']; ?>" class="btn btn-danger" 
                                                   onclick="return confirm('Delete this question?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                            <h5>No questions found</h5>
                            <p class="text-muted">Try adjusting your filters.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>