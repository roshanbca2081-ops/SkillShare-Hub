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

function json_response($status, $message, $data = [], $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

function get_json_input() {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function generate_token($bytes = 32) {
    return bin2hex(random_bytes($bytes));
}

function get_request_ip() {
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? null);
}


function ensure_database_schema() {
    global $conn;

    if (!$conn) {
        return;
    }

    // Core users table (kept for compatibility with existing login/register pages)
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin','graduate','fresher') NOT NULL DEFAULT 'fresher',
        is_verified TINYINT(1) NOT NULL DEFAULT 0,
        email_verified_at TIMESTAMP NULL DEFAULT NULL,
        status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login_at TIMESTAMP NULL DEFAULT NULL,
        last_login_ip VARCHAR(45) DEFAULT NULL
    )");

    // Phase 1 required supporting tables

    // Phase 2 profile tables

    $conn->query("CREATE TABLE IF NOT EXISTS user_profiles (
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id),
        CONSTRAINT fk_user_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS fresher_profiles (
        user_id INT NOT NULL,
        academic_field VARCHAR(150) DEFAULT NULL,
        course VARCHAR(150) DEFAULT NULL,
        semester VARCHAR(50) DEFAULT NULL,
        skills TEXT,
        resume_url VARCHAR(255) DEFAULT NULL,
        portfolio_url VARCHAR(255) DEFAULT NULL,
        interests TEXT,
        personal_details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id),
        CONSTRAINT fk_fresher_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS graduate_profiles (
        user_id INT NOT NULL,
        experience TEXT,
        company VARCHAR(150) DEFAULT NULL,
        skills TEXT,
        biography TEXT,
        hourly_rate DECIMAL(10,2) DEFAULT NULL,
        available_time VARCHAR(100) DEFAULT NULL,
        rating DECIMAL(3,2) DEFAULT NULL,
        certificates TEXT,
        languages TEXT,
        portfolio_url VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id),
        CONSTRAINT fk_graduate_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");


    $conn->query("CREATE TABLE IF NOT EXISTS user_roles (
        user_id INT NOT NULL,
        role ENUM('admin','graduate','fresher') NOT NULL DEFAULT 'fresher',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id),
        CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS login_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        login_ip VARCHAR(45) DEFAULT NULL,
        user_agent TEXT,
        is_success TINYINT(1) NOT NULL DEFAULT 1,
        reason VARCHAR(255) DEFAULT NULL,
        CONSTRAINT fk_login_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL,
        token VARCHAR(100) NOT NULL UNIQUE,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_password_resets_email_user FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Remember-me tokens
    $conn->query("CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        remember_token VARCHAR(255) NOT NULL UNIQUE,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_seen_at TIMESTAMP NULL DEFAULT NULL,
        ip_address VARCHAR(45) DEFAULT NULL,
        user_agent TEXT,
        CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Backfill roles for existing users (safe, idempotent)
    $conn->query("INSERT IGNORE INTO user_roles (user_id, role)
                 SELECT id, role FROM users");

    // For existing installations: if admin user exists, mark verified so API login can work
    // (If you later add real email verification, you can remove this backfill.)
    // Backfill for older installs: email_verified_at may not exist yet.
    $conn->query("UPDATE users SET is_verified=1 WHERE role='admin'");


    // Continue creating the rest of the existing schema

    // Enhanced courses table with all required columns
    $conn->query("CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        slug VARCHAR(150) NOT NULL UNIQUE,
        description TEXT,
        image_path VARCHAR(255) DEFAULT NULL,
        duration VARCHAR(80) DEFAULT NULL,
        difficulty_level VARCHAR(50) DEFAULT 'Beginner',
        rating DECIMAL(3,2) DEFAULT 0.00,
        enrolled_students INT DEFAULT 0,
        mentor_name VARCHAR(150) DEFAULT NULL,
        mentor_avatar VARCHAR(255) DEFAULT NULL,
        field_id INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_courses_field FOREIGN KEY (field_id) REFERENCES academic_fields(id) ON DELETE SET NULL
    )");

    // Migrate existing courses table columns
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'slug'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD slug VARCHAR(150) NOT NULL DEFAULT '' AFTER title");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'image_path'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD image_path VARCHAR(255) DEFAULT NULL AFTER description");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'duration'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD duration VARCHAR(80) DEFAULT NULL");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'difficulty_level'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD difficulty_level VARCHAR(50) DEFAULT 'Beginner'");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'rating'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD rating DECIMAL(3,2) DEFAULT 0.00");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'enrolled_students'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD enrolled_students INT DEFAULT 0");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'mentor_name'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD mentor_name VARCHAR(150) DEFAULT NULL");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'mentor_avatar'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD mentor_avatar VARCHAR(255) DEFAULT NULL");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'field_id'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD field_id INT DEFAULT NULL");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM courses LIKE 'updated_at'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE courses ADD updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
    }

    // Phase 3: Fields, practical skills, and linking tables
    $conn->query("CREATE TABLE IF NOT EXISTS academic_fields (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        slug VARCHAR(150) NOT NULL UNIQUE,
        icon VARCHAR(120) DEFAULT NULL,
        description TEXT,
        logo_path VARCHAR(255) DEFAULT NULL,
        course_count INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    )");

    // Migrate existing academic_fields table
    $colCheck = $conn->query("SHOW COLUMNS FROM academic_fields LIKE 'slug'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE academic_fields ADD slug VARCHAR(150) NOT NULL DEFAULT '' AFTER name");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM academic_fields LIKE 'description'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE academic_fields ADD description TEXT AFTER icon");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM academic_fields LIKE 'logo_path'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE academic_fields ADD logo_path VARCHAR(255) DEFAULT NULL AFTER description");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM academic_fields LIKE 'course_count'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE academic_fields ADD course_count INT NOT NULL DEFAULT 0");
    }
    $colCheck = $conn->query("SHOW COLUMNS FROM academic_fields LIKE 'updated_at'");
    if ($colCheck && $colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE academic_fields ADD updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS course_fields (
        course_id INT NOT NULL,
        field_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (course_id, field_id),
        CONSTRAINT fk_course_fields_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        CONSTRAINT fk_course_fields_field FOREIGN KEY (field_id) REFERENCES academic_fields(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS practical_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS mentor_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT NOT NULL,
        practical_skill_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_mentor_skills_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_mentor_skills_skill FOREIGN KEY (practical_skill_id) REFERENCES practical_skills(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS mentor_availability (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT NOT NULL,
        available_time VARCHAR(120) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_mentor_availability_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // assignments
    $conn->query("CREATE TABLE IF NOT EXISTS assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT,
        created_by INT NOT NULL,
        deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS assignment_courses (
        assignment_id INT NOT NULL,
        course_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (assignment_id, course_id),
        CONSTRAINT fk_assignment_courses_assignment FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
        CONSTRAINT fk_assignment_courses_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    $conn->query("CREATE TABLE IF NOT EXISTS assignment_fields (
        assignment_id INT NOT NULL,
        field_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (assignment_id, field_id),
        CONSTRAINT fk_assignment_fields_assignment FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
        CONSTRAINT fk_assignment_fields_field FOREIGN KEY (field_id) REFERENCES academic_fields(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Create mentors table
    $conn->query("CREATE TABLE IF NOT EXISTS mentors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(150) DEFAULT NULL,
        bio TEXT,
        avatar VARCHAR(255) DEFAULT NULL,
        specialization VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create course_mentors pivot table
    $conn->query("CREATE TABLE IF NOT EXISTS course_mentors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        mentor_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_cm_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        CONSTRAINT fk_cm_mentor FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE CASCADE
    )");

    // Create bookings table
    $conn->query("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        mentor_id INT DEFAULT NULL,
        course_id INT DEFAULT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_bookings_mentor FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE SET NULL,
        CONSTRAINT fk_bookings_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
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

    $conn->query("CREATE TABLE IF NOT EXISTS enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT DEFAULT NULL,
        full_name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(40) DEFAULT NULL,
        address VARCHAR(190) DEFAULT NULL,
        course VARCHAR(150) NOT NULL,
        mentor VARCHAR(150) DEFAULT NULL,
        preferred_duration VARCHAR(80) DEFAULT NULL,
        preferred_time VARCHAR(80) DEFAULT NULL,
        session_type VARCHAR(40) DEFAULT NULL,
        payment_method VARCHAR(40) DEFAULT NULL,
        remarks TEXT,
        amount DECIMAL(10,2) DEFAULT 800.00,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
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

/**
 * Enhanced get_courses() - returns courses with field info, mentor details, etc.
 */
function get_courses() {
    global $conn;
    if (!$conn) {
        return [];
    }

    $result = $conn->query("
        SELECT c.id, c.title, c.slug, c.description, c.image_path, c.duration,
               c.difficulty_level, c.rating, c.enrolled_students, c.mentor_name,
               c.mentor_avatar, c.field_id, c.created_at,
               af.name AS field_name, af.slug AS field_slug, af.icon AS field_icon
        FROM courses c
        LEFT JOIN academic_fields af ON af.id = c.field_id
        ORDER BY c.id DESC
    ");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Enhanced get_fields() - returns fields with course counts
 */
function get_fields() {
    global $conn;
    if (!$conn) {
        return [];
    }

    // If academic_fields table exists, use it. Otherwise return empty.
    $result = $conn->query("SHOW TABLES LIKE 'academic_fields'");
    if (!$result || $result->num_rows === 0) {
        return [];
    }

    // Check if slug column exists
    $slugCheck = $conn->query("SHOW COLUMNS FROM academic_fields LIKE 'slug'");
    $hasSlug = $slugCheck && $slugCheck->num_rows > 0;

    $sql = "SELECT af.id, af.name" . ($hasSlug ? ", af.slug" : ", '' AS slug") . ", af.icon, af.description, af.logo_path, af.course_count, af.created_at,
                   (SELECT COUNT(*) FROM courses c WHERE c.field_id = af.id) AS actual_course_count
            FROM academic_fields af
            ORDER BY af.id ASC";

    $rows = $conn->query($sql);
    if (!$rows) {
        return [];
    }

    $fields = $rows->fetch_all(MYSQLI_ASSOC);

    // Use actual course count if course_count column is 0
    foreach ($fields as &$field) {
        if ((int)$field['course_count'] === 0 && (int)$field['actual_course_count'] > 0) {
            $field['course_count'] = $field['actual_course_count'];
        }
        // Auto-generate slug if empty
        if (empty($field['slug'])) {
            $field['slug'] = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $field['name'])));
        }
    }

    return $fields;
}

/**
 * Get field by slug
 */
function get_field_by_slug($slug) {
    global $conn;
    if (!$conn || !$slug) {
        return null;
    }

    $stmt = $conn->prepare("SELECT af.*, (SELECT COUNT(*) FROM courses c WHERE c.field_id = af.id) AS actual_course_count
                           FROM academic_fields af WHERE af.slug = ? LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $field = $result->fetch_assoc();
    $stmt->close();
    return $field;
}

/**
 * Get field by ID
 */
function get_field_by_id($id) {
    global $conn;
    if (!$conn || !$id) {
        return null;
    }

    $stmt = $conn->prepare("SELECT af.* FROM academic_fields af WHERE af.id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $field = $result->fetch_assoc();
    $stmt->close();
    return $field;
}

/**
 * Get courses by field ID
 */
function get_courses_by_field($field_id) {
    global $conn;
    if (!$conn || !$field_id) {
        return [];
    }

    $stmt = $conn->prepare("
        SELECT c.*, af.name AS field_name, af.slug AS field_slug, af.icon AS field_icon
        FROM courses c
        LEFT JOIN academic_fields af ON af.id = c.field_id
        WHERE c.field_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->bind_param('i', $field_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $courses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $courses;
}

/**
 * Upload an image and return the path
 */
function upload_image($file, $target_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0775, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];

    if (!in_array($ext, $allowed)) {
        return null;
    }

    $filename = uniqid('field_') . '.' . $ext;
    $dest_path = $target_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
        return $filename;
    }

    return null;
}

/**
 * Create a field (admin)
 */
function create_field($name, $slug, $icon, $description, $logo_path = null) {
    global $conn;
    if (!$conn || trim($name) === '' || trim($slug) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO academic_fields (name, slug, icon, description, logo_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $name, $slug, $icon, $description, $logo_path);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Update a field (admin)
 */
function update_field($id, $name, $slug, $icon, $description, $logo_path = null) {
    global $conn;
    if (!$conn || !$id || trim($name) === '') {
        return false;
    }

    if ($logo_path !== null) {
        $stmt = $conn->prepare("UPDATE academic_fields SET name = ?, slug = ?, icon = ?, description = ?, logo_path = ? WHERE id = ?");
        $stmt->bind_param('sssssi', $name, $slug, $icon, $description, $logo_path, $id);
    } else {
        $stmt = $conn->prepare("UPDATE academic_fields SET name = ?, slug = ?, icon = ?, description = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $name, $slug, $icon, $description, $id);
    }
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Delete a field (admin)
 */
function delete_field($id) {
    global $conn;
    if (!$conn || !$id) {
        return false;
    }

    // Unset field_id for courses in this field
    $conn->query("UPDATE courses SET field_id = NULL WHERE field_id = $id");

    $stmt = $conn->prepare("DELETE FROM academic_fields WHERE id = ?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Enhanced create_course with all fields
 */
function create_course_full($title, $slug, $description, $image_path, $duration, $difficulty_level, $rating, $enrolled_students, $mentor_name, $mentor_avatar, $field_id) {
    global $conn;
    if (!$conn || trim($title) === '' || trim($slug) === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO courses (title, slug, description, image_path, duration, difficulty_level, rating, enrolled_students, mentor_name, mentor_avatar, field_id)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssdssi', $title, $slug, $description, $image_path, $duration, $difficulty_level, $rating, $enrolled_students, $mentor_name, $mentor_avatar, $field_id);
    $ok = $stmt->execute();
    $stmt->close();

    // Update field course count
    if ($ok && $field_id) {
        $conn->query("UPDATE academic_fields SET course_count = (SELECT COUNT(*) FROM courses WHERE field_id = $field_id) WHERE id = $field_id");
    }

    return $ok;
}

/**
 * Enhanced update_course with all fields
 */
function update_course_full($id, $title, $slug, $description, $image_path, $duration, $difficulty_level, $rating, $enrolled_students, $mentor_name, $mentor_avatar, $field_id) {
    global $conn;
    if (!$conn || !$id || trim($title) === '') {
        return false;
    }

    // Get old field_id for count update
    $oldFieldId = null;
    $stmt = $conn->prepare("SELECT field_id FROM courses WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($oldFieldId);
    $stmt->fetch();
    $stmt->close();

    if ($image_path !== null) {
        $stmt = $conn->prepare("UPDATE courses SET title = ?, slug = ?, description = ?, image_path = ?, duration = ?, difficulty_level = ?, rating = ?, enrolled_students = ?, mentor_name = ?, mentor_avatar = ?, field_id = ? WHERE id = ?");
        $stmt->bind_param('sssssssdssii', $title, $slug, $description, $image_path, $duration, $difficulty_level, $rating, $enrolled_students, $mentor_name, $mentor_avatar, $field_id, $id);
    } else {
        $stmt = $conn->prepare("UPDATE courses SET title = ?, slug = ?, description = ?, duration = ?, difficulty_level = ?, rating = ?, enrolled_students = ?, mentor_name = ?, mentor_avatar = ?, field_id = ? WHERE id = ?");
        $stmt->bind_param('sssssssdssi', $title, $slug, $description, $duration, $difficulty_level, $rating, $enrolled_students, $mentor_name, $mentor_avatar, $field_id, $id);
    }
    $ok = $stmt->execute();
    $stmt->close();

    // Update course counts for old and new fields
    if ($ok) {
        if ($oldFieldId) {
            $conn->query("UPDATE academic_fields SET course_count = (SELECT COUNT(*) FROM courses WHERE field_id = $oldFieldId) WHERE id = $oldFieldId");
        }
        if ($field_id && $field_id != $oldFieldId) {
            $conn->query("UPDATE academic_fields SET course_count = (SELECT COUNT(*) FROM courses WHERE field_id = $field_id) WHERE id = $field_id");
        }
    }

    return $ok;
}

/**
 * Override original create_course to use enhanced version
 */
function create_course($title, $description) {
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $title)));
    return create_course_full($title, $slug, $description, null, null, 'Beginner', 0, 0, null, null, null);
}

/**
 * Override original update_course to use enhanced version
 */
function update_course($id, $title, $description) {
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $title)));
    return update_course_full($id, $title, $slug, $description, null, null, 'Beginner', 0, 0, null, null, null);
}

/**
 * Get enhanced course by ID
 */
function get_course($id) {
    global $conn;
    if (!$conn) {
        return null;
    }

    $stmt = $conn->prepare("SELECT c.*, af.name AS field_name, af.slug AS field_slug, af.icon AS field_icon
                           FROM courses c
                           LEFT JOIN academic_fields af ON af.id = c.field_id
                           WHERE c.id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
    return $course;
}

/**
 * Get course by slug
 */
function get_course_by_slug($slug) {
    global $conn;
    if (!$conn || !$slug) {
        return null;
    }

    $stmt = $conn->prepare("SELECT c.*, af.name AS field_name, af.slug AS field_slug, af.icon AS field_icon
                           FROM courses c
                           LEFT JOIN academic_fields af ON af.id = c.field_id
                           WHERE c.slug = ? LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
    return $course;
}

function create_enrollment($data) {
    global $conn;
    if (!$conn || trim($data['full_name'] ?? '') === '' || trim($data['email'] ?? '') === '' || trim($data['course'] ?? '') === '') {
        return false;
    }

    $studentId = $data['student_id'] ?? null;
    $fullName = $data['full_name'];
    $email = $data['email'];
    $phone = $data['phone'] ?? '';
    $address = $data['address'] ?? '';
    $course = $data['course'];
    $mentor = $data['mentor'] ?? '';
    $duration = $data['preferred_duration'] ?? '';
    $time = $data['preferred_time'] ?? '';
    $sessionType = $data['session_type'] ?? '';
    $payment = $data['payment_method'] ?? '';
    $remarks = $data['remarks'] ?? '';
    $amount = (float)($data['amount'] ?? 800);

    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, full_name, email, phone, address, course, mentor, preferred_duration, preferred_time, session_type, payment_method, remarks, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssssssssssd', $studentId, $fullName, $email, $phone, $address, $course, $mentor, $duration, $time, $sessionType, $payment, $remarks, $amount);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function delete_course($id) {
    global $conn;
    if (!$conn) {
        return false;
    }

    // Get field_id before deleting
    $fieldId = null;
    $stmt = $conn->prepare("SELECT field_id FROM courses WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($fieldId);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();

    // Update field course count
    if ($ok && $fieldId) {
        $conn->query("UPDATE academic_fields SET course_count = (SELECT COUNT(*) FROM courses WHERE field_id = $fieldId) WHERE id = $fieldId");
    }

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

function get_assignments_by_course($courseId) {
    global $conn;
    if (!$conn || !$courseId) return [];

    $stmt = $conn->prepare("SELECT a.id, a.title, a.description, a.deadline, a.created_by, u.full_name as created_by_name
                            FROM assignment_courses ac
                            INNER JOIN assignments a ON a.id = ac.assignment_id
                            LEFT JOIN users u ON u.id = a.created_by
                            WHERE ac.course_id = ?
                            ORDER BY a.id DESC");
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_assignments_by_field($fieldId) {
    global $conn;
    if (!$conn || !$fieldId) return [];

    $stmt = $conn->prepare("SELECT a.id, a.title, a.description, a.deadline, a.created_by, u.full_name as created_by_name
                            FROM assignment_fields af
                            INNER JOIN assignments a ON a.id = af.assignment_id
                            LEFT JOIN users u ON u.id = a.created_by
                            WHERE af.field_id = ?
                            ORDER BY a.id DESC");
    $stmt->bind_param('i', $fieldId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_practical_skills() {
    global $conn;
    if (!$conn) {
        return [];
    }

    $result = $conn->query("SHOW TABLES LIKE 'practical_skills'");
    if (!$result || $result->num_rows === 0) {
        return [];
    }

    $rows = $conn->query("SELECT id, title, description, created_at FROM practical_skills ORDER BY id DESC");
    return $rows ? $rows->fetch_all(MYSQLI_ASSOC) : [];
}

function get_mentors_by_practical_skill($skillId) {
    global $conn;
    if (!$conn || !$skillId) return [];

    $stmt = $conn->prepare("SELECT DISTINCT u.id, u.full_name, u.email
                            FROM mentor_skills ms
                            INNER JOIN users u ON u.id = ms.mentor_id
                            WHERE ms.practical_skill_id = ? AND u.role = 'graduate'
                            ORDER BY u.id DESC");
    $stmt->bind_param('i', $skillId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
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

    $hasTopicColumn = false;
    $hasStudentIdColumn = false;
    $hasMentorIdColumn = false;

    $topicCheck = $conn->query("SHOW COLUMNS FROM mentorship_sessions LIKE 'topic'");
    if ($topicCheck && $topicCheck->num_rows > 0) {
        $hasTopicColumn = true;
    }

    $studentIdCheck = $conn->query("SHOW COLUMNS FROM mentorship_sessions LIKE 'student_id'");
    if ($studentIdCheck && $studentIdCheck->num_rows > 0) {
        $hasStudentIdColumn = true;
    }

    $mentorIdCheck = $conn->query("SHOW COLUMNS FROM mentorship_sessions LIKE 'mentor_id'");
    if ($mentorIdCheck && $mentorIdCheck->num_rows > 0) {
        $hasMentorIdColumn = true;
    }

    $sql = "SELECT ms.id";
    if ($hasTopicColumn) {
        $sql .= ", ms.topic";
    } else {
        $sql .= ", '' AS topic";
    }
    $sql .= ", ms.status, ms.created_at";
    if ($hasStudentIdColumn) {
        $sql .= ", ms.student_id";
    }
    if ($hasMentorIdColumn) {
        $sql .= ", ms.mentor_id";
    }
    $sql .= ", student.full_name AS student_name, mentor.full_name AS mentor_name FROM mentorship_sessions ms LEFT JOIN users student ON student.id = ms.student_id LEFT JOIN users mentor ON mentor.id = ms.mentor_id";
    $params = [];
    $types = '';
    $conditions = [];

    if ($student_id !== null && $hasStudentIdColumn) {
        $conditions[] = 'ms.student_id = ?';
        $params[] = $student_id;
        $types .= 'i';
    }

    if ($mentor_id !== null && $hasMentorIdColumn) {
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
        'fields' => 0,
        'mentors' => 0,
        'bookings' => 0,
    ];

    if (!$conn) {
        return $counts;
    }

    $counts['users'] = (int) $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $counts['courses'] = (int) $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];
    $counts['assignments'] = (int) $conn->query("SELECT COUNT(*) FROM assignments")->fetch_row()[0];
    $counts['sessions'] = (int) $conn->query("SELECT COUNT(*) FROM mentorship_sessions")->fetch_row()[0];

    // New counts
    $result = $conn->query("SHOW TABLES LIKE 'academic_fields'");
    if ($result && $result->num_rows > 0) {
        $counts['fields'] = (int) $conn->query("SELECT COUNT(*) FROM academic_fields")->fetch_row()[0];
    }
    $result = $conn->query("SHOW TABLES LIKE 'mentors'");
    if ($result && $result->num_rows > 0) {
        $counts['mentors'] = (int) $conn->query("SELECT COUNT(*) FROM mentors")->fetch_row()[0];
    }
    $result = $conn->query("SHOW TABLES LIKE 'bookings'");
    if ($result && $result->num_rows > 0) {
        $counts['bookings'] = (int) $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
    }

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

/**
 * Get all available field icons mapping
 */
function get_field_icon_map() {
    return [
        'information-technology' => 'fa-laptop-code',
        'engineering' => 'fa-gears',
        'science' => 'fa-flask',
        'management' => 'fa-briefcase',
        'agriculture' => 'fa-seedling',
        'health-sciences' => 'fa-heart-pulse',
        'education' => 'fa-graduation-cap',
        'law' => 'fa-scale-balanced',
        'arts' => 'fa-palette',
        'media' => 'fa-camera',
        'hospitality' => 'fa-hotel',
        'tourism' => 'fa-plane',
        'arts-humanities' => 'fa-palette',
        'media-communication' => 'fa-camera',
        'hospitality-tourism' => 'fa-hotel',
        'research-innovation' => 'fa-lightbulb',
        'business-finance' => 'fa-chart-line',
        'architecture' => 'fa-building',
        'environmental-science' => 'fa-earth-americas',
        'mathematics' => 'fa-calculator',
        'social-science' => 'fa-globe',
        'pharmacy' => 'fa-capsules',
        'nursing' => 'fa-user-nurse',
        'veterinary' => 'fa-paw',
        'civil-engineering' => 'fa-road',
        'mechanical-engineering' => 'fa-cogs',
        'electrical-engineering' => 'fa-bolt',
        'computer-engineering' => 'fa-microchip',
        'aerospace-engineering' => 'fa-rocket',
        'fashion-design' => 'fa-shirt',
        'music' => 'fa-music',
        'psychology' => 'fa-brain',
        'economics' => 'fa-chart-bar',
        'journalism' => 'fa-newspaper',
        'digital-marketing' => 'fa-bullhorn',
        'biotechnology' => 'fa-dna',
        'data-science' => 'fa-robot',
        'sports-science' => 'fa-running',
        'construction-management' => 'fa-hard-hat',
    ];
}

/**
 * Get icon for a field slug
 */
function get_field_icon($slug) {
    $map = get_field_icon_map();
    return $map[$slug] ?? 'fa-graduation-cap';
}

/**
 * Create a booking record
 */
function create_booking($user_id, $mentor_id = null, $course_id = null, $status = 'pending') {
    global $conn;
    if (!$conn || !$user_id) {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, mentor_id, course_id, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiis', $user_id, $mentor_id, $course_id, $status);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

/**
 * Get bookings for a user
 */
function get_user_bookings($user_id) {
    global $conn;
    if (!$conn || !$user_id) {
        return [];
    }

    $stmt = $conn->prepare("SELECT b.*, m.name AS mentor_name, c.title AS course_title
                           FROM bookings b
                           LEFT JOIN mentors m ON m.id = b.mentor_id
                           LEFT JOIN courses c ON c.id = b.course_id
                           WHERE b.user_id = ?
                           ORDER BY b.created_at DESC");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
?>

