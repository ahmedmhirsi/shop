<?php
/**
 * Configuration File
 * Database and Application Settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Stock & Sales Management System');
define('APP_URL', 'http://localhost/shop');
define('APP_VERSION', '1.0.0');

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration
// Ces réglages ne peuvent être appliqués qu'AVANT le démarrage de la session.
// Si la session est déjà active (ex: session_start() appelé plus tôt dans index.php),
// on les ignore silencieusement pour éviter les warnings PHP.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
}

// File Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 2097152); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// CSRF Token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
