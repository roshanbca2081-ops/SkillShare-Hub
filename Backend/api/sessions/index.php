<?php

/**
 * API Sessions Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/SessionController.php';

$controller = new SessionController();
$controller->index();
