<?php
/**
 * Routes Configuration File
 * Defines application routes and their corresponding controllers
 */

// Route patterns
define('ROUTE_PATTERN', '/^\/?([^\/]+)?\/?([^\/]+)?\/?([^\/]+)?\/?([^\/]+)?$/');

// Route handlers
$routes = [
    // Authentication Routes
    'GET' => [
        'login' => ['AuthController', 'showLoginForm'],
        'register' => ['AuthController', 'showRegistrationForm'],
        'logout' => ['AuthController', 'logout'],
        'forgot-password' => ['AuthController', 'showForgotPasswordForm'],
        'reset-password' => ['AuthController', 'showResetPasswordForm'],
    ],

    'POST' => [
        'login' => ['AuthController', 'login'],
        'register' => ['AuthController', 'register'],
        'forgot-password' => ['AuthController', 'forgotPassword'],
        'reset-password' => ['AuthController', 'resetPassword'],
    ],

    // Dashboard Routes
    'GET' => [
        'dashboard' => ['DashboardController', 'index'],
        'dashboard/profile' => ['ProfileController', 'index'],
        'dashboard/settings' => ['SettingController', 'index'],
    ],

    // User Management Routes
    'GET' => [
        'users' => ['UserController', 'index'],
        'users/create' => ['UserController', 'create'],
        'users/edit/{id}' => ['UserController', 'edit'],
        'users/view/{id}' => ['UserController', 'view'],
    ],

    'POST' => [
        'users/store' => ['UserController', 'store'],
        'users/update/{id}' => ['UserController', 'update'],
        'users/delete/{id}' => ['UserController', 'delete'],
    ],

    // Academic Field Routes
    'GET' => [
        'academic-fields' => ['AcademicFieldController', 'index'],
        'academic-fields/create' => ['AcademicFieldController', 'create'],
        'academic-fields/edit/{id}' => ['AcademicFieldController', 'edit'],
        'academic-fields/view/{id}' => ['AcademicFieldController', 'view'],
    ],

    'POST' => [
        'academic-fields/store' => ['AcademicFieldController', 'store'],
        'academic-fields/update/{id}' => ['AcademicFieldController', 'update'],
        'academic-fields/delete/{id}' => ['AcademicFieldController', 'delete'],
    ],

    // Course Routes
    'GET' => [
        'courses' => ['CourseController', 'index'],
        'courses/create' => ['CourseController', 'create'],
        'courses/edit/{id}' => ['CourseController', 'edit'],
        'courses/view/{id}' => ['CourseController', 'view'],
        'courses/enroll/{id}' => ['CourseController', 'enroll'],
    ],

    'POST' => [
        'courses/store' => ['CourseController', 'store'],
        'courses/update/{id}' => ['CourseController', 'update'],
        'courses/delete/{id}' => ['CourseController', 'delete'],
        'courses/enroll' => ['CourseController', 'processEnrollment'],
    ],

    // Booking Routes
    'GET' => [
        'bookings' => ['BookingController', 'index'],
        'bookings/create' => ['BookingController', 'create'],
        'bookings/edit/{id}' => ['BookingController', 'edit'],
        'bookings/view/{id}' => ['BookingController', 'view'],
    ],

    'POST' => [
        'bookings/store' => ['BookingController', 'store'],
        'bookings/update/{id}' => ['BookingController', 'update'],
        'bookings/delete/{id}' => ['BookingController', 'delete'],
        'bookings/approve/{id}' => ['BookingController', 'approve'],
        'bookings/reject/{id}' => ['BookingController', 'reject'],
    ],

    // Session Routes
    'GET' => [
        'sessions' => ['SessionController', 'index'],
        'sessions/create' => ['SessionController', 'create'],
        'sessions/edit/{id}' => ['SessionController', 'edit'],
        'sessions/view/{id}' => ['SessionController', 'view'],
    ],

    'POST' => [
        'sessions/store' => ['SessionController', 'store'],
        'sessions/update/{id}' => ['SessionController', 'update'],
        'sessions/delete/{id}' => ['SessionController', 'delete'],
    ],

    // Assignment Routes
    'GET' => [
        'assignments' => ['AssignmentController', 'index'],
        'assignments/create' => ['AssignmentController', 'create'],
        'assignments/edit/{id}' => ['AssignmentController', 'edit'],
        'assignments/view/{id}' => ['AssignmentController', 'view'],
    ],

    'POST' => [
        'assignments/store' => ['AssignmentController', 'store'],
        'assignments/update/{id}' => ['AssignmentController', 'update'],
        'assignments/delete/{id}' => ['AssignmentController', 'delete'],
        'assignments/submit/{id}' => ['AssignmentController', 'submit'],
    ],

    // Research Routes
    'GET' => [
        'research' => ['ResearchController', 'index'],
        'research/create' => ['ResearchController', 'create'],
        'research/edit/{id}' => ['ResearchController', 'edit'],
        'research/view/{id}' => ['ResearchController', 'view'],
    ],

    'POST' => [
        'research/store' => ['ResearchController', 'store'],
        'research/update/{id}' => ['ResearchController', 'update'],
        'research/delete/{id}' => ['ResearchController', 'delete'],
    ],

    // Interview Routes
    'GET' => [
        'interviews' => ['InterviewController', 'index'],
        'interviews/create' => ['InterviewController', 'create'],
        'interviews/edit/{id}' => ['InterviewController', 'edit'],
        'interviews/view/{id}' => ['InterviewController', 'view'],
    ],

    'POST' => [
        'interviews/store' => ['InterviewController', 'store'],
        'interviews/update/{id}' => ['InterviewController', 'update'],
        'interviews/delete/{id}' => ['InterviewController', 'delete'],
    ],

    // Certificate Routes
    'GET' => [
        'certificates' => ['CertificateController', 'index'],
        'certificates/create' => ['CertificateController', 'create'],
        'certificates/edit/{id}' => ['CertificateController', 'edit'],
        'certificates/view/{id}' => ['CertificateController', 'view'],
    ],

    'POST' => [
        'certificates/store' => ['CertificateController', 'store'],
        'certificates/update/{id}' => ['CertificateController', 'update'],
        'certificates/delete/{id}' => ['CertificateController', 'delete'],
        'certificates/generate/{id}' => ['CertificateController', 'generate'],
    ],

    // Payment Routes
    'GET' => [
        'payments' => ['PaymentController', 'index'],
        'payments/create' => ['PaymentController', 'create'],
        'payments/edit/{id}' => ['PaymentController', 'edit'],
        'payments/view/{id}' => ['PaymentController', 'view'],
    ],

    'POST' => [
        'payments/store' => ['PaymentController', 'store'],
        'payments/update/{id}' => ['PaymentController', 'update'],
        'payments/delete/{id}' => ['PaymentController', 'delete'],
        'payments/process' => ['PaymentController', 'processPayment'],
    ],

    // Message Routes
    'GET' => [
        'messages' => ['MessageController', 'index'],
        'messages/create' => ['MessageController', 'create'],
        'messages/edit/{id}' => ['MessageController', 'edit'],
        'messages/view/{id}' => ['MessageController', 'view'],
    ],

    'POST' => [
        'messages/store' => ['MessageController', 'store'],
        'messages/update/{id}' => ['MessageController', 'update'],
        'messages/delete/{id}' => ['MessageController', 'delete'],
    ],

    // Notification Routes
    'GET' => [
        'notifications' => ['NotificationController', 'index'],
        'notifications/create' => ['NotificationController', 'create'],
        'notifications/edit/{id}' => ['NotificationController', 'edit'],
        'notifications/view/{id}' => ['NotificationController', 'view'],
    ],

    'POST' => [
        'notifications/store' => ['NotificationController', 'store'],
        'notifications/update/{id}' => ['NotificationController', 'update'],
        'notifications/delete/{id}' => ['NotificationController', 'delete'],
    ],

    // Search Routes
    'GET' => [
        'search' => ['SearchController', 'index'],
        'search/results' => ['SearchController', 'results'],
    ],

    'POST' => [
        'search' => ['SearchController', 'search'],
    ],

    // Contact Routes
    'GET' => [
        'contact' => ['ContactController', 'index'],
    ],

    'POST' => [
        'contact' => ['ContactController', 'submit'],
    ],
];

// API Routes
$apiRoutes = [
    'GET' => [
        'api/auth/user' => ['AuthController', 'getCurrentUser'],
        'api/courses' => ['CourseController', 'apiIndex'],
        'api/fields' => ['AcademicFieldController', 'apiIndex'],
    ],

    'POST' => [
        'api/auth/login' => ['AuthController', 'apiLogin'],
        'api/auth/register' => ['AuthController', 'apiRegister'],
        'api/courses/search' => ['CourseController', 'apiSearch'],
    ],
];

// Helper function to match routes
function matchRoute($method, $path) {
    global $routes, $apiRoutes;

    // Check API routes first
    if (strpos($path, '/api/') === 0) {
        $path = substr($path, 4); // Remove '/api' prefix
        $routeArray = $apiRoutes;
    } else {
        $routeArray = $routes;
    }

    if (!isset($routeArray[$method])) {
        return null;
    }

    // Check for exact match first
    if (isset($routeArray[$method][$path])) {
        return $routeArray[$method][$path];
    }

    // Check for pattern matches
    foreach ($routeArray[$method] as $route => $handler) {
        // Convert route to regex pattern
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $path, $matches)) {
            // Extract parameters
            $params = [];
            for ($i = 1; $i < count($matches); $i++) {
                $params[] = $matches[$i];
            }

            return [
                'handler' => $handler,
                'params' => $params
            ];
        }
    }

    return null;
}
