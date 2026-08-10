<?php

/**
 * API Courses Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/CourseController.php';

$controller = new CourseController();
$id = $_GET['id'] ?? null;

if ($id) {
    $controller->show($id);
} else {
    $controller->index();
}
