<?php
/**
 * Stock & Sales Management System v1.0.0
 * Configuration File
 * 
 * Database connection and application settings
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'stock_management');

define('APP_NAME', 'Stock & Sales Management');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/shop_v2');
define('APP_ENV', 'production');

define('CURRENCY', 'TND');
define('TAX_RATE', 0.00);

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('LOGS_DIR', __DIR__ . '/logs/');

if (php_sapi_name() !== 'cli' && !headers_sent()) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/shop_v2/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

if (session_status() === PHP_SESSION_NONE && php_sapi_name() !== 'cli') {
    session_start();
}

