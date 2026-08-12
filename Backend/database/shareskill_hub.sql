-- ============================================================
-- SkillShare Hub - Complete Database Schema
-- Version 1.0.0
-- Engine: InnoDB | Charset: utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- USERS (all roles: admin, mentor, fresher)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','mentor','fresher') NOT NULL DEFAULT 'fresher',
    academic_field INT UNSIGNED DEFAULT NULL,
    course INT UNSIGNED DEFAULT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    bio TEXT,
    address VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    verification_token VARCHAR(255) DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    remember_expiry DATETIME DEFAULT NULL,
    status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    last_ip VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX idx_users_role (role),
    INDEX idx_users_status (status),
    INDEX idx_users_academic_field (academic_field),
    INDEX idx_users_course (course)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- MENTORS (role-specific profile)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS mentors;
CREATE TABLE mentors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    specialization VARCHAR(255) DEFAULT NULL,
    experience_years INT DEFAULT 0,
    mentoring_years INT DEFAULT 0,
    current_company VARCHAR(150) DEFAULT NULL,
    current_position VARCHAR(150) DEFAULT NULL,
    qualification VARCHAR(255) DEFAULT NULL,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    verified_at DATETIME DEFAULT NULL,
    verified_by INT UNSIGNED DEFAULT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    reviews_count INT NOT NULL DEFAULT 0,
    total_sessions INT NOT NULL DEFAULT 0,
    total_students INT NOT NULL DEFAULT 0,
    portfolio_url VARCHAR(255) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    github_url VARCHAR(255) DEFAULT NULL,
    website_url VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mentors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- FRESHERS (role-specific profile)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS freshers;
CREATE TABLE freshers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    education_level VARCHAR(100) DEFAULT NULL,
    institution VARCHAR(200) DEFAULT NULL,
    graduation_year YEAR DEFAULT NULL,
    interests TEXT,
    goals TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_freshers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- ACADEMIC FIELDS (Level 1)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS academic_fields;
CREATE TABLE academic_fields (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT 'fa-layer-group',
    color VARCHAR(20) DEFAULT '#3b82f6',
    description TEXT,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- COURSES (Level 2)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS courses;
CREATE TABLE courses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academic_field_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT 'fa-graduation-cap',
    description TEXT,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_courses_field FOREIGN KEY (academic_field_id) REFERENCES academic_fields(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- COURSE ENROLLMENTS (fresher <-> course)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS course_enrollments;
CREATE TABLE course_enrollments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NOT NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_enrollment (user_id, course_id),
    CONSTRAINT fk_enroll_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_enroll_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SKILLS (Level 3 - belong to a course)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS skills;
CREATE TABLE skills (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_skills_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SKILL LEARNERS (mentor <-> skill taught / fresher <-> skill learned)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS skill_learners;
CREATE TABLE skill_learners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    skill_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    status ENUM('in_progress','completed') NOT NULL DEFAULT 'in_progress',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_skill_learner (skill_id, user_id),
    CONSTRAINT fk_sl_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
    CONSTRAINT fk_sl_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- BOOKINGS (fresher requests a session with a mentor)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS bookings;
CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_number VARCHAR(30) NOT NULL UNIQUE,
    mentor_id INT UNSIGNED NOT NULL,
    fresher_id INT UNSIGNED NOT NULL,
    skill_id INT UNSIGNED DEFAULT NULL,
    session_title VARCHAR(200) NOT NULL DEFAULT 'Mentorship Session',
    session_description TEXT,
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    duration INT NOT NULL DEFAULT 60,
    hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    payment_status ENUM('pending','paid','refunded') NOT NULL DEFAULT 'pending',
    cancellation_reason TEXT,
    cancelled_at DATETIME DEFAULT NULL,
    cancelled_by INT UNSIGNED DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bookings_mentor (mentor_id),
    INDEX idx_bookings_fresher (fresher_id),
    INDEX idx_bookings_status (status),
    CONSTRAINT fk_book_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_book_fresher FOREIGN KEY (fresher_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_book_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SESSIONS (completed/upcoming mentorship sessions)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED DEFAULT NULL,
    mentor_id INT UNSIGNED NOT NULL,
    fresher_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) DEFAULT 'Mentorship Session',
    notes TEXT,
    scheduled_at DATETIME DEFAULT NULL,
    duration INT NOT NULL DEFAULT 60,
    meeting_link VARCHAR(500) DEFAULT NULL,
    status ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sessions_mentor (mentor_id),
    INDEX idx_sessions_fresher (fresher_id),
    CONSTRAINT fk_sess_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_sess_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_sess_fresher FOREIGN KEY (fresher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- ASSIGNMENTS
-- ------------------------------------------------------------
DROP TABLE IF EXISTS assignments;
CREATE TABLE assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mentor_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATE DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_assign_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_assign_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SUBMISSIONS (fresher answers to assignments)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS submissions;
CREATE TABLE submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT UNSIGNED NOT NULL,
    fresher_id INT UNSIGNED NOT NULL,
    content TEXT,
    file_path VARCHAR(255) DEFAULT NULL,
    score DECIMAL(5,2) DEFAULT NULL,
    feedback TEXT,
    status ENUM('submitted','graded','returned') NOT NULL DEFAULT 'submitted',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    graded_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_sub_assign FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    CONSTRAINT fk_sub_fresher FOREIGN KEY (fresher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- RESEARCH (fresher research projects)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS research;
CREATE TABLE research (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(250) NOT NULL,
    abstract TEXT,
    category VARCHAR(100) DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    status ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_research_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- INTERVIEW QUESTIONS
-- ------------------------------------------------------------
DROP TABLE IF EXISTS interview_questions;
CREATE TABLE interview_questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    question TEXT NOT NULL,
    answer TEXT,
    difficulty ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ivq_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- CERTIFICATES
-- ------------------------------------------------------------
DROP TABLE IF EXISTS certificates;
CREATE TABLE certificates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    certificate_number VARCHAR(40) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('issued','revoked') NOT NULL DEFAULT 'issued',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cert_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cert_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- PAYMENTS
-- ------------------------------------------------------------
DROP TABLE IF EXISTS payments;
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED DEFAULT NULL,
    user_id INT UNSIGNED NOT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method VARCHAR(50) DEFAULT NULL,
    status ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
    paid_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pay_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_pay_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- MESSAGES (private user-to-user)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS messages;
CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    body TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_msg_sender (sender_id),
    INDEX idx_msg_receiver (receiver_id),
    CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- NOTIFICATIONS
-- ------------------------------------------------------------
DROP TABLE IF EXISTS notifications;
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    type VARCHAR(50) DEFAULT 'general',
    link VARCHAR(500) DEFAULT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notif_user (user_id),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- REVIEWS (fresher -> mentor)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS reviews;
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reviewer_id INT UNSIGNED NOT NULL,
    reviewee_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED DEFAULT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
    comment TEXT,
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reviews_reviewee (reviewee_id),
    CONSTRAINT fk_rev_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_reviewee FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_rev_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- BOOKMARKS (saved courses/mentors)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS bookmarks;
CREATE TABLE bookmarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    item_type ENUM('course','mentor','field') NOT NULL DEFAULT 'course',
    item_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_bookmark (user_id, item_type, item_id),
    CONSTRAINT fk_bookmark_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- AVAILABILITY (mentor weekly schedule)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS availability;
CREATE TABLE availability (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    day_of_week ENUM('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_availability (user_id, day_of_week),
    CONSTRAINT fk_avail_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- PROGRESS (learning progress per item)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS progress;
CREATE TABLE progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    item_type ENUM('course','skill','assignment','resource') NOT NULL DEFAULT 'course',
    item_id INT UNSIGNED NOT NULL,
    progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0,
    last_accessed DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_progress (user_id, item_type, item_id),
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SETTINGS (key/value)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- ACTIVITY LOGS
-- ------------------------------------------------------------
DROP TABLE IF EXISTS activity_logs;
CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_log_user (user_id),
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- CONTACT MESSAGES (public contact form)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS contact_messages;
CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- DEFAULT DATA
-- ------------------------------------------------------------

-- Default admin user (email: admin@shareskillhub.com / password: admin123)
INSERT INTO users (full_name, email, password_hash, role, is_verified, status, created_at) VALUES
('System Admin', 'admin@shareskillhub.com', '$2y$12$4fG7mYwQnJhFqV7k0hF1eO0qGjRdc4p0mBz1kM3tC8sZ0dWlX1yK', 'admin', 1, 'active', NOW());

-- Default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'ShareSkill Hub'),
('site_tagline', 'Learn. Connect. Grow with expert mentors.'),
('contact_email', 'support@shareskillhub.com'),
('items_per_page', '12'),
('maintenance_mode', '0');

SET FOREIGN_KEY_CHECKS = 1;
