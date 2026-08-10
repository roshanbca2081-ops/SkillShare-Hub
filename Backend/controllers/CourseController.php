<?php

/**
 * Course Controller
 */

require_once __DIR__ . '/../models/Course.php';

class CourseController
{
    private $courseModel;

    public function __construct()
    {
        $this->courseModel = new Course();
    }

    public function index()
    {
        $courses = $this->courseModel->getAll();
        sendSuccess($courses);
    }

    public function show($id)
    {
        $course = $this->courseModel->findById($id);
        if ($course) {
            $subjects = $this->courseModel->getSubjects($id);
            $course['subjects'] = $subjects;
            sendSuccess($course);
        } else {
            sendError('Course not found', 404);
        }
    }
}
