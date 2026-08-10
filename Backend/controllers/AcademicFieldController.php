<?php

/**
 * AcademicField Controller
 */

require_once __DIR__ . '/../models/AcademicField.php';

class AcademicFieldController
{
    private $fieldModel;

    public function __construct()
    {
        $this->fieldModel = new AcademicField();
    }

    public function index()
    {
        $fields = $this->fieldModel->getAll();
        sendSuccess($fields);
    }

    public function show($id)
    {
        $field = is_numeric($id) ? $this->fieldModel->findById($id) : $this->fieldModel->findBySlug($id);
        if ($field) {
            $courses = $this->fieldModel->getCourses($field['id']);
            $field['courses'] = $courses;
            sendSuccess($field);
        } else {
            sendError('Academic Field not found', 404);
        }
    }
}
