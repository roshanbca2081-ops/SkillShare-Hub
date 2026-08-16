
<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();

    // Sync acad_fields into academic_fields
    $pdo->exec("INSERT INTO academic_fields (id, name, slug, icon, color, description, status)
                SELECT id, name, slug, icon, color, description, status FROM acad_fields
                ON DUPLICATE KEY UPDATE name=VALUES(name), slug=VALUES(slug), icon=VALUES(icon), color=VALUES(color), description=VALUES(description), status=VALUES(status)");

    // Sync acad_courses into courses
    $pdo->exec("INSERT IGNORE INTO courses (id, field_id, academic_field_id, name, slug, icon, description, status)
                SELECT id, field_id, field_id, name, CONCAT(slug, '-', field_id), icon, description, status FROM acad_courses");

    // Update total_courses count
    $pdo->exec("UPDATE academic_fields af SET total_courses = (SELECT COUNT(*) FROM courses c WHERE c.field_id = af.id OR c.academic_field_id = af.id)");

    echo "Synced acad_fields -> academic_fields successfully!\n";
    echo "academic_fields count: " . $pdo->query("SELECT COUNT(*) FROM academic_fields")->fetchColumn() . "\n";
    echo "courses count: " . $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "Sync error: " . $e->getMessage() . "\n";
}
