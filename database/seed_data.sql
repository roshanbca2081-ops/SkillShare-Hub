-- =============================================
-- SkillShare Hub - Database Seed Data
-- Complete seed file for development and testing
-- =============================================

-- Users
INSERT IGNORE INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `is_active`, `bio`, `skills`, `title`, `location`, `created_at`) VALUES
(1, 'Admin User', 'admin@skillsharehub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'System Administrator', 'Management', 'Administrator', 'Kathmandu', NOW()),
(2, 'Roshan Timalsina', 'roshan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', 1, 'Experienced Full Stack Developer with 8+ years in web technologies.', 'PHP, JavaScript, React, Laravel, MySQL', 'Senior Full Stack Developer', 'Kathmandu', NOW()),
(3, 'Abiral Rai', 'abiral@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', 1, 'UI/UX Designer passionate about creating intuitive user experiences.', 'UI/UX, Figma, Adobe XD, HTML/CSS', 'UI/UX Designer', 'Pokhara', NOW()),
(4, 'Amit Sharma', 'amit@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fresher', 1, 'Recent BCA graduate looking to learn web development.', 'HTML, CSS, JavaScript', 'BCA Graduate', 'Kathmandu', NOW()),
(5, 'Priya Patel', 'priya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fresher', 1, 'BSc CSIT student interested in data science and AI.', 'Python, Data Analysis, Machine Learning', 'BSc CSIT Student', 'Lalitpur', NOW());

-- Academic Fields
INSERT IGNORE INTO `academic_fields` (`id`, `name`, `description`, `icon`, `color`, `is_active`, `created_at`) VALUES
(1, 'Computer Science', 'Explore programming, software development, and computer technology.', 'fa-laptop-code', '#2563EB', 1, NOW()),
(2, 'Engineering', 'Civil, mechanical, electrical, and electronics engineering disciplines.', 'fa-gear', '#7C3AED', 1, NOW()),
(3, 'Business', 'Management, finance, marketing, and entrepreneurship.', 'fa-briefcase', '#059669', 1, NOW()),
(4, 'Health Sciences', 'Medicine, nursing, pharmacy, and public health.', 'fa-heart-pulse', '#DC2626', 1, NOW()),
(5, 'Education', 'Teaching, educational leadership, and curriculum development.', 'fa-graduation-cap', '#D97706', 1, NOW()),
(6, 'Law', 'Legal studies, corporate law, and human rights.', 'fa-scale-balanced', '#4F46E5', 1, NOW());

-- Courses
INSERT IGNORE INTO `courses` (`id`, `title`, `slug`, `description`, `mentor_id`, `field_id`, `status`, `level`, `price`, `duration`, `thumbnail`, `featured`, `total_students`, `created_at`) VALUES
(1, 'Web Development Fundamentals', 'web-development-fundamentals', 'Learn HTML, CSS, JavaScript and build real-world websites.', 2, 1, 'active', 'beginner', 49.99, 40, 'web-dev.jpg', 1, 120, NOW()),
(2, 'UI/UX Design Mastery', 'uiux-design-mastery', 'Master user interface and experience design principles.', 3, 1, 'active', 'intermediate', 59.99, 35, 'uiux.jpg', 1, 85, NOW()),
(3, 'Data Science with Python', 'data-science-with-python', 'Learn data analysis, visualization, and machine learning.', 2, 1, 'active', 'advanced', 79.99, 50, 'datascience.jpg', 0, 60, NOW()),
(4, 'Digital Marketing Strategy', 'digital-marketing-strategy', 'Master SEO, social media marketing, and analytics.', 3, 3, 'active', 'beginner', 39.99, 25, 'marketing.jpg', 0, 95, NOW());

-- Sessions
INSERT IGNORE INTO `sessions` (`id`, `title`, `description`, `mentor_id`, `course_id`, `status`, `scheduled_at`, `duration`, `price`, `is_free`, `max_participants`, `meeting_link`, `created_at`) VALUES
(1, 'Introduction to Web Development', 'Live session covering HTML, CSS, and JavaScript basics.', 2, 1, 'scheduled', DATE_ADD(NOW(), INTERVAL 2 DAY), 60, 0.00, 1, 20, 'https://meet.example.com/web-dev-intro', NOW()),
(2, 'Advanced UI/UX Techniques', 'Deep dive into advanced design principles and tools.', 3, 2, 'scheduled', DATE_ADD(NOW(), INTERVAL 5 DAY), 90, 19.99, 0, 10, 'https://meet.example.com/uiux-advanced', NOW()),
(3, 'Python for Data Science', 'Hands-on session with Python libraries for data analysis.', 2, 3, 'scheduled', DATE_ADD(NOW(), INTERVAL 7 DAY), 120, 29.99, 0, 15, 'https://meet.example.com/python-datascience', NOW());

-- Enrollments
INSERT IGNORE INTO `enrollments` (`id`, `fresher_id`, `course_id`, `status`, `started_at`, `progress`) VALUES
(1, 4, 1, 'active', NOW(), 35),
(2, 4, 2, 'active', NOW(), 20),
(3, 5, 1, 'active', NOW(), 50),
(4, 5, 3, 'completed', DATE_SUB(NOW(), INTERVAL 30 DAY), 100);

-- Notifications
INSERT IGNORE INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `icon`, `is_read`, `created_at`) VALUES
(1, 4, 'booking', 'Session Booked', 'Your booking for Introduction to Web Development has been confirmed.', 'fresher/my-sessions.php', 'fa-calendar-check', 0, NOW()),
(2, 4, 'message', 'New Message', 'You have received a new message from Roshan Timalsina.', 'fresher/message/chat.php?user_id=2', 'fa-envelope', 0, NOW()),
(3, 5, 'enrollment', 'Course Enrollment', 'You have been enrolled in Data Science with Python.', 'fresher/my-courses.php', 'fa-book-open', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 2, 'booking', 'New Booking', 'Amit Sharma has booked your upcoming session.', 'mentor/sessions/my-sessions.php', 'fa-calendar-check', 0, NOW());

-- Additional sample data
INSERT IGNORE INTO `ratings` (`mentor_id`, `fresher_id`, `rating`, `review`, `is_public`, `created_at`) VALUES
(2, 4, 5, 'Excellent mentor! Very knowledgeable and patient.', 1, NOW()),
(2, 5, 5, 'Great teaching style, highly recommended.', 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(3, 4, 4, 'Very helpful and professional.', 1, NOW()),
(3, 5, 5, 'Amazing UI/UX insights, learned a lot!', 1, DATE_SUB(NOW(), INTERVAL 5 DAY));

INSERT IGNORE INTO `bookings` (`fresher_id`, `session_id`, `status`, `notes`, `booking_date`, `created_at`) VALUES
(4, 1, 'approved', 'Excited to learn web development!', NOW(), NOW()),
(5, 1, 'pending', 'Looking forward to this session.', NOW(), NOW());

INSERT IGNORE INTO `assignments` (`course_id`, `mentor_id`, `title`, `description`, `instructions`, `due_date`, `max_score`, `is_published`, `created_at`) VALUES
(1, 2, 'Build a Personal Portfolio', 'Create a responsive portfolio website using HTML and CSS.', 'Include a header, about section, projects gallery, and contact form.', DATE_ADD(NOW(), INTERVAL 7 DAY), 100, 1, NOW()),
(2, 3, 'Design a Mobile App Interface', 'Create a mobile app UI design for a fitness tracking app.', 'Include at least 5 screens with a consistent design system.', DATE_ADD(NOW(), INTERVAL 10 DAY), 100, 1, NOW());

INSERT IGNORE INTO `resources` (`course_id`, `mentor_id`, `title`, `description`, `file_url`, `file_type`, `is_public`, `download_count`, `created_at`) VALUES
(1, 2, 'HTML Cheat Sheet', 'Quick reference for HTML tags and attributes.', '/resources/html-cheat-sheet.pdf', 'application/pdf', 1, 45, NOW()),
(1, 2, 'CSS Flexbox Guide', 'Complete guide to CSS Flexbox layout.', '/resources/css-flexbox-guide.pdf', 'application/pdf', 1, 38, NOW()),
(2, 3, 'UI Design System Template', 'Figma template for consistent UI design.', '/resources/ui-design-system.fig', 'application/octet-stream', 1, 22, NOW());

INSERT IGNORE INTO `research_projects` (`mentor_id`, `title`, `description`, `objectives`, `required_skills`, `status`, `max_applicants`, `start_date`, `end_date`, `created_at`) VALUES
(2, 'E-commerce Website Development', 'Build a full-stack e-commerce platform with payment integration.', 'Learn modern web development practices.', 'PHP, MySQL, JavaScript', 'open', 5, DATE_ADD(NOW(), INTERVAL 14 DAY), DATE_ADD(NOW(), INTERVAL 60 DAY), NOW()),
(3, 'Mobile App UX Research', 'Conduct user research and design a mobile app experience.', 'Understand user needs and design solutions.', 'UI/UX, Figma, User Research', 'open', 3, DATE_ADD(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 45 DAY), NOW());

INSERT IGNORE INTO `interview_questions` (`mentor_id`, `field_id`, `question`, `answer`, `difficulty`, `category`, `is_published`, `created_at`) VALUES
(2, 1, 'What is the difference between GET and POST in PHP?', 'GET sends data in URL parameters while POST sends data in the request body. POST is more secure for sensitive data.', 'easy', 'PHP', 1, NOW()),
(2, 1, 'Explain MVC architecture.', 'MVC stands for Model-View-Controller. It separates application logic into three interconnected components for better organization.', 'medium', 'Architecture', 1, NOW()),
(3, 1, 'What is the difference between UI and UX?', 'UI (User Interface) is about visual design, while UX (User Experience) is about overall user satisfaction and usability.', 'easy', 'Design', 1, NOW());

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `is_public`, `created_at`) VALUES
('site_name', 'SkillShare Hub', 'general', 1, NOW()),
('site_description', 'Learn from Industry Experts', 'general', 1, NOW()),
('site_email', 'contact@skillsharehub.com', 'general', 1, NOW()),
('registration_enabled', '1', 'general', 0, NOW()),
('max_upload_size', '10485760', 'general', 0, NOW());
