<?php
/**
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
}
