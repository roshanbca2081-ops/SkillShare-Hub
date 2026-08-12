<?php
/**
 * SkillShare Hub - Development Seeder
 * Run via browser: http://localhost/SkillShare-Hub/database/seeder.php
 * or CLI: php database/seeder.php
 */

require_once __DIR__ . '/../Backend/config/config.php';
require_once __DIR__ . '/../Backend/config/database.php';

$pdo = getDB();

$users = [
    ['Admin','User','admin@skillsharehub.com','Password123!','admin'],
    ['Aisha','Khan','mentor@skillsharehub.com','Password123!','mentor'],
    ['Alex','Johnson','fresher@skillsharehub.com','Password123!','fresher']
];

$created = [];
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role, status, email_verified, created_at) VALUES (?, ?, ?, ?, ?, 'active', 1, NOW()) ON DUPLICATE KEY UPDATE email=email");
    foreach ($users as $u) {
        [$fn,$ln,$email,$pwd,$role] = $u;
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt->execute([$fn, $ln, $email, $hash, $role]);
        $created[] = ['email' => $email, 'password' => $pwd, 'role' => $role];
    }
    $pdo->commit();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'created' => $created], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header('Content-Type: application/json', true, 500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
