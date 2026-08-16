<?php
require 'config.php';

try {
    $db = getDB();
    echo "✅ Database Connection: SUCCESS<br><br>";
    
    // Get all tables
    $result = $db->query('SHOW TABLES');
    $tables = $result->fetchAll();
    
    echo "<strong>Tables in skillshare_hub database:</strong><br>";
    echo "<ol>";
    foreach($tables as $row) {
        $tableName = array_values($row)[0];
        echo "<li>" . htmlspecialchars($tableName) . "</li>";
    }
    echo "</ol>";
    
    echo "<br><strong>Total Tables: " . count($tables) . "</strong>";
    
} catch (Exception $e) {
    echo "❌ Connection Failed: " . $e->getMessage();
}
?>
