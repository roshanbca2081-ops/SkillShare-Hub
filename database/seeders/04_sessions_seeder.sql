-- =============================================
-- SkillShare Hub - Seeder
-- 04_sessions_seeder.sql
-- =============================================

INSERT IGNORE INTO `sessions` (`id`, `title`, `description`, `mentor_id`, `course_id`, `status`, `scheduled_at`, `duration`, `price`, `is_free`, `max_participants`, `meeting_link`, `created_at`) VALUES
(1, 'Introduction to Web Development', 'Live session covering HTML, CSS, and JavaScript basics.', 2, 1, 'scheduled', DATE_ADD(NOW(), INTERVAL 2 DAY), 60, 0.00, 1, 20, 'https://meet.example.com/web-dev-intro', NOW()),
(2, 'Advanced UI/UX Techniques', 'Deep dive into advanced design principles and tools.', 3, 2, 'scheduled', DATE_ADD(NOW(), INTERVAL 5 DAY), 90, 19.99, 0, 10, 'https://meet.example.com/uiux-advanced', NOW()),
(3, 'Python for Data Science', 'Hands-on session with Python libraries for data analysis.', 2, 3, 'scheduled', DATE_ADD(NOW(), INTERVAL 7 DAY), 120, 29.99, 0, 15, 'https://meet.example.com/python-datascience', NOW());
