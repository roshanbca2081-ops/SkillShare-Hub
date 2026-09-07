<?php
$page_title = 'Download Certificate';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$cert_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$cert_id) {
    redirect('fresher/certificate/index.php');
}

$stmt = $pdo->prepare("SELECT c.*, cr.title as course_title, cr.duration, u.full_name as mentor_name 
                       FROM certificates c 
                       JOIN courses cr ON c.course_id = cr.id 
                       JOIN users u ON cr.mentor_id = u.id 
                       WHERE c.id = ? AND c.fresher_id = ? AND c.is_valid = 1");
$stmt->execute([$cert_id, $user_id]);
$cert = $stmt->fetch();

if (!$cert) {
    redirect('fresher/certificate/index.php');
}

$certificate_code = $cert['certificate_code'];
$student_name = $_SESSION['full_name'];
$course_title = $cert['course_title'];
$mentor_name = $cert['mentor_name'];
$issued_date = formatDate($cert['issued_at']);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?php echo htmlspecialchars($course_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; padding: 40px 20px; }
        .certificate-container { max-width: 900px; margin: 0 auto; background: white; padding: 60px; border-radius: 16px; box-shadow: 0 8px 40px rgba(0,0,0,0.1); text-align: center; border: 8px solid #1E3A8A; position: relative; }
        .certificate-container::before { content: ''; position: absolute; top: 15px; left: 15px; right: 15px; bottom: 15px; border: 2px solid #2563EB; border-radius: 8px; pointer-events: none; }
        .cert-icon { font-size: 4rem; color: #2563EB; margin-bottom: 20px; }
        .cert-title { font-size: 2.5rem; font-weight: 700; color: #1E3A8A; margin-bottom: 10px; }
        .cert-subtitle { font-size: 1.1rem; color: #6b7280; margin-bottom: 40px; }
        .cert-student { font-size: 2rem; font-weight: 700; color: #2563EB; margin: 30px 0; }
        .cert-course { font-size: 1.5rem; font-weight: 600; color: #1E3A8A; margin-bottom: 10px; }
        .cert-mentor { color: #6b7280; margin-bottom: 40px; }
        .cert-footer { display: flex; justify-content: space-between; margin-top: 60px; padding-top: 30px; border-top: 2px solid #e5e7eb; }
        .cert-signature { text-align: center; }
        .cert-signature-line { width: 200px; border-bottom: 2px solid #1E3A8A; margin-bottom: 10px; }
        .cert-code { margin-top: 40px; padding: 15px; background: #f9fafb; border-radius: 8px; font-family: monospace; }
        .btn-print { margin-top: 30px; }
        @media print { body { padding: 0; } .certificate-container { box-shadow: none; border-radius: 0; } .btn-print { display: none; } }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="cert-icon">
            <i class="fas fa-certificate"></i>
        </div>
        <h1 class="cert-title">Certificate of Completion</h1>
        <p class="cert-subtitle">This is to certify that</p>
        
        <div class="cert-student"><?php echo htmlspecialchars($student_name); ?></div>
        <p class="cert-subtitle">has successfully completed the course</p>
        
        <div class="cert-course"><?php echo htmlspecialchars($course_title); ?></div>
        <p class="cert-mentor">Instructor: <?php echo htmlspecialchars($mentor_name); ?></p>
        
        <div class="cert-footer">
            <div class="cert-signature">
                <div class="cert-signature-line"></div>
                <p><strong><?php echo htmlspecialchars($student_name); ?></p>
                <p class="text-muted small">Student</p>
            </div>
            <div class="cert-signature">
                <div class="cert-signature-line"></div>
                <p><strong><?php echo htmlspecialchars($mentor_name); ?></p>
                <p class="text-muted small">Instructor</p>
            </div>
        </div>
        
        <div class="cert-code">
            <strong>Certificate Code:</strong> <?php echo $certificate_code; ?><br>
            <strong>Issued Date:</strong> <?php echo $issued_date; ?>
        </div>
        
        <div class="text-center btn-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-download me-2"></i> Download / Print Certificate
            </button>
        </div>
    </div>
</body>
</html>
