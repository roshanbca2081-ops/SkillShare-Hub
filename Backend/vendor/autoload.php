<?php
/**
 * SkillShare Hub - PSR-4 Style Autoloader
 * Lightweight autoloader mapped to the Backend/ directory structure.
 *
 * Usage:
 *   require_once __DIR__ . '/vendor/autoload.php';
 */

spl_autoload_register(function ($class) {
    // Namespace prefix => base directory map (PSR-4)
    $prefixes = [
        'SkillShareHub\\Controllers\\' => __DIR__ . '/../controllers/',
        'SkillShareHub\\Models\\'       => __DIR__ . '/../models/',
        'SkillShareHub\\Services\\'     => __DIR__ . '/../services/',
        'SkillShareHub\\Middleware\\'   => __DIR__ . '/../middleware/',
        'SkillShareHub\\Helpers\\'      => __DIR__ . '/../helpers/',
        'SkillShareHub\\Database\\'     => __DIR__ . '/../database/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($prefix, $class, strlen($prefix)) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }

    // Fallback: legacy flat class names (AuthController, Database, Router, ...)
    $legacyPaths = [
        __DIR__ . '/../config/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../helpers/',
        __DIR__ . '/../middleware/',
        __DIR__ . '/../services/',
    ];

    foreach ($legacyPaths as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
