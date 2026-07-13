<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function redirect($path) {
    $target = rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
    header('Location: ' . $target);
    exit;
}

function require_login($requiredRole = null) {
    if (!is_logged_in()) {
        redirect('login.php');
    }

    if ($requiredRole && current_user_role() !== $requiredRole) {
        redirect('dashboard.php');
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token() {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

function ensure_database_schema() {
    global $conn;

    if (!$conn) {
        return;
    }

    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin','graduate','fresher') NOT NULL DEFAULT 'fresher',
        status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        created_by INT NOT NULL,
        deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS mentorship_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT NOT NULL,
        student_id INT NOT NULL,
        topic VARCHAR(150) NOT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS assignment_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        assignment_id INT NOT NULL,
        student_id INT NOT NULL,
        notes TEXT,
        file_path VARCHAR(255) DEFAULT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'submitted',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS placements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        company VARCHAR(150) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $email = 'admin@shareskillhub.com';
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $name = 'System Admin';
        $insert = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, 'admin', 'active')");
        $insert->bind_param('sss', $name, $email, $hashedPassword);
        $insert->execute();
        $insert->close();
    }

    $stmt->close();
}

function get_courses() {
    global $conn;
    if (!$conn) {
        return [];
    }

    $result = $conn->query("SELECT id, title, description, created_at FROM courses ORDER BY id DESC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function get_course($id) {
    global $conn;
    if (!$conn) {
        return null;
    }

    $stmt = $conn->prepare("SELECT id, title, description FROM courses WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
    return $course;
}

function create_course($title, $description) {
    global $conn;
    if (!$conn || trim($title) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO courses (title, description) VALUES (?, ?)");
    $stmt->bind_param('ss', $title, $description);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function update_course($id, $title, $description) {
    global $conn;
    if (!$conn || trim($title) === '') {
        return false;
    }

    $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param('ssi', $title, $description, $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function delete_course($id) {
    global $conn;
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_users($role = null) {
    global $conn;
    if (!$conn) {
        return [];
    }

    $sql = "SELECT id, full_name, email, role, status, created_at FROM users";
    $params = [];
    $types = '';

    if ($role) {
        $sql .= " WHERE role = ?";
        $params[] = $role;
        $types = 's';
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

function update_user_status($id, $status) {
    global $conn;
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function create_assignment($title, $description, $created_by, $deadline) {
    global $conn;
    if (!$conn || trim($title) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO assignments (title, description, created_by, deadline) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssis', $title, $description, $created_by, $deadline);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_assignments() {
    global $conn;
    if (!$conn) {
        return [];
    }

    $result = $conn->query("SELECT a.id, a.title, a.description, a.deadline, a.created_by, u.full_name as created_by_name FROM assignments a LEFT JOIN users u ON u.id = a.created_by ORDER BY a.id DESC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function create_mentorship_booking($mentor_id, $student_id, $topic, $status = 'pending') {
    global $conn;
    if (!$conn || !$mentor_id || !$student_id || trim($topic) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO mentorship_sessions (mentor_id, student_id, topic, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $mentor_id, $student_id, $topic, $status);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_mentorship_bookings($student_id = null, $mentor_id = null) {
    global $conn;
    if (!$conn) {
        return [];
    }

    $sql = "SELECT ms.id, ms.topic, ms.status, ms.created_at, ms.student_id, ms.mentor_id, student.full_name AS student_name, mentor.full_name AS mentor_name FROM mentorship_sessions ms LEFT JOIN users student ON student.id = ms.student_id LEFT JOIN users mentor ON mentor.id = ms.mentor_id";
    $params = [];
    $types = '';
    $conditions = [];

    if ($student_id !== null) {
        $conditions[] = 'ms.student_id = ?';
        $params[] = $student_id;
        $types .= 'i';
    }

    if ($mentor_id !== null) {
        $conditions[] = 'ms.mentor_id = ?';
        $params[] = $mentor_id;
        $types .= 'i';
    }

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY ms.created_at DESC';
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function update_mentorship_status($id, $status) {
    global $conn;
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare("UPDATE mentorship_sessions SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function create_assignment_submission($assignment_id, $student_id, $notes, $file_path = '') {
    global $conn;
    if (!$conn || !$assignment_id || !$student_id) {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, notes, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $assignment_id, $student_id, $notes, $file_path);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_assignment_submissions($assignment_id = null, $student_id = null) {
    global $conn;
    if (!$conn) {
        return [];
    }

    $sql = "SELECT s.id, s.assignment_id, s.student_id, s.notes, s.file_path, s.status, s.created_at, a.title AS assignment_title, u.full_name AS student_name FROM assignment_submissions s LEFT JOIN assignments a ON a.id = s.assignment_id LEFT JOIN users u ON u.id = s.student_id";
    $params = [];
    $types = '';
    $conditions = [];

    if ($assignment_id !== null) {
        $conditions[] = 's.assignment_id = ?';
        $params[] = $assignment_id;
        $types .= 'i';
    }

    if ($student_id !== null) {
        $conditions[] = 's.student_id = ?';
        $params[] = $student_id;
        $types .= 'i';
    }

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY s.created_at DESC';
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_assignment_submissions_for_graduate($graduate_id) {
    global $conn;
    if (!$conn) {
        return [];
    }

    $stmt = $conn->prepare("SELECT s.id, s.assignment_id, s.student_id, s.notes, s.file_path, s.status, s.created_at, a.title AS assignment_title, u.full_name AS student_name FROM assignment_submissions s LEFT JOIN assignments a ON a.id = s.assignment_id LEFT JOIN users u ON u.id = s.student_id WHERE a.created_by = ? ORDER BY s.created_at DESC");
    $stmt->bind_param('i', $graduate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_dashboard_counts() {
    global $conn;

    $counts = [
        'users' => 0,
        'courses' => 0,
        'assignments' => 0,
        'sessions' => 0,
    ];

    if (!$conn) {
        return $counts;
    }

    $counts['users'] = (int) $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $counts['courses'] = (int) $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];
    $counts['assignments'] = (int) $conn->query("SELECT COUNT(*) FROM assignments")->fetch_row()[0];
    $counts['sessions'] = (int) $conn->query("SELECT COUNT(*) FROM mentorship_sessions")->fetch_row()[0];

    return $counts;
}

function get_user_by_id($id) {
    global $conn;
    if (!$conn) {
        return null;
    }

    $stmt = $conn->prepare("SELECT id, full_name, email, role, status FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function add_notification($user_id, $message) {
    global $conn;
    if (!$conn || !$user_id || trim($message) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $message);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_notifications($user_id) {
    global $conn;
    if (!$conn || !$user_id) {
        return [];
    }

    $stmt = $conn->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY id DESC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function send_message($sender_id, $receiver_id, $message) {
    global $conn;
    if (!$conn || !$sender_id || !$receiver_id || trim($message) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $sender_id, $receiver_id, $message);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function get_messages_between($user_id, $other_id) {
    global $conn;
    if (!$conn || !$user_id || !$other_id) {
        return [];
    }

    $stmt = $conn->prepare("SELECT sender_id, receiver_id, message, created_at FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->bind_param('iiii', $user_id, $other_id, $other_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_placements() {
    global $conn;
    if (!$conn) {
        return [];
    }

    $result = $conn->query("SELECT id, title, company, description, created_at FROM placements ORDER BY id DESC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function create_placement($title, $company, $description) {
    global $conn;
    if (!$conn || trim($title) === '' || trim($company) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO placements (title, company, description) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $title, $company, $description);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
?>