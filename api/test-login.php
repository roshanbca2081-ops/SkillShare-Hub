<?php
require_once 'config.php';

// Simulate a login request
$data = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

// Simulate the API logic
try {
    $pdo = getDB();

    // Query user by email
    $stmt = $pdo->prepare('SELECT id, firstname, lastname, email, password, role, status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $data['email']]);
    $user = $stmt->fetch();

    echo "<h2>Database Query Result:</h2>";
    if (!$user) {
        echo "<p style='color: red;'>❌ User not found</p>";
    } else {
        echo "<p style='color: green;'>✅ User found!</p>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        
        // Check password
        echo "<h3>Password Verification:</h3>";
        echo "<p><strong>Entered Password:</strong> " . htmlspecialchars($data['password']) . "</p>";
        echo "<p><strong>Stored Password:</strong> " . htmlspecialchars($user['password']) . "</p>";
        
        if ($data['password'] === $user['password']) {
            echo "<p style='color: green;'>✅ Password matches!</p>";
        } else {
            echo "<p style='color: red;'>❌ Password does not match</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
