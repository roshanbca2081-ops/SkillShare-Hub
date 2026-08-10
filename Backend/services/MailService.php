<?php

/**
 * Mail Service
 */

require_once __DIR__ . '/../helpers/mail.php';

class MailService
{
    public function sendWelcomeEmail($to, $name)
    {
        $subject = "Welcome to SkillShare Hub";
        $body = "<h1>Welcome {$name}!</h1><p>Thank you for joining SkillShare Hub.</p>";
        return sendMail($to, $subject, $body);
    }

    public function sendPasswordReset($to, $resetLink)
    {
        $subject = "Reset Your Password";
        $body = "<p>Click <a href='{$resetLink}'>here</a> to reset your password.</p>";
        return sendMail($to, $subject, $body);
    }
}
