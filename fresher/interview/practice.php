<?php
$page_title = 'Practice Mode';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$question_id = isset($_GET['question']) ? (int)$_GET['question'] : 0;
$mode = isset($_GET['mode']) ? sanitize($_GET['mode']) : 'random';

// Get questions for practice
if ($question_id) {
    // Practice a specific question
    $stmt = $pdo->prepare("SELECT q.*, u.full_name as mentor_name, f.name as field_name 
                           FROM interview_questions q 
                           JOIN users u ON q.mentor_id = u.id 
                           LEFT JOIN academic_fields f ON q.field_id = f.id 
                           WHERE q.id = ? AND q.is_published = 1");
    $stmt->execute([$question_id]);
    $questions = [$stmt->fetch()];
    $current_index = 0;
} else {
    // Get random questions based on mode
    $query = "SELECT q.*, u.full_name as mentor_name, f.name as field_name 
              FROM interview_questions q 
              JOIN users u ON q.mentor_id = u.id 
              LEFT JOIN academic_fields f ON q.field_id = f.id 
              WHERE q.is_published = 1";
    $params = [];
    
    if ($mode === 'easy') {
        $query .= " AND q.difficulty = 'easy'";
    } elseif ($mode === 'medium') {
        $query .= " AND q.difficulty = 'medium'";
    } elseif ($mode === 'hard') {
        $query .= " AND q.difficulty = 'hard'";
    } elseif ($mode === 'saved') {
        $query .= " AND q.id IN (SELECT question_id FROM interview_saved WHERE fresher_id = ?)";
        $params[] = $user_id;
    }
    
    $query .= " ORDER BY RAND() LIMIT 20";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $questions = $stmt->fetchAll();
    $current_index = 0;
}

if (empty($questions)) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'No questions available for practice. Try a different mode.'
    ];
    redirect('index.php');
}

// Get total questions
$total_questions = count($questions);

// Get current question
$current_question = $questions[$current_index];

// Handle next/previous
if (isset($_GET['next']) && $current_index < $total_questions - 1) {
    $current_index++;
    $current_question = $questions[$current_index];
} elseif (isset($_GET['prev']) && $current_index > 0) {
    $current_index--;
    $current_question = $questions[$current_index];
}

// Handle showing answer
$show_answer = isset($_GET['show_answer']) ? true : false;

// Save practice session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_practice'])) {
    $question_id = (int)$_POST['question_id'];
    $difficulty = sanitize($_POST['difficulty']);
    $notes = sanitize($_POST['notes'] ?? '');
    
    $stmt = $pdo->prepare("INSERT INTO interview_practice (fresher_id, question_id, difficulty, notes, practiced_at) 
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $question_id, $difficulty, $notes]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Practice session saved successfully!'
    ];
}

// Get practice stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total, 
                       AVG(CASE WHEN difficulty = 'easy' THEN 1 ELSE 0 END) as easy_ratio,
                       AVG(CASE WHEN difficulty = 'medium' THEN 1 ELSE 0 END) as medium_ratio,
                       AVG(CASE WHEN difficulty = 'hard' THEN 1 ELSE 0 END) as hard_ratio
                       FROM interview_practice 
                       WHERE fresher_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();
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
                <h1 class="h2">Practice Mode</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Questions
                    </a>
                </div>
            </div>
            
            <!-- Practice Controls -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <!-- Question Progress -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-primary">Question <?php echo $current_index + 1; ?> of <?php echo $total_questions; ?></span>
                                <span class="badge bg-<?php echo $current_question['difficulty'] === 'easy' ? 'success' : ($current_question['difficulty'] === 'medium' ? 'warning' : 'danger'); ?>">
                                    <?php echo ucfirst($current_question['difficulty']); ?>
                                </span>
                            </div>
                            
                            <div class="progress mb-4" style="height: 4px;">
                                <div class="progress-bar" style="width: <?php echo (($current_index + 1) / $total_questions) * 100; ?>%"></div>
                            </div>
                            
                            <!-- Question -->
                            <div class="question-container">
                                <h4 class="mb-3"><?php echo htmlspecialchars($current_question['question']); ?></h4>
                                
                                <?php if ($current_question['field_name']): ?>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($current_question['field_name']); ?></span>
                                <?php endif; ?>
                                <?php if ($current_question['category']): ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($current_question['category']); ?></span>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <small class="text-muted">Mentor: <?php echo htmlspecialchars($current_question['mentor_name']); ?></small>
                                </div>
                                
                                <!-- Answer Section -->
                                <div class="answer-section mt-4">
                                    <?php if ($show_answer): ?>
                                        <div class="alert alert-info">
                                            <strong>Answer:</strong>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($current_question['answer'] ?? 'No answer provided.')); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <button class="btn btn-primary" onclick="showAnswer()">
                                            <i class="fas fa-eye"></i> Show Answer
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="d-flex justify-content-between mt-3">
                        <?php if ($current_index > 0): ?>
                            <a href="practice.php?question=<?php echo $current_question['id']; ?>&prev=1&show_answer=<?php echo $show_answer ? 1 : 0; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Previous
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>
                        
                        <?php if ($current_index < $total_questions - 1): ?>
                            <a href="practice.php?question=<?php echo $current_question['id']; ?>&next=1&show_answer=<?php echo $show_answer ? 1 : 0; ?>" class="btn btn-primary">
                                Next <i class="fas fa-arrow-right"></i>
                            </a>
                        <?php else: ?>
                            <a href="index.php" class="btn btn-success">
                                <i class="fas fa-check"></i> Complete Practice
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar - Practice Tools -->
                <div class="col-lg-4">
                    <!-- Quick Stats -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6>Practice Stats</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4><?php echo $stats['total'] ?? 0; ?></h4>
                                    <small class="text-muted">Total</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success"><?php echo round(($stats['easy_ratio'] ?? 0) * 100); ?>%</h4>
                                    <small class="text-muted">Easy</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-danger"><?php echo round(($stats['hard_ratio'] ?? 0) * 100); ?>%</h4>
                                    <small class="text-muted">Hard</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Practice Notes -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6>Practice Notes</h6>
                            <form method="POST">
                                <input type="hidden" name="question_id" value="<?php echo $current_question['id']; ?>">
                                <input type="hidden" name="difficulty" value="<?php echo $current_question['difficulty']; ?>">
                                <div class="mb-3">
                                    <textarea name="notes" class="form-control" rows="4" 
                                              placeholder="Add notes about this question..."></textarea>
                                </div>
                                <button type="submit" name="save_practice" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Save Notes
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Timer -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <h6>Practice Timer</h6>
                            <div class="display-4" id="timer">00:00</div>
                            <div class="btn-group mt-2">
                                <button class="btn btn-sm btn-primary" onclick="startTimer()">
                                    <i class="fas fa-play"></i> Start
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="pauseTimer()">
                                    <i class="fas fa-pause"></i> Pause
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="resetTimer()">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showAnswer() {
    window.location.href = 'practice.php?question=<?php echo $current_question['id']; ?>&show_answer=1';
}

// Timer functionality
let timerInterval;
let seconds = 0;
let isRunning = false;

function startTimer() {
    if (!isRunning) {
        isRunning = true;
        timerInterval = setInterval(() => {
            seconds++;
            updateTimerDisplay();
        }, 1000);
    }
}

function pauseTimer() {
    isRunning = false;
    clearInterval(timerInterval);
}

function resetTimer() {
    pauseTimer();
    seconds = 0;
    updateTimerDisplay();
}

function updateTimerDisplay() {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    document.getElementById('timer').textContent = 
        `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' && <?php echo $current_index < $total_questions - 1 ? 'true' : 'false'; ?>) {
        window.location.href = 'practice.php?question=<?php echo $current_question['id']; ?>&next=1&show_answer=<?php echo $show_answer ? 1 : 0; ?>';
    }
    if (e.key === 'ArrowLeft' && <?php echo $current_index > 0 ? 'true' : 'false'; ?>) {
        window.location.href = 'practice.php?question=<?php echo $current_question['id']; ?>&prev=1&show_answer=<?php echo $show_answer ? 1 : 0; ?>';
    }
    if (e.key === ' ' || e.key === 'Space') {
        e.preventDefault();
        showAnswer();
    }
});
</script>

<style>
.question-container {
    min-height: 200px;
}
</style>

<?php include '../../includes/footer.php'; ?>