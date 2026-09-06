<?php
$page_title = 'All Interview Questions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$field_id = isset($_GET['field']) ? (int)$_GET['field'] : 0;
$difficulty = isset($_GET['difficulty']) ? sanitize($_GET['difficulty']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT q.*, u.full_name as mentor_name, f.name as field_name 
          FROM interview_questions q 
          JOIN users u ON q.mentor_id = u.id 
          LEFT JOIN academic_fields f ON q.field_id = f.id 
          WHERE q.is_published = 1";
$count_query = "SELECT COUNT(*) FROM interview_questions q WHERE q.is_published = 1";
$params = [];

if ($field_id) {
    $query .= " AND q.field_id = ?";
    $count_query .= " AND q.field_id = ?";
    $params[] = $field_id;
}

if ($difficulty) {
    $query .= " AND q.difficulty = ?";
    $count_query .= " AND q.difficulty = ?";
    $params[] = $difficulty;
}

if ($search) {
    $query .= " AND (q.question LIKE ? OR q.answer LIKE ? OR q.category LIKE ?)";
    $count_query .= " AND (q.question LIKE ? OR q.answer LIKE ? OR q.category LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY q.created_at DESC LIMIT ? OFFSET ?";
$count_params = $params;

// Get total count
$stmt = $pdo->prepare($count_query);
$stmt->execute($count_params);
$total_questions = $stmt->fetchColumn();
$total_pages = ceil($total_questions / $per_page);

// Get questions
$params[] = $per_page;
$params[] = $offset;
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
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">All Interview Questions</h1>
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
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search questions..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label class="form-label">Difficulty</label>
                            <select name="difficulty" class="form-select">
                                <option value="">All</option>
                                <option value="easy" <?php echo $difficulty === 'easy' ? 'selected' : ''; ?>>Easy</option>
                                <option value="medium" <?php echo $difficulty === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="hard" <?php echo $difficulty === 'hard' ? 'selected' : ''; ?>>Hard</option>
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
            
            <!-- Results -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if (!empty($questions)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Category</th>
                                        <th>Field</th>
                                        <th>Difficulty</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($questions as $index => $question): ?>
                                    <tr>
                                        <td><?php echo $offset + $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars(substr($question['question'], 0, 60)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($question['category'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($question['field_name'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $question['difficulty'] === 'easy' ? 'success' : ($question['difficulty'] === 'medium' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($question['difficulty']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="practice.php?question=<?php echo $question['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-play"></i> Practice
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleAnswer(<?php echo $question['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="answer_row_<?php echo $question['id']; ?>" class="d-none">
                                        <td colspan="6">
                                            <div class="alert alert-info mb-0">
                                                <strong>Answer:</strong>
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($question['answer'] ?? 'No answer provided.')); ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="p-3">
                            <nav>
                                <ul class="pagination justify-content-center mb-0">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query(['field' => $field_id, 'difficulty' => $difficulty, 'search' => $search]); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&<?php echo http_build_query(['field' => $field_id, 'difficulty' => $difficulty, 'search' => $search]); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query(['field' => $field_id, 'difficulty' => $difficulty, 'search' => $search]); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
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

<script>
function toggleAnswer(id) {
    const row = document.getElementById('answer_row_' + id);
    row.classList.toggle('d-none');
}
</script>

<?php include '../../includes/footer.php'; ?>