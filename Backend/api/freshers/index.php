<?php

/**
 * API Freshers Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../controllers/FresherController.php';

$controller = new FresherController();
$controller->index();
