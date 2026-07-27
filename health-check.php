#!/usr/bin/env php
<?php
/**
 * System Health Check Script
 * Verifies all requirements and configurations are correct
 */

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║  Stock & Sales Management System v1.0.0 - Health Check    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$checks = [];
$errors = [];

// PHP Version Check
echo "[1/10] Checking PHP version...";
$php_version = phpversion();
if (version_compare($php_version, '7.4.0', '>=')) {
    echo " ✅ PHP $php_version\n";
    $checks[] = true;
} else {
    echo " ❌ PHP $php_version (requires 7.4+)\n";
    $checks[] = false;
    $errors[] = "PHP version is too old";
}

// PDO MySQL Extension Check
echo "[2/10] Checking PDO MySQL extension...";
if (extension_loaded('pdo_mysql')) {
    echo " ✅ Installed\n";
    $checks[] = true;
} else {
    echo " ❌ Not installed\n";
    $checks[] = false;
    $errors[] = "PDO MySQL extension not loaded";
}

// File System Permissions Check
echo "[3/10] Checking file permissions...";
$dirs_to_check = [
    'app' => 0o755,
    'public' => 0o755,
    'uploads' => 0o777,
    'logs' => 0o777
];

$perm_ok = true;
$APP_ROOT = __DIR__;

foreach ($dirs_to_check as $dir => $required_perm) {
    $path = $APP_ROOT . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($path)) {
        echo "\n   ⚠️  Directory missing: $dir\n";
        $perm_ok = false;
    } elseif (!is_writable($path)) {
        echo "\n   ⚠️  Not writable: $dir\n";
        $perm_ok = false;
    }
}

if ($perm_ok) {
    echo " ✅ All directories accessible\n";
    $checks[] = true;
} else {
    echo " ❌ Permission issues detected\n";
    $checks[] = false;
    $errors[] = "Directory permissions need adjustment";
}

// Configuration File Check
echo "[4/10] Checking configuration files...";
$config_files = ['config.php', 'index.php', 'init-db.php'];
$config_ok = true;

foreach ($config_files as $file) {
    if (!file_exists($APP_ROOT . DIRECTORY_SEPARATOR . $file)) {
        $config_ok = false;
    }
}

if ($config_ok) {
    echo " ✅ All files present\n";
    $checks[] = true;
} else {
    echo " ❌ Missing configuration files\n";
    $checks[] = false;
    $errors[] = "Configuration files missing";
}

// Database Connection Check
echo "[5/10] Checking database connection...";
require_once $APP_ROOT . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo " ✅ Connected to " . DB_NAME . "\n";
    $checks[] = true;
    $db_ok = true;
} catch (PDOException $e) {
    echo " ❌ " . $e->getMessage() . "\n";
    $checks[] = false;
    $errors[] = "Database connection failed";
    $db_ok = false;
}

// Database Tables Check
echo "[6/10] Checking database tables...";
if ($db_ok) {
    $required_tables = [
        'users', 'products', 'categories', 'suppliers', 'customers',
        'sales', 'sale_items', 'settings', 'stock_history'
    ];
    
    $missing_tables = [];
    foreach ($required_tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'")->fetch();
        if (!$result) {
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo " ✅ All tables present\n";
        $checks[] = true;
    } else {
        echo " ❌ Missing tables: " . implode(', ', $missing_tables) . "\n";
        $checks[] = false;
        $errors[] = "Database tables missing - run: php init-db.php";
    }
} else {
    echo " ⏭️  Skipped (DB connection failed)\n";
    $checks[] = false;
}

// Sample Data Check
echo "[7/10] Checking sample data...";
if ($db_ok) {
    $user_count = $pdo->query("SELECT COUNT(*) as cnt FROM users")->fetch()['cnt'];
    $product_count = $pdo->query("SELECT COUNT(*) as cnt FROM products")->fetch()['cnt'];
    
    if ($user_count > 0) {
        echo " ✅ Demo users configured ($user_count users)\n";
        $checks[] = true;
    } else {
        echo " ❌ No users found\n";
        $checks[] = false;
        $errors[] = "No demo users - run: php init-db.php";
    }
} else {
    echo " ⏭️  Skipped\n";
    $checks[] = false;
}

// Required Functions Check
echo "[8/10] Checking required PHP functions...";
$required_functions = [
    'password_hash', 'password_verify', 'hash_equals',
    'htmlspecialchars', 'session_start', 'json_encode'
];

$missing_functions = [];
foreach ($required_functions as $func) {
    if (!function_exists($func)) {
        $missing_functions[] = $func;
    }
}

if (empty($missing_functions)) {
    echo " ✅ All functions available\n";
    $checks[] = true;
} else {
    echo " ❌ Missing: " . implode(', ', $missing_functions) . "\n";
    $checks[] = false;
    $errors[] = "Required PHP functions not available";
}

// Session Configuration Check
echo "[9/10] Checking session configuration...";
$session_issues = [];

if (!ini_get('session.use_strict_mode')) {
    $session_issues[] = "session.use_strict_mode disabled";
}
if (!ini_get('session.cookie_httponly')) {
    $session_issues[] = "session.cookie_httponly disabled";
}

if (empty($session_issues)) {
    echo " ✅ Session security configured\n";
    $checks[] = true;
} else {
    echo " ⚠️  Issues: " . implode(', ', $session_issues) . "\n";
    $checks[] = true; // Non-critical warnings
}

// Summary
echo "[10/10] Summary...\n";
$pass_count = array_sum($checks);
$total_count = count($checks);
$health_percentage = round(($pass_count / $total_count) * 100);

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║  HEALTH CHECK SUMMARY                                      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Passed:    $pass_count/$total_count\n";
echo "💪 Status:    $health_percentage%\n\n";

if (!empty($errors)) {
    echo "❌ Issues Found:\n";
    foreach ($errors as $i => $error) {
        echo "   " . ($i + 1) . ". $error\n";
    }
    echo "\n";
}

if ($health_percentage === 100) {
    echo "🎉 System is fully operational!\n";
    echo "\n📝 Next Steps:\n";
    echo "   1. Start Apache: Start XAMPP Control Panel\n";
    echo "   2. Visit: http://localhost/shop_v2/\n";
    echo "   3. Login: admin / password\n";
    echo "\n";
    exit(0);
} elseif ($health_percentage >= 80) {
    echo "⚠️  System is mostly ready. Review issues above.\n\n";
    exit(0);
} else {
    echo "❌ Critical issues detected. Please resolve before using the system.\n\n";
    exit(1);
}
