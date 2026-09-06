-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_generate_certificate.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_generate_certificate`(
    IN p_fresher_id INT,
    IN p_course_id INT
)
BEGIN
    DECLARE v_certificate_code VARCHAR(255);
    DECLARE v_certificate_id INT;

    SET v_certificate_code = CONCAT('SSH-', UPPER(SUBSTRING(MD5(RAND()), 1, 8)), '-', p_fresher_id, '-', p_course_id);

    INSERT INTO certificates (fresher_id, course_id, certificate_code, is_valid, issued_at)
    VALUES (p_fresher_id, p_course_id, v_certificate_code, 1, NOW())
    ON DUPLICATE KEY UPDATE
        certificate_code = VALUES(certificate_code),
        is_valid = 1,
        issued_at = NOW(),
        updated_at = NOW();

    SET v_certificate_id = LAST_INSERT_ID();

    SELECT 
        c.*,
        u.full_name AS fresher_name,
        co.title AS course_title,
        m.full_name AS mentor_name
    FROM certificates c
    JOIN users u ON c.fresher_id = u.id
    JOIN courses co ON c.course_id = co.id
    JOIN users m ON co.mentor_id = m.id
    WHERE c.id = v_certificate_id;
END$$

DELIMITER ;
