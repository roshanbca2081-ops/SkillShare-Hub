<?php
$page_title = 'Interview Questions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$query = "SELECT q.*, f.name as field_name 
          FROM interview_questions q 
          LEFT JOIN academic_fields f ON q.field_id = f.id 
          WHERE q.mentor_id = ?";

$params = [$user_id];

if ($filter === 'published') {
    $query .= " AND q.is_published = 1";
} elseif ($filter === 'draft') {
    $query .= " AND q.is_published = 0";
}

$query .= " ORDER BY q.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$questions = $stmt->fetchAll();

// Get fields for filter
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_question'])) {
    $question = sanitize($_POST['question']);
    $answer = sanitize($_POST['answer']);
    $field_id = (int)$_POST['field_id'];
    $difficulty = sanitize($_POST['difficulty']);
    $category = sanitize($_POST['category']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO interview_questions (mentor_id, field_id, question, answer, difficulty, category, is_published) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $field_id, $question, $answer, $difficulty, $category, $is_published]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Question added successfully!'
    ];
    redirect('index.php');
}

if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE interview_questions SET is_published = NOT is_published WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$id, $user_id]);
    redirect('index.php');
}

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM interview_questions WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$id, $user_id]);
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Question deleted successfully.'
    ];
    redirect('index.php');
}
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
                <h1 class="h2">Interview Questions</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="?filter=all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'published' ? 'active' : ''; ?>" href="?filter=published">Published</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'draft' ? 'active' : ''; ?>" href="?filter=draft">Draft</a>
                </li>
            </ul>
            
            <?php if (!empty($questions)): ?>
                <div class="row g-4">
                    <?php foreach ($questions as $question): ?>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6><?php echo htmlspecialchars($question['question']); ?></h6>
                                    <span class="badge bg-<?php 
                                        echo $question['difficulty'] === 'easy' ? 'success' : 
                                            ($question['difficulty'] === 'medium' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($question['difficulty']); ?>
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <?php if ($question['field_name']): ?>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($question['field_name']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($question['category']): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($question['category']); ?></span>
                                    <?php endif; ?>
                                    <span class="badge bg-<?php echo $question['is_published'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $question['is_published'] ? 'Published' : 'Draft'; ?>
                                    </span>
                                </div>
                                <?php if ($question['answer']): ?>
                                    <p class="text-muted small"><?php echo substr($question['answer'], 0, 100); ?>...</p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group btn-group-sm">
                                    <a href="?toggle=1&id=<?php echo $question['id']; ?>" class="btn btn-info">
                                        <i class="fas fa-<?php echo $question['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                        <?php echo $question['is_published'] ? 'Unpublish' : 'Publish'; ?>
                                    </a>
                                    <a href="edit.php?id=<?php echo $question['id']; ?>" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=1&id=<?php echo $question['id']; ?>" class="btn btn-danger" 
                                       onclick="return confirm('Delete this question?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <h5>No questions added</h5>
                    <p class="text-muted">Add interview questions to help students prepare.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Question Modal -->
<div class="modal fade" id="createQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Interview Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <textarea name="question" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <textarea name="answer" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field</label>
                            <select name="field_id" class="form-select">
                                <option value="">Select Field</option>
                                <?php foreach ($fields as $field): ?>
                                <option value="<?php echo $field['id']; ?>"><?php echo htmlspecialchars($field['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="easy">Easy</option>
                                <option value="medium" selected>Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g., Technical, Behavioral">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_published" class="form-check-input" id="isPublished" checked>
                        <label class="form-check-label" for="isPublished">Publish immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_question" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>