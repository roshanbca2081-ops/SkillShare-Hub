<?php

/**
 * API Academic Fields Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/AcademicFieldController.php';

$controller = new AcademicFieldController();
$id = $_GET['id'] ?? ($_GET['slug'] ?? null);

if ($id) {
    $controller->show($id);
} else {
    $controller->index();
}
