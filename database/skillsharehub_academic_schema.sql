-- ============================================================
-- SkillShare Hub - Academic Field Navigation Tables
-- Flow: academic_fields -> courses -> subjects -> skills -> mentors
-- These are DEDICATED tables so we don't disturb existing
-- courses / mentors tables used by admin & dashboard.
-- ============================================================

-- Drop tables if they exist (for clean re-seed) - order matters (children first)
DROP TABLE IF EXISTS acad_mentor_skills;
DROP TABLE IF EXISTS acad_mentors;
DROP TABLE IF EXISTS acad_skills;
DROP TABLE IF EXISTS acad_subjects;
DROP TABLE IF EXISTS acad_courses;
DROP TABLE IF EXISTS acad_fields;

-- ------------------------------------------------------------
-- ACADEMIC FIELDS (Level 1)
-- ------------------------------------------------------------
CREATE TABLE acad_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT 'fa-layer-group',
    color VARCHAR(20) DEFAULT '#3b82f6',
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- COURSES (Level 2) - belongs to a field
-- ------------------------------------------------------------
CREATE TABLE acad_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT 'fa-graduation-cap',
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_acad_course_field FOREIGN KEY (field_id) REFERENCES acad_fields(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SUBJECTS (Level 3) - belongs to a course
-- ------------------------------------------------------------
CREATE TABLE acad_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_acad_subject_course_slug (course_id, slug),
    CONSTRAINT fk_acad_subject_course FOREIGN KEY (course_id) REFERENCES acad_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- SKILLS (Level 4) - belongs to a subject
-- ------------------------------------------------------------
CREATE TABLE acad_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_acad_skill_subject FOREIGN KEY (subject_id) REFERENCES acad_subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- MENTORS (Level 5) - dedicated sample mentors
-- ------------------------------------------------------------
CREATE TABLE acad_mentors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    title VARCHAR(255),
    company VARCHAR(150),
    avatar VARCHAR(20) DEFAULT 'blue',
    color VARCHAR(20) DEFAULT 'blue',
    rating DECIMAL(3,2) DEFAULT 0.00,
    reviews INT DEFAULT 0,
    students INT DEFAULT 0,
    experience VARCHAR(50),
    price DECIMAL(10,2) DEFAULT 0.00,
    online TINYINT(1) DEFAULT 1,
    verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- MENTOR <-> SKILL pivot (many-to-many)
-- ------------------------------------------------------------
CREATE TABLE acad_mentor_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mentor_id INT NOT NULL,
    skill_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_acad_ms_mentor FOREIGN KEY (mentor_id) REFERENCES acad_mentors(id) ON DELETE CASCADE,
    CONSTRAINT fk_acad_ms_skill FOREIGN KEY (skill_id) REFERENCES acad_skills(id) ON DELETE CASCADE,
    UNIQUE KEY uq_mentor_skill (mentor_id, skill_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
