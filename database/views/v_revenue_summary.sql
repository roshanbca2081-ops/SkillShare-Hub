-- =============================================
-- SkillShare Hub - Database Views
-- v_revenue_summary.sql
-- =============================================

CREATE OR REPLACE VIEW `v_revenue_summary` AS
SELECT 
    DATE_FORMAT(p.payment_date, '%Y-%m') AS month,
    COUNT(DISTINCT p.id) AS total_payments,
    COUNT(DISTINCT p.fresher_id) AS unique_customers,
    SUM(p.amount) AS total_revenue,
    AVG(p.amount) AS average_payment,
    COUNT(DISTINCT p.course_id) AS courses_purchased
FROM payments p
WHERE p.status = 'completed' AND p.deleted_at IS NULL
GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
ORDER BY month DESC;
