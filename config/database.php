<?php
$db_error = null;

$setup_connection = @new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($setup_connection && !$setup_connection->connect_error) {
    $setup_connection->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $setup_connection->close();
}

$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn && $conn->connect_error) {
    $db_error = $conn->connect_error;
    error_log('Database connection failed: ' . $db_error);
}
?>