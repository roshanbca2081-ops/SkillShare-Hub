<?php

/**
 * API Mentors Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/MentorController.php';

$controller = new MentorController();
$id = $_GET['id'] ?? null;

if ($id) {
    $controller->show($id);
} else {
    $controller->index();
}
