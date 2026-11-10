-- Session bookings may be paid without being attached to a course.
-- Run once against existing installations before enabling eSewa/Khalti checkout.
SET @payments_course_fk = (
    SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments'
      AND COLUMN_NAME = 'course_id' AND REFERENCED_TABLE_NAME = 'courses'
    LIMIT 1
);
SET @drop_payments_course_fk_sql = IF(
    @payments_course_fk IS NULL,
    'SELECT 1',
    CONCAT('ALTER TABLE `payments` DROP FOREIGN KEY `', REPLACE(@payments_course_fk, '`', '``'), '`')
);
PREPARE drop_payments_course_fk FROM @drop_payments_course_fk_sql;
EXECUTE drop_payments_course_fk;
DEALLOCATE PREPARE drop_payments_course_fk;

SET @courses_id_type = (
    SELECT COLUMN_TYPE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'courses' AND COLUMN_NAME = 'id'
    LIMIT 1
);
SET @alter_payments_course_id_sql = CONCAT(
    'ALTER TABLE `payments` MODIFY COLUMN `course_id` ', @courses_id_type, ' NULL'
);
PREPARE alter_payments_course_id FROM @alter_payments_course_id_sql;
EXECUTE alter_payments_course_id;
DEALLOCATE PREPARE alter_payments_course_id;

ALTER TABLE `payments`
    ADD CONSTRAINT `fk_payments_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`)ON DELETE SET NULL ON UPDATE CASCADE;

-- Older application installs may not have the metadata column. Add it only if absent.
SET @has_payment_metadata = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'metadata'
);
SET @add_payment_metadata_sql = IF(
    @has_payment_metadata = 0,
    'ALTER TABLE `payments` ADD COLUMN `metadata` JSON DEFAULT NULL AFTER `transaction_id`',
    'SELECT 1'
);
PREPARE add_payment_metadata FROM @add_payment_metadata_sql;
EXECUTE add_payment_metadata;
DEALLOCATE PREPARE add_payment_metadata;