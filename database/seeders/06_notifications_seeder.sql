-- =============================================
-- SkillShare Hub - Seeder
-- 06_notifications_seeder.sql
-- =============================================

INSERT IGNORE INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `icon`, `is_read`, `created_at`) VALUES
(1, 4, 'booking', 'Session Booked', 'Your booking for Introduction to Web Development has been confirmed.', 'fresher/my-sessions.php', 'fa-calendar-check', 0, NOW()),
(2, 4, 'message', 'New Message', 'You have received a new message from Roshan Timalsina.', 'fresher/messages/chat.php?user_id=2', 'fa-envelope', 0, NOW()),
(3, 5, 'enrollment', 'Course Enrollment', 'You have been enrolled in Data Science with Python.', 'fresher/my-courses.php', 'fa-book-open', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 2, 'booking', 'New Booking', 'Amit Sharma has booked your upcoming session.', 'mentor/sessions/my-sessions.php', 'fa-calendar-check', 0, NOW());
