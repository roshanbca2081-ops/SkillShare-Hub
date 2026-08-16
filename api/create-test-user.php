<?php
require_once 'config.php';

try {
    $pdo = getDB();

    // Check if test user already exists
    $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $check->execute([':email' => 'test@example.com']);

    if ($check->fetch()) {
        echo "<h2 style='color: orange;'>⚠️ Test user already exists (test@example.com)</h2>";
    } else {
        // Insert test user
        $stmt = $pdo->prepare('INSERT INTO users (full_name, firstname, lastname, email, password, role, status)
                               VALUES (:full_name, :firstname, :lastname, :email, :password, :role, :status)');
        $stmt->execute([
            ':full_name' => 'Test User',
            ':firstname' => 'Test',
            ':lastname' => 'User',
            ':email' => 'test@example.com',
            ':password' => 'password123',
            ':role' => 'fresher',
            ':status' => 'active'
        ]);

        echo "<h2 style='color: green;'>✅ Test user created successfully!</h2>";
        echo "<p><strong>Email:</strong> test@example.com</p>";
        echo "<p><strong>Password:</strong> password123</p>";
        echo "<p><strong>Role:</strong> fresher</p>";
        echo "<p><a href='../login.php'>Go to Login →</a></p>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error: " . $e->getMessage() . "</h2>";
}
?>
