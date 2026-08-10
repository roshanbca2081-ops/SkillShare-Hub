<?php

/**
 * API Users Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$controller = new UserController();
$controller->index();
