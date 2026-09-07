<?php
$page_title = 'Verify Certificate';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';

$certificate_code = isset($_GET['code']) ? sanitize($_GET['code']) : '';
$verified = false;
$cert = null;

if ($certificate_code) {
    $stmt = $pdo->prepare("SELECT c.*, cr.title as course_title, u.full_name as student_name, u.email as student_email,
                           m.full_name as mentor_name 
                           FROM certificates c 
                           JOIN courses cr ON c.course_id = cr.id 
                           JOIN users u ON c.fresher_id = u.id 
                           JOIN users m ON cr.mentor_id = m.id 
                           WHERE c.certificate_code = ? AND c.is_valid = 1");
    $stmt->execute([$certificate_code]);
    $cert = $stmt->fetch();
    $verified = (bool)$cert;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - SkillShare Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf4 100%); min-height: 100vh; padding: 40px 20px; }
        .verify-container { max-width: 600px; margin: 0 auto; }
        .verify-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 8px 32px rgba(31, 38, 135, 0.12); text-align: center; }
        .verify-icon { font-size: 4rem; margin-bottom: 20px; }
        .verify-icon.success { color: #22c55e; }
        .verify-icon.error { color: #ef4444; }
        .verify-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 10px; }
        .verify-card h2 { color: #1E293B; }
        .verify-card p { color: #475569; }
        .form-control { padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 1rem; width: 100%; margin-bottom: 15px; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; }
        .btn-primary { background: #2563EB; color: white; }
        .btn-primary:hover { background: #1D4ED8; }
        .cert-details { margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px; text-align: left; }
        .cert-details p { margin-bottom: 8px; }
        .cert-details strong { color: #1E293B; }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <h1 class="verify-title">Certificate Verification</h1>
            <p>Enter your certificate code to verify its authenticity</p>
            
            <form method="GET" class="mt-4">
                <input type="text" name="code" class="form-control" placeholder="Enter certificate code (e.g., SSH-XXXX-XX)" value="<?php echo htmlspecialchars($certificate_code); ?>" required>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i> Verify Certificate
                </button>
            </form>
            
            <?php if ($certificate_code): ?>
                <?php if ($verified): ?>
                    <div class="verify-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="text-success">Certificate Verified!</h2>
                    <p>This certificate is valid and authentic.</p>
                    
                    <div class="cert-details">
                        <p><strong>Student:</strong> <?php echo htmlspecialchars($cert['student_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($cert['student_email']); ?></p>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($cert['course_title']); ?></p>
                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($cert['mentor_name']); ?></p>
                        <p><strong>Issued:</strong> <?php echo formatDate($cert['issued_at']); ?></p>
                        <p><strong>Certificate Code:</strong> <?php echo $cert['certificate_code']; ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Valid</span></p>
                    </div>
                <?php else: ?>
                    <div class="verify-icon error">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h2 class="text-danger">Certificate Not Found</h2>
                    <p>The certificate code you entered is invalid or does not exist.</p>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to My Certificates
                </a>
            </div>
        </div>
    </div>
</body>
</html>
