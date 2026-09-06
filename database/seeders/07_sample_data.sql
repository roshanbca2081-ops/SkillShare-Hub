-- =============================================
-- SkillShare Hub - Seeder
-- 07_sample_data.sql
-- =============================================

-- Additional ratings
INSERT IGNORE INTO `ratings` (`mentor_id`, `fresher_id`, `rating`, `review`, `is_public`, `created_at`) VALUES
(2, 4, 5, 'Excellent mentor! Very knowledgeable and patient.', 1, NOW()),
(2, 5, 5, 'Great teaching style, highly recommended.', 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(3, 4, 4, 'Very helpful and professional.', 1, NOW()),
(3, 5, 5, 'Amazing UI/UX insights, learned a lot!', 1, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Additional bookings
INSERT IGNORE INTO `bookings` (`fresher_id`, `session_id`, `status`, `notes`, `booking_date`, `created_at`) VALUES
(4, 1, 'approved', 'Excited to learn web development!', NOW(), NOW()),
(5, 1, 'pending', 'Looking forward to this session.', NOW(), NOW());

-- Additional assignments
INSERT IGNORE INTO `assignments` (`course_id`, `mentor_id`, `title`, `description`, `instructions`, `due_date`, `max_score`, `is_published`, `created_at`) VALUES
(1, 2, 'Build a Personal Portfolio', 'Create a responsive portfolio website using HTML and CSS.', 'Include a header, about section, projects gallery, and contact form.', DATE_ADD(NOW(), INTERVAL 7 DAY), 100, 1, NOW()),
(2, 3, 'Design a Mobile App Interface', 'Create a mobile app UI design for a fitness tracking app.', 'Include at least 5 screens with a consistent design system.', DATE_ADD(NOW(), INTERVAL 10 DAY), 100, 1, NOW());

-- Additional resources
INSERT IGNORE INTO `resources` (`course_id`, `mentor_id`, `title`, `description`, `file_url`, `file_type`, `is_public`, `download_count`, `created_at`) VALUES
(1, 2, 'HTML Cheat Sheet', 'Quick reference for HTML tags and attributes.', '/resources/html-cheat-sheet.pdf', 'application/pdf', 1, 45, NOW()),
(1, 2, 'CSS Flexbox Guide', 'Complete guide to CSS Flexbox layout.', '/resources/css-flexbox-guide.pdf', 'application/pdf', 1, 38, NOW()),
(2, 3, 'UI Design System Template', 'Figma template for consistent UI design.', '/resources/ui-design-system.fig', 'application/octet-stream', 1, 22, NOW());

-- Additional research projects
INSERT IGNORE INTO `research_projects` (`mentor_id`, `title`, `description`, `objectives`, `required_skills`, `status`, `max_applicants`, `start_date`, `end_date`, `created_at`) VALUES
(2, 'E-commerce Website Development', 'Build a full-stack e-commerce platform with payment integration.', 'Learn modern web development practices.', 'PHP, MySQL, JavaScript', 'open', 5, DATE_ADD(NOW(), INTERVAL 14 DAY), DATE_ADD(NOW(), INTERVAL 60 DAY), NOW()),
(3, 'Mobile App UX Research', 'Conduct user research and design a mobile app experience.', 'Understand user needs and design solutions.', 'UI/UX, Figma, User Research', 'open', 3, DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 45 DAY), NOW());

-- Additional interview questions
INSERT IGNORE INTO `interview_questions` (`mentor_id`, `field_id`, `question`, `answer`, `difficulty`, `category`, `is_published`, `created_at`) VALUES
(2, 1, 'What is the difference between GET and POST in PHP?', 'GET sends data in URL parameters while POST sends data in the request body. POST is more secure for sensitive data.', 'easy', 'PHP', 1, NOW()),
(2, 1, 'Explain MVC architecture.', 'MVC stands for Model-View-Controller. It separates application logic into three interconnected components for better organization.', 'medium', 'Architecture', 1, NOW()),
(3, 1, 'What is the difference between UI and UX?', 'UI (User Interface) is about visual design, while UX (User Experience) is about overall user satisfaction and usability.', 'easy', 'Design', 1, NOW());

-- Settings
INSERT IGNORE INTO `settings` (`key`, `value`, `type`, `description`, `auto_load`, `created_at`) VALUES
('site_name', 'SkillShare Hub', 'string', 'Website name', 1, NOW()),
('site_description', 'Learn from Industry Experts', 'string', 'Website description', 1, NOW()),
('site_email', 'contact@skillsharehub.com', 'string', 'Contact email', 1, NOW()),
('registration_enabled', '1', 'boolean', 'Enable user registration', 1, NOW()),
('max_upload_size', '10485760', 'integer', 'Maximum file upload size in bytes', 1, NOW());
