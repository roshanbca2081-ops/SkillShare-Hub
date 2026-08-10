<?php

/**
 * Mentor Controller
 */

require_once __DIR__ . '/../models/Mentor.php';

class MentorController
{
    private $mentorModel;

    public function __construct()
    {
        $this->mentorModel = new Mentor();
    }

    public function index()
    {
        $mentors = $this->mentorModel->getAll();
        sendSuccess($mentors);
    }

    public function show($id)
    {
        $mentor = $this->mentorModel->findById($id);
        if ($mentor) {
            $skills = $this->mentorModel->getSkills($id);
            $mentor['skills'] = $skills;
            sendSuccess($mentor);
        } else {
            sendError('Mentor not found', 404);
        }
    }
}
