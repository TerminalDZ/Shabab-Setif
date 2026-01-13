<?php
/**
 * Shabab Setif - Application Configuration
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Application Settings
define('APP_NAME', 'شباب سطيف');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://chababsetif.diba');
define('APP_ENV', 'development'); // development | production

// Session Configuration
define('SESSION_NAME', 'shabab_session');
define('SESSION_LIFETIME', 7200); // 2 hours

// Security Settings
define('CSRF_TOKEN_NAME', '_csrf_token');
define('PASSWORD_ALGO', PASSWORD_ARGON2ID);
define('PASSWORD_OPTIONS', [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 1
]);

// Member Card Configuration
define('CARD_PREFIX', 'SS');
define('CARD_YEAR', date('Y'));

// Points System Defaults
define('POINTS_ACTIVITY_DEFAULT', 10);
define('POINTS_OFFICE_VISIT', 5);
define('POINTS_SOCIAL_INTERACTION', 3);

// File Upload Settings
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Email Configuration (Mailpit via DDEV)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 1025);
define('SMTP_AUTH', false);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@shababsetif.org');
define('SMTP_FROM_NAME', 'شباب سطيف');

// Role Definitions
define('ROLE_ADMIN', 'admin');
define('ROLE_HEAD', 'head');
define('ROLE_MEMBER', 'member');

// Timezone
date_default_timezone_set('Africa/Algiers');

// Error Reporting (based on environment)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start([
        'cookie_lifetime' => SESSION_LIFETIME,
        'cookie_httponly' => true,
        'cookie_secure' => APP_ENV !== 'development', // false in dev
        'cookie_samesite' => 'Lax'
    ]);
}

