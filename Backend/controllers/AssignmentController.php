<?php

/**
 * Assignment Controller
 */

require_once __DIR__ . '/../models/Assignment.php';

class AssignmentController
{
    private $assignmentModel;

    public function __construct()
    {
        $this->assignmentModel = new Assignment();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
