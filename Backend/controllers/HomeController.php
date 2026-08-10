<?php
/**
 * Home Controller
 * Handles homepage and general site content
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AcademicField.php';
require_once __DIR__ . '/../models/Course.php';

class HomeController {
    private $userModel;
    private $academicFieldModel;
    private $courseModel;

    public function __construct() {
        $this->userModel = new User();
        $this->academicFieldModel = new AcademicField();
        $this->courseModel = new Course();
    }

    /**
     * Display homepage
     */
    public function index() {
        // Get statistics for dashboard
        $stats = [
            'total_users' => $this->userModel->getTotalUsers(),
            'total_mentors' => $this->userModel->getTotalMentors(),
            'total_courses' => $this->courseModel->getTotalCourses(),
            'total_fields' => $this->academicFieldModel->getTotalFields(),
        ];

        // Get recent courses
        $recentCourses = $this->courseModel->getRecentCourses(6);

        // Get popular fields
        $popularFields = $this->academicFieldModel->getPopularFields(4);

        // Get featured mentors
        $featuredMentors = $this->userModel->getFeaturedMentors(4);

        // Include homepage view
        include __DIR__ . '/../views/home/index.php';
    }

    /**
     * Display about page
     */
    public function about() {
        // Get team members
        $teamMembers = $this->userModel->getTeamMembers();

        // Include about page view
        include __DIR__ . '/../views/pages/about.php';
    }

    /**
     * Display contact page
     */
    public function contact() {
        // Include contact page view
        include __DIR__ . '/../views/pages/contact.php';
    }

    /**
     * Handle contact form submission
     */
    public function submitContact() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /contact');
            exit;
        }

        // Get form data
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validate input
        $errors = validateContactInput($name, $email, $subject, $message);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /contact');
            exit;
        }

        // Save contact message
        $contactId = $this->userModel->saveContactMessage([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);

        if ($contactId) {
            // Set success message
            setFlashMessage('success', 'Your message has been sent successfully. We will get back to you soon.');

            // Redirect to contact page
            header('Location: /contact');
            exit;
        } else {
            // Set error message
            $_SESSION['errors']['general'] = 'Failed to send your message. Please try again.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /contact');
            exit;
        }
    }

    /**
     * Display terms of service
     */
    public function terms() {
        // Include terms page view
        include __DIR__ . '/../views/pages/terms.php';
    }

    /**
     * Display privacy policy
     */
    public function privacy() {
        // Include privacy policy page view
        include __DIR__ . '/../views/pages/privacy.php';
    }

    /**
     * Display FAQ page
     */
    public function faq() {
        // Get FAQ categories
        $faqCategories = $this->userModel->getFAQCategories();

        // Include FAQ page view
        include __DIR__ . '/../views/pages/faq.php';
    }
}
