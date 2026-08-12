<?php
/**
 * Routes Configuration File
 * Defines the Router class, middleware loading, and application route registrations
 */

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/AdminMiddleware.php';
require_once __DIR__ . '/../middleware/MentorMiddleware.php';
require_once __DIR__ . '/../middleware/FresherMiddleware.php';
require_once __DIR__ . '/../middleware/GuestMiddleware.php';

// ============================================
// ROUTER - REQUEST HANDLING
// ============================================

class Router
{
    private static $routes = [];
    private static $middlewares = [];

    public static function get($path, $callback, $middlewares = [])
    {
        self::$routes['GET'][$path] = $callback;
        if (!empty($middlewares)) {
            self::$middlewares['GET'][$path] = $middlewares;
        }
    }

    public static function post($path, $callback, $middlewares = [])
    {
        self::$routes['POST'][$path] = $callback;
        if (!empty($middlewares)) {
            self::$middlewares['POST'][$path] = $middlewares;
        }
    }

    public static function put($path, $callback, $middlewares = [])
    {
        self::$routes['PUT'][$path] = $callback;
        if (!empty($middlewares)) {
            self::$middlewares['PUT'][$path] = $middlewares;
        }
    }

    public static function delete($path, $callback, $middlewares = [])
    {
        self::$routes['DELETE'][$path] = $callback;
        if (!empty($middlewares)) {
            self::$middlewares['DELETE'][$path] = $middlewares;
        }
    }

    public static function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('/shareskill-hub/', '', $uri);
        $uri = trim($uri, '/');

        // Check for route match
        foreach (self::$routes[$method] ?? [] as $path => $callback) {
            $pattern = '#^' . preg_replace('/:([a-z]+)/', '([^/]+)', $path) . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                // Check middlewares
                if (isset(self::$middlewares[$method][$path])) {
                    foreach (self::$middlewares[$method][$path] as $middleware) {
                        $middleware::handle();
                    }
                }

                // Call callback
                if (is_array($callback)) {
                    $controller = new $callback[0]();
                    call_user_func_array([$controller, $callback[1]], $matches);
                } else {
                    call_user_func_array($callback, $matches);
                }
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
    }
}

// ============================================
// REGISTER ROUTES
// ============================================

// Public routes
Router::get('', [HomeController::class, 'index']);
Router::get('about', [HomeController::class, 'about']);
Router::get('contact', [HomeController::class, 'contact']);
Router::post('contact', [HomeController::class, 'sendContact']);

// Auth routes
Router::get('login', [AuthController::class, 'loginForm'], [GuestMiddleware::class]);
Router::post('login', [AuthController::class, 'login'], [GuestMiddleware::class]);
Router::get('register', [AuthController::class, 'registerForm'], [GuestMiddleware::class]);
Router::post('register', [AuthController::class, 'register'], [GuestMiddleware::class]);
Router::get('logout', [AuthController::class, 'logout']);
Router::get('verify/:token', [AuthController::class, 'verify']);
Router::get('forgot-password', [AuthController::class, 'forgotPasswordForm'], [GuestMiddleware::class]);
Router::post('forgot-password', [AuthController::class, 'forgotPassword'], [GuestMiddleware::class]);
Router::get('reset-password/:token', [AuthController::class, 'resetPasswordForm'], [GuestMiddleware::class]);
Router::post('reset-password', [AuthController::class, 'resetPassword'], [GuestMiddleware::class]);

// Academic Field routes
Router::get('academic-fields', [AcademicFieldController::class, 'index']);
Router::get('academic-fields/:slug', [AcademicFieldController::class, 'show']);

// Course routes
Router::get('courses', [CourseController::class, 'index']);
Router::get('courses/:slug', [CourseController::class, 'show']);
Router::post('courses/enroll', [CourseController::class, 'enroll'], [FresherMiddleware::class]);

// Mentor routes
Router::get('mentors', [MentorController::class, 'index']);
Router::get('mentors/:id', [MentorController::class, 'show']);
Router::get('mentor/dashboard', [MentorController::class, 'dashboard'], [MentorMiddleware::class]);
Router::get('mentor/availability', [MentorController::class, 'availability'], [MentorMiddleware::class]);
Router::post('mentor/availability', [MentorController::class, 'availability'], [MentorMiddleware::class]);
Router::get('mentor/earnings', [MentorController::class, 'earnings'], [MentorMiddleware::class]);

// Booking routes
Router::post('bookings', [BookingController::class, 'store'], [FresherMiddleware::class]);
Router::put('bookings/:id/accept', [BookingController::class, 'accept'], [MentorMiddleware::class]);
Router::put('bookings/:id/reject', [BookingController::class, 'reject'], [MentorMiddleware::class]);
Router::put('bookings/:id/cancel', [BookingController::class, 'cancel']);
Router::get('mentor/bookings', [BookingController::class, 'mentorBookings'], [MentorMiddleware::class]);
Router::get('fresher/bookings', [BookingController::class, 'fresherBookings'], [FresherMiddleware::class]);

// Fresher routes
Router::get('fresher/dashboard', [FresherController::class, 'dashboard'], [FresherMiddleware::class]);
Router::get('fresher/profile', [FresherController::class, 'profile'], [FresherMiddleware::class]);

// Admin routes
Router::get('admin', [AdminController::class, 'dashboard'], [AdminMiddleware::class]);
Router::get('admin/users', [AdminController::class, 'users'], [AdminMiddleware::class]);
Router::put('admin/users/:id', [AdminController::class, 'userUpdate'], [AdminMiddleware::class]);
Router::delete('admin/users/:id', [AdminController::class, 'userDelete'], [AdminMiddleware::class]);
Router::put('admin/mentors/:id/verify', [AdminController::class, 'verifyMentor'], [AdminMiddleware::class]);
Router::put('admin/mentors/:id/suspend', [AdminController::class, 'suspendMentor'], [AdminMiddleware::class]);

// Profile routes
Router::get('profile', [ProfileController::class, 'show']);
Router::post('profile/update', [ProfileController::class, 'update']);
Router::post('profile/password', [ProfileController::class, 'updatePassword']);

// API routes
Router::get('api/fields', function() {
    $db = Database::getInstance();
    $fields = $db->fetchAll("SELECT * FROM academic_fields WHERE status = 'active' ORDER BY name");
    successResponse($fields);
});

Router::get('api/courses', function() {
    $db = Database::getInstance();
    $fieldId = $_GET['field_id'] ?? null;
    $sql = "SELECT * FROM courses WHERE status = 'active'";
    $params = [];
    if ($fieldId) {
        $sql .= " AND academic_field_id = ?";
        $params[] = $fieldId;
    }
    $sql .= " ORDER BY name";
    $courses = $db->fetchAll($sql, $params);
    successResponse($courses);
});

Router::get('api/mentors', function() {
    $db = Database::getInstance();
    $mentors = $db->fetchAll(
        "SELECT u.id, u.full_name, u.email, u.profile_picture, u.bio, u.hourly_rate, m.specialization, m.experience_years, m.is_verified, m.rating, m.reviews_count, f.name as field_name FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field = f.id WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1 ORDER BY m.rating DESC LIMIT 20"
    );
    successResponse($mentors);
});

Router::get('api/skills/:course_id', function($courseId) {
    $db = Database::getInstance();
    $skills = $db->fetchAll(
        "SELECT * FROM skills WHERE course_id = ? AND status = 'active' ORDER BY name",
        [$courseId]
    );
    successResponse($skills);
});

Router::get('api/stats', function() {
    $db = Database::getInstance();
    $stats = [
        'fields' => $db->count('academic_fields', "status = 'active'"),
        'courses' => $db->count('courses', "status = 'active'"),
        'mentors' => $db->count('users', "role = 'mentor' AND status = 'active'"),
        'freshers' => $db->count('users', "role = 'fresher' AND status = 'active'"),
        'sessions' => $db->count('sessions', "status = 'completed'"),
        'skills' => $db->count('skills', "status = 'active'")
    ];
    successResponse($stats);
});

// ============================================
// LEgacy route table (backward compatibility)
// ============================================

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

// Helper function to match routes (backward compatibility)
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
