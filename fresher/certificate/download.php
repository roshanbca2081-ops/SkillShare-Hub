<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$certificateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT c.*, cr.title AS course_title, u.full_name AS mentor_name, s.full_name AS recipient_name
                       FROM certificates c
                       JOIN courses cr ON c.course_id = cr.id
                       JOIN users u ON cr.mentor_id = u.id
                       JOIN users s ON c.fresher_id = s.id
                       WHERE c.id = ? AND c.fresher_id = ? AND c.is_valid = 1
                       LIMIT 1");
$stmt->execute([$certificateId, getUserId()]);
$certificate = $stmt->fetch();

if (!$certificate) {
    redirect('index.php');
}

try {
    require_once '../../vendor/autoload.php';
    $html = '<!doctype html><html lang="en"><head><meta charset="UTF-8"><title>Certificate</title></head><body>';
    ob_start();
    include '../../includes/certificate-document.php';
    $html .= ob_get_clean() . '</body></html>';

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');
    $pdf = new \Dompdf\Dompdf($options);
    $pdf->loadHtml($html, 'UTF-8');
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();

    $safeCode = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$certificate['certificate_code']);
    $pdf->stream('SkillShare-Certificate-' . $safeCode . '.pdf', ['Attachment' => true]);
    exit;
} catch (Throwable $e) {
    error_log('Certificate PDF generation failed: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('The certificate PDF could not be generated. Please use the Print button and choose Save as PDF.');
}
