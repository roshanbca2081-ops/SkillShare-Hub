# SkillShare Hub — Backend

The backend for the SkillShare Hub mentoring platform: a native PHP (no framework) MVC-style application.

## Requirements

- PHP 7.4+ (PDO, pdo_mysql, fileinfo enabled)
- MySQL 5.7+ / MariaDB 10.2+
- Apache with `mod_rewrite` (XAMPP recommended)

## Installation

1. Copy the project into `C:\xampp\htdocs\SkillShare-Hub`.
2. Create the database and load the schema:

   ```sql
   CREATE DATABASE skillshare_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Then import `database/schema.sql`.

3. Configure the database connection in `Backend/config/config.php`:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'skillshare_hub');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. Point your browser at:

   ```
   http://localhost/SkillShare-Hub/Backend/
   ```

## Directory Structure

```
Backend/
├── index.php              Front controller — every request enters here
├── .htaccess              Rewrites all requests to index.php
├── config/                App config, constants, database, routes, mail
├── controllers/           Auth, Home, Mentor, Fresher, Admin, ... Controllers
├── models/                Database models (User, Mentor, Booking, ...)
├── middleware/            Auth/Admin/Mentor/Fresher/Guest guards
├── helpers/               functions, validation, upload, csrf, responses, ...
├── services/              AuthService, MailService, PaymentService, ...
├── api/                   Versioned API endpoints (api/v1/...)
├── views/                 HTML templates (layout, auth, dashboards, ...)
├── storage/               Logs, cache, temp, exports
├── uploads/               User-uploaded content (profiles, courses, ...)
├── database/              Schema SQL, migrations, seeders
└── vendor/                Lightweight autoloader
```

## Routing

`Backend/.htaccess` rewrites every non-file request to `index.php`, which boots the
config and calls `Router::run()`. Routes are registered in `Backend/config/routes.php`:

```php
Router::get('courses', [CourseController::class, 'index']);
Router::post('bookings', [BookingController::class, 'store'], [FresherMiddleware::class]);
Router::get('verify/:token', [AuthController::class, 'verify']);
```

Dynamic segments are declared with `:param` and are passed positionally to the
controller method.

## API

Versioned endpoints are served from `Backend/api/v1/`:

```
http://localhost/SkillShare-Hub/Backend/api/v1/courses.php
http://localhost/SkillShare-Hub/Backend/api/v1/mentors.php?id=5
```

All API responses follow the wrapper:

```json
{
  "success": true,
  "message": "Success",
  "data": { }
}
```

## Key Conventions

- PDO prepared statements everywhere (`Database::getInstance()`).
- `e()` for output escaping, `sanitize()` for input.
- Flash messages via `setFlash()` / `getFlash()`.
- CSRF tokens on every POST via `checkCSRF()` / `csrfField()`.
- Role guards: `AuthMiddleware`, `AdminMiddleware`, `MentorMiddleware`, `FresherMiddleware`, `GuestMiddleware`.
