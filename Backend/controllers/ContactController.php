<?php

/**
 * Contact Controller
 */

class ContactController
{
    public function submit()
    {
        $data = getRequestData();
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $message = $data['message'] ?? '';

        if (empty($name) || empty($email) || empty($message)) {
            sendError('Name, email, and message are required.', 400);
        }

        sendSuccess(null, 'Thank you for reaching out! We will get back to you soon.');
    }
}
