-- =============================================
-- SkillShare Hub - Seeder
-- 05_enrollments_seeder.sql
-- =============================================

INSERT IGNORE INTO `enrollments` (`id`, `fresher_id`, `course_id`, `status`, `enrolled_at`, `progress`) VALUES
(1, 4, 1, 'active', NOW(), 35),
(2, 4, 2, 'active', NOW(), 20),
(3, 5, 1, 'active', NOW(), 50),
(4, 5, 3, 'completed', DATE_SUB(NOW(), INTERVAL 30 DAY), 100);
