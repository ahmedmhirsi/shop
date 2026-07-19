<?php
/**
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
}
