<?php

/**
 * Research Controller
 */

require_once __DIR__ . '/../models/Research.php';

class ResearchController
{
    private $researchModel;

    public function __construct()
    {
        $this->researchModel = new Research();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
