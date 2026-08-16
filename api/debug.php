<?php
require_once 'config.php';

try {
    $pdo = getDB();
    echo "<h2>✅ Database Connection: SUCCESS</h2>";
    
    // Check users table
    $result = $pdo->query('SHOW COLUMNS FROM users');
    $columns = $result->fetchAll();
    
    echo "<h3>Users Table Columns:</h3>";
    echo "<ul>";
    foreach($columns as $col) {
        echo "<li><strong>" . $col['Field'] . "</strong> (" . $col['Type'] . ")</li>";
    }
    echo "</ul>";
    
    // Check existing users
    $result = $pdo->query('SELECT * FROM users LIMIT 5');
    $users = $result->fetchAll();
    
    echo "<h3>Existing Users:</h3>";
    if(empty($users)) {
        echo "<p style='color: orange;'>No users found in database</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
        foreach($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['firstname'] . "</td>";
            echo "<td>" . $user['lastname'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error: " . $e->getMessage() . "</h2>";
}
?>
