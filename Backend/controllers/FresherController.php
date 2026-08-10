<?php

/**
 * Fresher Controller
 */

require_once __DIR__ . '/../models/Fresher.php';

class FresherController
{
    private $fresherModel;

    public function __construct()
    {
        $this->fresherModel = new Fresher();
    }

    public function index()
    {
        $freshers = $this->fresherModel->getAll();
        sendSuccess($freshers);
    }
}
