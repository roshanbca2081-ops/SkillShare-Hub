<?php

/**
 * Interview Controller
 */

require_once __DIR__ . '/../models/Interview.php';

class InterviewController
{
    private $interviewModel;

    public function __construct()
    {
        $this->interviewModel = new Interview();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
