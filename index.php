<?php
/**
<<<<<<< HEAD
 * Stock & Sales Management System v1.0.0
 * Front Controller / Router
 */

define('APP_DIR', __DIR__ . '/app');
define('PUBLIC_DIR', __DIR__ . '/public');

require_once __DIR__ . '/config.php';
require_once APP_DIR . '/Database.php';
require_once APP_DIR . '/Helpers/SecurityHelper.php';
require_once APP_DIR . '/Helpers/FormatterHelper.php';

require_once APP_DIR . '/Models/User.php';
require_once APP_DIR . '/Models/Product.php';
require_once APP_DIR . '/Models/Sale.php';
require_once APP_DIR . '/Models/SaleItem.php';
require_once APP_DIR . '/Models/Reference.php';

require_once APP_DIR . '/Controllers/Controller.php';
require_once APP_DIR . '/Controllers/AuthController.php';
require_once APP_DIR . '/Controllers/DashboardController.php';
require_once APP_DIR . '/Controllers/POSController.php';
require_once APP_DIR . '/Controllers/ProductController.php';

$url = $_GET['url'] ?? 'login';
$parts = explode('/', $url);
$controller = $parts[0] ?? 'login';
$method = $parts[1] ?? 'index';

switch ($controller) {
    case 'login':
        $ctrl = new AuthController();
        $ctrl->login();
        break;
    case 'logout':
        $ctrl = new AuthController();
        $ctrl->logout();
        break;
    case 'register':
        $ctrl = new AuthController();
        $ctrl->register();
        break;
    case 'dashboard':
        $ctrl = new DashboardController();
        if (method_exists($ctrl, $method)) {
            $ctrl->$method();
        } else {
            $ctrl->index();
        }
        break;
    case 'pos':
        $ctrl = new POSController();
        if (method_exists($ctrl, $method)) {
            $ctrl->$method();
        } else {
            $ctrl->index();
        }
        break;
    case 'products':
        $ctrl = new ProductController();
        if (method_exists($ctrl, $method)) {
            $ctrl->$method();
        } else {
            $ctrl->index();
        }
        break;
    default:
        header('HTTP/1.1 404 Not Found');
        die('Page not found');
=======
 * Main Entry Point
 * Routes to different pages based on query parameter
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Check authentication
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Get current page
$page = $_GET['page'] ?? 'dashboard';

// Route to appropriate page
switch ($page) {
    case 'dashboard':
        require_login();
        if (!is_boss()) {
            header('Location: index.php?page=pos');
            exit;
        }
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'products':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController();
        $action = $_GET['action'] ?? 'index';
        $controller->$action();
        break;
        
    case 'categories':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/CategoryController.php';
        $controller = new CategoryController();
        $action = $_GET['action'] ?? 'index';
        $controller->$action();
        break;
        
    case 'suppliers':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/SupplierController.php';
        $controller = new SupplierController();
        $action = $_GET['action'] ?? 'index';
        $controller->$action();
        break;
        
    case 'pos':
        require_login();
        require_once __DIR__ . '/controllers/POSController.php';
        $controller = new POSController();
        $action = $_GET['action'] ?? 'index';
        if (!method_exists($controller, $action)) {
            $action = 'index';
        }
        $controller->$action();
        break;
        
    case 'reports':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController();
        $action = $_GET['action'] ?? 'index';
        $controller->$action();
        break;
        
    case 'settings':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/SettingsController.php';
        $controller = new SettingsController();
        $action = $_GET['action'] ?? 'index';
        $controller->$action();
        break;
        
    case 'sales':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/SaleController.php';
        $controller = new SaleController();
        $action = $_GET['action'] ?? 'index';
        $controller->$action();
        break;
        
    case 'stock':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/StockController.php';
        $controller = new StockController();
        $controller->index();
        break;

    case 'shift_history':
        require_login();
        require_boss();
        require_once __DIR__ . '/controllers/ShiftHistoryController.php';
        $controller = new ShiftHistoryController();
        $controller->index();
        break;
        
    default:
        // Redirect to dashboard
        if (is_boss()) {
            header('Location: index.php?page=dashboard');
        } else {
            header('Location: index.php?page=pos');
        }
        exit;
>>>>>>> b47ee5eba4e058640b479010b7719ba3976e48d5
}
