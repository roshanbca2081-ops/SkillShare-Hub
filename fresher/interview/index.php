<?php
$page_title = 'Interview Preparation';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$field_id = isset($_GET['field']) ? (int)$_GET['field'] : 0;
$difficulty = isset($_GET['difficulty']) ? sanitize($_GET['difficulty']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT q.*, u.full_name as mentor_name, u.avatar, f.name as field_name, f.color as field_color
          FROM interview_questions q 
          JOIN users u ON q.mentor_id = u.id 
          LEFT JOIN academic_fields f ON q.field_id = f.id 
          WHERE q.is_published = 1";
$params = [];

if ($field_id) {
    $query .= " AND q.field_id = ?";
    $params[] = $field_id;
}

if ($difficulty) {
    $query .= " AND q.difficulty = ?";
    $params[] = $difficulty;
}

if ($category) {
    $query .= " AND q.category = ?";
    $params[] = $category;
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

// Get categories
$stmt = $pdo->query("SELECT DISTINCT category FROM interview_questions WHERE category IS NOT NULL AND is_published = 1 ORDER BY category");
$categories = $stmt->fetchAll();

// Get fields
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();

// Get user's saved questions
$stmt = $pdo->prepare("SELECT question_id FROM interview_saved WHERE fresher_id = ?");
$stmt->execute([$user_id]);
$saved_questions = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get statistics
$stats = [
    'total' => count($questions),
    'easy' => 0,
    'medium' => 0,
    'hard' => 0
];

foreach ($questions as $q) {
    if ($q['difficulty'] === 'easy') $stats['easy']++;
    elseif ($q['difficulty'] === 'medium') $stats['medium']++;
    elseif ($q['difficulty'] === 'hard') $stats['hard']++;
}

// Handle save/unsave
if (isset($_GET['save']) && isset($_GET['id'])) {
    $question_id = (int)$_GET['id'];
    if (in_array($question_id, $saved_questions)) {
        $stmt = $pdo->prepare("DELETE FROM interview_saved WHERE fresher_id = ? AND question_id = ?");
        $stmt->execute([$user_id, $question_id]);
        $message = 'Question removed from saved list.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO interview_saved (fresher_id, question_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $question_id]);
        $message = 'Question saved for later review.';
    }
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => $message
    ];
    redirect('index.php?' . http_build_query($_GET));
}
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
                <h1 class="h2">Interview Preparation</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="practice.php" class="btn btn-primary me-2">
                        <i class="fas fa-play"></i> Practice Mode
                    </a>
                    <a href="questions.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> All Questions
                    </a>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-primary"><?php echo $stats['total']; ?></h3>
                            <small class="text-muted">Total Questions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-success"><?php echo $stats['easy']; ?></h3>
                            <small class="text-muted">Easy</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-warning"><?php echo $stats['medium']; ?></h3>
                            <small class="text-muted">Medium</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-danger"><?php echo $stats['hard']; ?></h3>
                            <small class="text-muted">Hard</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search questions..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Field</label>
                            <select name="field" class="form-select">
                                <option value="">All Fields</option>
                                <?php foreach ($fields as $field): ?>
                                <option value="<?php echo $field['id']; ?>" <?php echo $field_id == $field['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($field['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="">All</option>
                                <option value="easy" <?php echo $difficulty === 'easy' ? 'selected' : ''; ?>>Easy</option>
                                <option value="medium" <?php echo $difficulty === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="hard" <?php echo $difficulty === 'hard' ? 'selected' : ''; ?>>Hard</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Questions Grid -->
            <?php if (!empty($questions)): ?>
                <div class="row g-4">
                    <?php foreach ($questions as $question): ?>
                        <?php
                        $is_saved = in_array($question['id'], $saved_questions);
                        $difficulty_colors = [
                            'easy' => 'success',
                            'medium' => 'warning',
                            'hard' => 'danger'
                        ];
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0"><?php echo htmlspecialchars($question['question']); ?></h6>
                                        <span class="badge bg-<?php echo $difficulty_colors[$question['difficulty']] ?? 'secondary'; ?>">
                                            <?php echo ucfirst($question['difficulty']); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <?php if ($question['field_name']): ?>
                                            <span class="badge bg-primary" style="background: <?php echo $question['field_color'] ?? '#6C63FF'; ?>">
                                                <?php echo htmlspecialchars($question['field_name']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($question['category']): ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($question['category']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?php echo getAvatar($question); ?>" class="rounded-circle me-2" style="width: 24px; height: 24px;">
                                        <small class="text-muted">by <?php echo htmlspecialchars($question['mentor_name']); ?></small>
                                    </div>
                                    
                                    <div class="question-actions mt-2">
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="toggleAnswer(<?php echo $question['id']; ?>)">
                                            <i class="fas fa-eye"></i> Show Answer
                                        </button>
                                        <a href="?save=1&id=<?php echo $question['id']; ?>&<?php echo http_build_query(['field' => $field_id, 'difficulty' => $difficulty, 'category' => $category, 'search' => $search]); ?>" 
                                           class="btn btn-sm <?php echo $is_saved ? 'btn-warning' : 'btn-outline-secondary'; ?>">
                                            <i class="fas fa-<?php echo $is_saved ? 'bookmark' : 'bookmark'; ?>"></i>
                                            <?php echo $is_saved ? 'Saved' : 'Save'; ?>
                                        </a>
                                    </div>
                                    
                                    <div id="answer_<?php echo $question['id']; ?>" class="mt-3 d-none">
                                        <div class="alert alert-info">
                                            <strong>Answer:</strong>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($question['answer'] ?? 'No answer provided.')); ?></p>
                                        </div>
                                        <button class="btn btn-sm btn-success" onclick="practiceQuestion(<?php echo $question['id']; ?>)">
                                            <i class="fas fa-microphone"></i> Practice Answer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <h5>No questions found</h5>
                    <p class="text-muted">Try adjusting your filters or check back later for new questions.</p>
                    <a href="index.php" class="btn btn-primary">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleAnswer(id) {
    const el = document.getElementById('answer_' + id);
    el.classList.toggle('d-none');
    if (!el.classList.contains('d-none')) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function practiceQuestion(id) {
    window.location.href = 'practice.php?question=' + id;
}
</script>

<?php include '../../includes/footer.php'; ?>