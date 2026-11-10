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

    SET v_certificate_code = CONCAT('SSH-', UPPER(SUBSTRING(MD5(RAND()), 1, 8)), '-', p_fresher_id, '-', p_course_id);

    INSERT INTO certificates (fresher_id, course_id, certificate_code, is_valid, issued_at)
    VALUES (p_fresher_id, p_course_id, v_certificate_code, 1, NOW())
    ON DUPLICATE KEY UPDATE
        certificate_code = VALUES(certificate_code),
        is_valid = 1,
        issued_at = NOW(),
        updated_at = NOW();

END$$

DELIMITER ;
