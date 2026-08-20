-- SkillShare Hub live schema alignment
-- Safe to run against the existing skillshare_hub database.

USE `skillshare_hub`;

ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `academic_field_id` INT NULL AFTER `address`,
    ADD COLUMN IF NOT EXISTS `course_id` INT NULL AFTER `academic_field_id`;

CREATE TABLE IF NOT EXISTS `user_skills` (
    `user_id` INT NOT NULL,
    `skill_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `skill_id`),
    CONSTRAINT `fk_user_skills_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_skills_skill` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
