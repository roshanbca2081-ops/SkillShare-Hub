-- =============================================
-- SkillShare Hub - Database Schema
-- Table 22: blog_posts
-- =============================================

CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `author_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `slug` VARCHAR(500) NOT NULL UNIQUE,
    `content` TEXT DEFAULT NULL,
    `excerpt` TEXT DEFAULT NULL,
    `featured_image` VARCHAR(500) DEFAULT NULL,
    `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    `published_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_blog_posts_author_id` (`author_id`),
    INDEX `idx_blog_posts_status` (`status`),
    INDEX `idx_blog_posts_slug` (`slug`),
    INDEX `idx_blog_posts_published_at` (`published_at`),
    CONSTRAINT `fk_blog_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
