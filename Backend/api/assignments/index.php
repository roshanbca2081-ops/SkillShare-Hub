<?php

/**
 * API Assignments Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/AssignmentController.php';

$controller = new AssignmentController();
$controller->index();
