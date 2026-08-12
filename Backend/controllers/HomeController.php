<?php
/**
 * Home Controller
 * Handles homepage and general site content
 */

class HomeController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $stats = [
            'fields' => $this->db->count('academic_fields', "status = 'active'"),
            'courses' => $this->db->count('courses', "status = 'active'"),
            'mentors' => $this->db->count('users', "role = 'mentor' AND status = 'active'"),
            'freshers' => $this->db->count('users', "role = 'fresher' AND status = 'active'"),
            'sessions' => $this->db->count('sessions', "status = 'completed'"),
            'skills' => $this->db->count('skills', "status = 'active'"),
            'reviews' => $this->db->count('reviews')
        ];

        $fields = $this->db->fetchAll(
            "SELECT * FROM academic_fields WHERE status = 'active' ORDER BY sort_order LIMIT 8"
        );

        $mentors = $this->db->fetchAll(
            "SELECT u.*, m.rating, m.reviews_count, m.total_sessions FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1 ORDER BY m.rating DESC LIMIT 6"
        );

        $data = [
            'title' => 'Home',
            'stats' => $stats,
            'fields' => $fields,
            'mentors' => $mentors
        ];
        $this->render('home/index', $data);
    }

    public function about()
    {
        $this->render('home/about', ['title' => 'About']);
    }

    public function contact()
    {
        $this->render('contact/index', ['title' => 'Contact']);
    }

    public function sendContact()
    {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        // Validate
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            setFlash('error', 'All fields are required');
            redirectBack();
            return;
        }

        if (!validateEmail($email)) {
            setFlash('error', 'Valid email is required');
            redirectBack();
            return;
        }

        $this->db->insert('contact_messages', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        setFlash('success', 'Message sent successfully! We\'ll get back to you soon.');
        redirect(APP_URL . 'contact');
    }

    public function submitContact()
    {
        $this->sendContact();
    }

    public function terms()
    {
        $this->render('terms', ['title' => 'Terms of Service']);
    }

    public function privacy()
    {
        $this->render('privacy-policy', ['title' => 'Privacy Policy']);
    }

    public function faq()
    {
        $this->render('faq', ['title' => 'FAQ']);
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
