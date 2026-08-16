<?php
/**
 * Admin Setup Script
 * Creates the first admin user if none exists
 * DELETE THIS FILE AFTER USE for security
 */

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($fullName) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $pdo = getDB();
        $existing = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $existing->execute([$email]);
        if ($existing->fetch()) {
            $error = 'Email already exists';
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, password_hash, role, status, is_verified, created_at) VALUES (?, ?, ?, ?, 'admin', 'active', 1, NOW())");
            $stmt->execute([$fullName, $email, $passwordHash, $passwordHash]);
            $adminId = $pdo->lastInsertId();
            
            $pdo->prepare("INSERT IGNORE INTO admins (user_id, is_super_admin, created_at) VALUES (?, 1, NOW())")->execute([$adminId]);
            
            $message = 'Admin user created successfully! You can now login at ' . BASE_URL . 'login.php';
        }
    }
}

$adminExists = false;
$pdo = getDB();
$existing = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$adminExists = (int)$existing > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - SkillShare Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.8);
            --text-muted: rgba(255,255,255,0.4);
            --glass-bg: rgba(255,255,255,0.05);
            --glass-border: rgba(255,255,255,0.1);
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --gradient-primary: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .setup-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: var(--radius-xl);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4);
        }
        .setup-card h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            text-align: center;
        }
        .setup-card p {
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }
        .input-wrapper {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
        }
        .input-wrapper:focus-within {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
        }
        .input-wrapper .input-icon {
            padding: 0 14px;
            color: var(--text-muted);
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 0;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            outline: none;
        }
        .input-wrapper input::placeholder {
            color: var(--text-muted);
        }
        .btn-primary {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: var(--radius-md);
            background: var(--gradient-primary);
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59,130,246,0.3);
        }
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-danger {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.2);
            color: #86efac;
        }
        .alert i { font-size: 1.1rem; flex-shrink: 0; }
    </style>
</head>
<body>
    <div class="setup-card">
        <h1><i class="fas fa-shield-halved"></i> Admin Setup</h1>
        <p><?php echo $adminExists ? 'Admin user already exists.' : 'Create the first admin account to access the admin panel.'; ?></p>
        
        <?php if ($message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!$adminExists): ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" name="full_name" placeholder="Enter admin full name" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" placeholder="Enter admin email" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" placeholder="Create a password" required minlength="6">
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-check-circle"></i></span>
                    <input type="password" name="confirm_password" placeholder="Confirm password" required minlength="6">
                </div>
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-user-plus"></i> Create Admin Account</button>
        </form>
        <?php else: ?>
        <div style="text-align:center;margin-top:16px;">
            <a href="<?php echo BASE_URL; ?>login.php" class="btn-primary" style="text-decoration:none;display:inline-block;width:auto;padding:14px 32px;"><i class="fas fa-sign-in-alt"></i> Go to Login</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
