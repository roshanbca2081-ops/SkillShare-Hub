<?php

/**
 * Profile Controller
 */

class ProfileController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function show()
    {
        if (!isLoggedIn()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();

        $user = $this->db->fetch(
            "SELECT u.*, af.name as field_name, c.name as course_name FROM users u LEFT JOIN academic_fields af ON u.academic_field_id = af.id LEFT JOIN courses c ON u.course_id = c.id WHERE u.id = ?",
            [$userId]
        );

        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];
        $this->render('profile/show', $data);
    }

    public function update()
    {
        if (!isLoggedIn()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $userId = getUserId();

        $data = [
            'full_name' => sanitize($_POST['full_name'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'bio' => sanitize($_POST['bio'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'city' => sanitize($_POST['city'] ?? ''),
            'state' => sanitize($_POST['state'] ?? ''),
            'country' => sanitize($_POST['country'] ?? ''),
            'postal_code' => sanitize($_POST['postal_code'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadProfilePicture($_FILES['profile_picture']);
            if ($uploadResult['success']) {
                $data['profile_picture'] = $uploadResult['filename'];
            }
        }

        $this->db->update('users', $data, 'id = ?', [$userId]);

        setFlash('success', 'Profile updated successfully');
        redirectBack();
    }

    public function updatePassword()
    {
        if (!isLoggedIn()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $userId = getUserId();
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Verify current password
        $user = $this->db->fetch("SELECT password_hash FROM users WHERE id = ?", [$userId]);
        if (!password_verify($current, $user['password_hash'])) {
            setFlash('error', 'Current password is incorrect');
            redirectBack();
            return;
        }

        if (strlen($new) < 6) {
            setFlash('error', 'New password must be at least 6 characters');
            redirectBack();
            return;
        }

        if ($new !== $confirm) {
            setFlash('error', 'Passwords do not match');
            redirectBack();
            return;
        }

        $this->db->update('users', [
            'password_hash' => password_hash($new, PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_ROUNDS]),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$userId]);

        setFlash('success', 'Password updated successfully');
        redirectBack();
    }

    private function uploadProfilePicture($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File too large'];
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . getUserId() . '_' . time() . '.' . $extension;
        $uploadPath = __DIR__ . '/../uploads/profiles/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            return ['success' => true, 'filename' => $filename];
        }

        return ['success' => false, 'message' => 'Upload failed'];
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
