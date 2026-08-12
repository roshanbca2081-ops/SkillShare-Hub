<?php
/**
 * Constants File
 * Contains application-wide constants and configuration values
 */

// Application Environment
define('APP_ENV', ENVIRONMENT ?? 'development');
define('APP_DEBUG', APP_ENV === 'development');

// Application Configuration
define('APP_NAME', 'ShareSkill Hub');
define('APP_URL', SITE_URL . '/');
define('APP_VERSION', '1.0.0');

// Security
define('PASSWORD_BCRYPT_ROUNDS', 12);
define('CSRF_TOKEN_LIFETIME', 3600);
define('SESSION_LIFETIME', 3600);
define('REMEMBER_ME_LIFETIME', 2592000);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Date Formats
define('DATE_DISPLAY', 'F j, Y');
define('TIME_DISPLAY', 'g:i A');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('CONTROLLER_PATH', BASE_PATH . '/controllers');
define('MODEL_PATH', BASE_PATH . '/models');
define('VIEW_PATH', BASE_PATH . '/views');
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('STORAGE_PATH', BASE_PATH . '/storage');

// URL Paths
define('BASE_URL', SITE_URL . '/Backend');
define('ASSETS_URL', SITE_URL . '/frontend/assets');
define('UPLOAD_URL', SITE_URL . '/uploads');

// Session Keys
define('SESSION_USER_KEY', 'user_data');
define('SESSION_FLASH_KEY', 'flash_messages');
define('SESSION_ERRORS_KEY', 'validation_errors');

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_MENTOR', 'mentor');
define('ROLE_FRESHER', 'fresher');

// Status Constants
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');

// Pagination
define('DEFAULT_PAGE', 1);
define('DEFAULT_LIMIT', 10);

// Date Formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'F j, Y');
define('DISPLAY_DATETIME_FORMAT', 'F j, Y, g:i a');

// File Types
define('IMAGE_MIME_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('DOCUMENT_MIME_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// API Response Codes
define('API_SUCCESS', 200);
define('API_CREATED', 201);
define('API_BAD_REQUEST', 400);
define('API_UNAUTHORIZED', 401);
define('API_FORBIDDEN', 403);
define('API_NOT_FOUND', 404);
define('API_METHOD_NOT_ALLOWED', 405);
define('API_VALIDATION_ERROR', 422);
define('API_SERVER_ERROR', 500);

// Password Settings
define('MIN_PASSWORD_LENGTH', 8);
define('PASSWORD_REGEX', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/');

// Time Intervals (in seconds)
define('MINUTE', 60);
define('HOUR', 3600);
define('DAY', 86400);
define('WEEK', 604800);
define('MONTH', 2592000);
define('YEAR', 31536000);

// Cache Keys
define('CACHE_PREFIX', 'skillsharehub_');
define('CACHE_USER_PREFIX', CACHE_PREFIX . 'user_');
define('CACHE_COURSE_PREFIX', CACHE_PREFIX . 'course_');

// Rate Limiting
define('RATE_LIMIT_WINDOW', 60); // 1 minute
define('RATE_LIMIT_ATTEMPTS', 30); // 30 requests per minute

// File Size Limits (in bytes)
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_DOCUMENT_SIZE', 20 * 1024 * 1024); // 20MB
