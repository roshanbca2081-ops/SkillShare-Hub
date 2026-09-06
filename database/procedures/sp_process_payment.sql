-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_process_payment.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_process_payment`(
    IN p_fresher_id INT,
    IN p_course_id INT,
    IN p_amount DECIMAL(10, 2),
    IN p_payment_method VARCHAR(100),
    IN p_transaction_id VARCHAR(255)
)
BEGIN
    DECLARE v_payment_id INT;

    INSERT INTO payments (fresher_id, course_id, amount, payment_method, status, transaction_id, payment_date)
    VALUES (p_fresher_id, p_course_id, p_amount, p_payment_method, 'completed', p_transaction_id, NOW())
    ON DUPLICATE KEY UPDATE
        amount = VALUES(amount),
        status = 'completed',
        transaction_id = VALUES(transaction_id),
        payment_date = NOW(),
        updated_at = NOW();

    SET v_payment_id = LAST_INSERT_ID();

    IF v_payment_id = 0 THEN
        SELECT id INTO v_payment_id FROM payments WHERE fresher_id = p_fresher_id AND course_id = p_course_id;
    END IF;

    INSERT IGNORE INTO enrollments (fresher_id, course_id, status)
    VALUES (p_fresher_id, p_course_id, 'active')
    ON DUPLICATE KEY UPDATE status = 'active';

    UPDATE courses SET total_students = total_students + 1 WHERE id = p_course_id AND deleted_at IS NULL;

    INSERT INTO notifications (user_id, type, title, message, link, icon, is_read)
    VALUES (p_fresher_id, 'payment', 'Payment Successful', CONCAT('You have successfully enrolled in the course.'), 'fresher/my-courses.php', 'fa-check-circle', 0);

    SELECT 
        p.*,
        u.full_name AS fresher_name,
        c.title AS course_title
    FROM payments p
    JOIN users u ON p.fresher_id = u.id
    JOIN courses c ON p.course_id = c.id
    WHERE p.id = v_payment_id;
END$$

DELIMITER ;
