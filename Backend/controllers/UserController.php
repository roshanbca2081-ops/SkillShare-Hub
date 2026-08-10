<?php
/**
 * User Controller
 * Handles user management operations
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../helpers/upload.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Display user listing
     */
    public function index() {
        // Ensure admin access
        AdminMiddleware::check();

        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get search parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role = isset($_GET['role']) ? trim($_GET['role']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';

        // Get users with filters
        $users = $this->userModel->getUsers($limit, $offset, $search, $role, $status);
        $totalUsers = $this->userModel->getTotalUsers($search, $role, $status);

        // Calculate total pages
        $totalPages = ceil($totalUsers / $limit);

        // Include user listing view
        include __DIR__ . '/../views/users/index.php';
    }

    /**
     * Display user creation form
     */
    public function create() {
        // Ensure admin access
        AdminMiddleware::check();

        // Get roles for dropdown
        $roles = $this->userModel->getRoles();

        // Include user creation form view
        include __DIR__ . '/../views/users/create.php';
    }

    /**
     * Store new user
     */
    public function store() {
        // Ensure admin access
        AdminMiddleware::check();

        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /users/create');
            exit;
        }

        // Get form data
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'fresher';
        $status = $_POST['status'] ?? 'active';

        // Validate input
        $errors = validateUserInput($name, $email, $password, $confirm_password, $role, $status);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /users/create');
            exit;
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            $_SESSION['errors']['email'] = 'This email is already registered';
            $_SESSION['old_input'] = $_POST;
            header('Location: /users/create');
            exit;
        }

        // Handle profile picture upload
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['profile_picture'], 'profiles');
            if ($uploadResult['success']) {
                $profilePicture = $uploadResult['filename'];
            } else {
                $_SESSION['errors']['profile_picture'] = $uploadResult['message'];
                $_SESSION['old_input'] = $_POST;
                header('Location: /users/create');
                exit;
            }
        }

        // Create user
        $userId = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'status' => $status,
            'profile_picture' => $profilePicture
        ]);

        if ($userId) {
            // Set success message
            setFlashMessage('success', 'User created successfully.');

            // Redirect to user listing
            header('Location: /users');
            exit;
        } else {
            // Set error message
            $_SESSION['errors']['general'] = 'Failed to create user. Please try again.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /users/create');
            exit;
        }
    }

    /**
     * Display user edit form
     */
    public function edit($id) {
        // Ensure admin access
        AdminMiddleware::check();

        // Get user by ID
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlashMessage('error', 'User not found.');
            header('Location: /users');
            exit;
        }

        // Get roles for dropdown
        $roles = $this->userModel->getRoles();

        // Include user edit form view
        include __DIR__ . '/../views/users/edit.php';
    }

    /**
     * Update user
     */
    public function update($id) {
        // Ensure admin access
        AdminMiddleware::check();

        // Get existing user
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlashMessage('error', 'User not found.');
            header('Location: /users');
            exit;
        }

        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /users/edit/' . $id);
            exit;
        }

        // Get form data
        $name = $_POST['name'] ?? $user->name;
        $email = $_POST['email'] ?? $user->email;
        $role = $_POST['role'] ?? $user->role;
        $status = $_POST['status'] ?? $user->status;

        // Check if email changed and already exists
        if ($email !== $user->email && $this->userModel->emailExists($email)) {
            $_SESSION['errors']['email'] = 'This email is already registered';
            $_SESSION['old_input'] = $_POST;
            header('Location: /users/edit/' . $id);
            exit;
        }

        // Handle password update
        $password = null;
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        // Handle profile picture upload
        $profilePicture = $user->profile_picture;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['profile_picture'], 'profiles');
            if ($uploadResult['success']) {
                // Delete old picture if exists
                if ($profilePicture) {
                    deleteFile($profilePicture);
                }
                $profilePicture = $uploadResult['filename'];
            } else {
                $_SESSION['errors']['profile_picture'] = $uploadResult['message'];
                $_SESSION['old_input'] = $_POST;
                header('Location: /users/edit/' . $id);
                exit;
            }
        }

        // Update user
        $updateData = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => $status,
            'profile_picture' => $profilePicture
        ];

        if ($password) {
            $updateData['password'] = $password;
        }

        $result = $this->userModel->update($id, $updateData);

        if ($result) {
            // Set success message
            setFlashMessage('success', 'User updated successfully.');

            // Redirect to user listing
            header('Location: /users');
            exit;
        } else {
            // Set error message
            $_SESSION['errors']['general'] = 'Failed to update user. Please try again.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /users/edit/' . $id);
            exit;
        }
    }

    /**
     * Display user details
     */
    public function view($id) {
        // Ensure admin access
        AdminMiddleware::check();

        // Get user by ID
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlashMessage('error', 'User not found.');
            header('Location: /users');
            exit;
        }

        // Get user statistics
        $stats = $this->userModel->getUserStats($id);

        // Include user details view
        include __DIR__ . '/../views/users/view.php';
    }

    /**
     * Delete user
     */
    public function delete($id) {
        // Ensure admin access
        AdminMiddleware::check();

        // Get user by ID
        $user = $this->userModel->findById($id);

        if (!$user) {
            setFlashMessage('error', 'User not found.');
            header('Location: /users');
            exit;
        }

        // Don't allow deleting admin users
        if ($user->role === 'admin') {
            setFlashMessage('error', 'Cannot delete admin users.');
            header('Location: /users');
            exit;
        }

        // Delete user
        $result = $this->userModel->delete($id);

        if ($result) {
            // Delete profile picture if exists
            if ($user->profile_picture) {
                deleteFile($user->profile_picture);
            }

            // Set success message
            setFlashMessage('success', 'User deleted successfully.');
        } else {
            // Set error message
            setFlashMessage('error', 'Failed to delete user. Please try again.');
        }

        // Redirect to user listing
        header('Location: /users');
        exit;
    }
}
