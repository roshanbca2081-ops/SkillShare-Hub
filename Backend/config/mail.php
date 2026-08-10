<?php
/**
 * Mail Configuration File
 * Contains email settings and mail sending functionality
 */

require_once __DIR__ . '/../helpers/mail.php';

class MailConfig {
    private $mailer;

    public function __construct() {
        // Create PHPMailer instance
        $this->mailer = new PHPMailer(true);

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = MAIL_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = MAIL_USERNAME;
        $this->mailer->Password = MAIL_PASSWORD;
        $this->mailer->SMTPSecure = MAIL_ENCRYPTION;
        $this->mailer->Port = MAIL_PORT;

        // Charset
        $this->mailer->CharSet = 'UTF-8';

        // From settings
        $this->mailer->From = MAIL_FROM_ADDRESS;
        $this->mailer->FromName = MAIL_FROM_NAME;
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param array $cc Carbon copy recipients
     * @param array $bcc Blind carbon copy recipients
     * @param array $attachments File attachments
     * @return bool True if sent successfully, false otherwise
     */
    public function send($to, $subject, $body, $cc = [], $bcc = [], $attachments = []) {
        try {
            // Set recipients
            $this->mailer->addAddress($to);

            // Add CC recipients
            foreach ($cc as $email) {
                $this->mailer->addCC($email);
            }

            // Add BCC recipients
            foreach ($bcc as $email) {
                $this->mailer->addBCC($email);
            }

            // Add attachments
            foreach ($attachments as $file) {
                $this->mailer->addAttachment($file);
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            // Send email
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            // Log error
            error_log("Mail sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a template email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $template Template name
     * @param array $data Template data
     * @param array $cc Carbon copy recipients
     * @param array $bcc Blind carbon copy recipients
     * @param array $attachments File attachments
     * @return bool True if sent successfully, false otherwise
     */
    public function sendTemplate($to, $subject, $template, $data = [], $cc = [], $bcc = [], $attachments = []) {
        // Load template
        $templatePath = __DIR__ . '/../templates/emails/' . $template . '.php';

        if (!file_exists($templatePath)) {
            error_log("Email template not found: " . $templatePath);
            return false;
        }

        // Extract variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include template
        include $templatePath;

        // Get template content
        $body = ob_get_clean();

        // Send email
        return $this->send($to, $subject, $body, $cc, $bcc, $attachments);
    }

    /**
     * Send verification email
     * 
     * @param string $to Recipient email
     * @param string $token Verification token
     * @return bool True if sent successfully, false otherwise
     */
    public function sendVerificationEmail($to, $token) {
        $subject = 'Verify Your Email Address - ' . SITE_NAME;
        $template = 'verification';
        $data = [
            'name' => $this->getNameFromEmail($to),
            'verification_link' => SITE_URL . '/verify-email?token=' . $token,
            'site_name' => SITE_NAME,
            'site_url' => SITE_URL
        ];

        return $this->sendTemplate($to, $subject, $template, $data);
    }

    /**
     * Send password reset email
     * 
     * @param string $to Recipient email
     * @param string $token Reset token
     * @return bool True if sent successfully, false otherwise
     */
    public function sendPasswordResetEmail($to, $token) {
        $subject = 'Reset Your Password - ' . SITE_NAME;
        $template = 'password-reset';
        $data = [
            'name' => $this->getNameFromEmail($to),
            'reset_link' => SITE_URL . '/reset-password?token=' . $token,
            'site_name' => SITE_NAME,
            'site_url' => SITE_URL
        ];

        return $this->sendTemplate($to, $subject, $template, $data);
    }

    /**
     * Send welcome email
     * 
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @return bool True if sent successfully, false otherwise
     */
    public function sendWelcomeEmail($to, $name) {
        $subject = 'Welcome to ' . SITE_NAME . '!';
        $template = 'welcome';
        $data = [
            'name' => $name,
            'site_name' => SITE_NAME,
            'site_url' => SITE_URL,
            'login_url' => SITE_URL . '/login'
        ];

        return $this->sendTemplate($to, $subject, $template, $data);
    }

    /**
     * Send course enrollment confirmation
     * 
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @param string $course_name Course name
     * @param string $course_link Course link
     * @return bool True if sent successfully, false otherwise
     */
    public function sendEnrollmentConfirmation($to, $name, $course_name, $course_link) {
        $subject = 'Course Enrollment Confirmation - ' . SITE_NAME;
        $template = 'enrollment-confirmation';
        $data = [
            'name' => $name,
            'course_name' => $course_name,
            'course_link' => $course_link,
            'site_name' => SITE_NAME,
            'site_url' => SITE_URL
        ];

        return $this->sendTemplate($to, $subject, $template, $data);
    }

    /**
     * Send booking confirmation
     * 
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @param array $booking_details Booking details
     * @return bool True if sent successfully, false otherwise
     */
    public function sendBookingConfirmation($to, $name, $booking_details) {
        $subject = 'Booking Confirmation - ' . SITE_NAME;
        $template = 'booking-confirmation';
        $data = [
            'name' => $name,
            'booking_details' => $booking_details,
            'site_name' => SITE_NAME,
            'site_url' => SITE_URL
        ];

        return $this->sendTemplate($to, $subject, $template, $data);
    }

    /**
     * Extract name from email
     * 
     * @param string $email Email address
     * @return string Name extracted from email
     */
    private function getNameFromEmail($email) {
        $parts = explode('@', $email);
        return $parts[0] ?? 'User';
    }
}

// Create mailer instance
$mailer = new MailConfig();
