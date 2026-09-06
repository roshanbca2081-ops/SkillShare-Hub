-- =============================================
-- SkillShare Hub - Seeder
-- 02_academic_fields_seeder.sql
-- =============================================

INSERT IGNORE INTO `academic_fields` (`id`, `name`, `description`, `icon`, `color`, `is_active`, `created_at`) VALUES
(1, 'Computer Science', 'Explore programming, software development, and computer technology.', 'fa-laptop-code', '#2563EB', 1, NOW()),
(2, 'Engineering', 'Civil, mechanical, electrical, and electronics engineering disciplines.', 'fa-gear', '#7C3AED', 1, NOW()),
(3, 'Business', 'Management, finance, marketing, and entrepreneurship.', 'fa-briefcase', '#059669', 1, NOW()),
(4, 'Health Sciences', 'Medicine, nursing, pharmacy, and public health.', 'fa-heart-pulse', '#DC2626', 1, NOW()),
(5, 'Education', 'Teaching, educational leadership, and curriculum development.', 'fa-graduation-cap', '#D97706', 1, NOW()),
(6, 'Law', 'Legal studies, corporate law, and human rights.', 'fa-scale-balanced', '#4F46E5', 1, NOW());
