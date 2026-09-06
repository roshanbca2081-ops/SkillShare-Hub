-- =============================================
-- SkillShare Hub - Seeder
-- 01_users_seeder.sql
-- =============================================

INSERT IGNORE INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `is_active`, `bio`, `skills`, `title`, `location`, `created_at`) VALUES
(1, 'Admin User', 'admin@skillsharehub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'System Administrator', 'Management', 'Administrator', 'Kathmandu', NOW()),
(2, 'Roshan Timalsina', 'roshan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', 1, 'Experienced Full Stack Developer with 8+ years in web technologies.', 'PHP, JavaScript, React, Laravel, MySQL', 'Senior Full Stack Developer', 'Kathmandu', NOW()),
(3, 'Abiral Rai', 'abiral@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', 1, 'UI/UX Designer passionate about creating intuitive user experiences.', 'UI/UX, Figma, Adobe XD, HTML/CSS', 'UI/UX Designer', 'Pokhara', NOW()),
(4, 'Amit Sharma', 'amit@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fresher', 1, 'Recent BCA graduate looking to learn web development.', 'HTML, CSS, JavaScript', 'BCA Graduate', 'Kathmandu', NOW()),
(5, 'Priya Patel', 'priya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fresher', 1, 'BSc CSIT student interested in data science and AI.', 'Python, Data Analysis, Machine Learning', 'BSc CSIT Student', 'Lalitpur', NOW());
