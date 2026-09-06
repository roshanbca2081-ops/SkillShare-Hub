-- =============================================
-- SkillShare Hub - Seeder
-- 03_courses_seeder.sql
-- =============================================

INSERT IGNORE INTO `courses` (`id`, `title`, `slug`, `description`, `mentor_id`, `field_id`, `status`, `level`, `price`, `duration`, `thumbnail`, `featured`, `total_students`, `progress`, `is_published`, `created_at`) VALUES
(1, 'Web Development Fundamentals', 'web-development-fundamentals', 'Learn HTML, CSS, JavaScript and build real-world websites.', 2, 1, 'active', 'beginner', 49.99, 40, 'web-dev.jpg', 1, 120, 0, 1, NOW()),
(2, 'UI/UX Design Mastery', 'uiux-design-mastery', 'Master user interface and experience design principles.', 3, 1, 'active', 'intermediate', 59.99, 35, 'uiux.jpg', 1, 85, 0, 1, NOW()),
(3, 'Data Science with Python', 'data-science-with-python', 'Learn data analysis, visualization, and machine learning.', 2, 1, 'active', 'advanced', 79.99, 50, 'datascience.jpg', 0, 60, 0, 1, NOW()),
(4, 'Digital Marketing Strategy', 'digital-marketing-strategy', 'Master SEO, social media marketing, and analytics.', 3, 3, 'active', 'beginner', 39.99, 25, 'marketing.jpg', 0, 95, 0, 1, NOW());
