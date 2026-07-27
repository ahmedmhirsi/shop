<?php
/**
 * Base Controller Class
 */

class Controller {
    protected $user;
    protected $route;
    protected $method;

    public function __construct() {
        if (SecurityHelper::isAuthenticated()) {
            $userModel = new User();
            $this->user = $userModel->findById($_SESSION['user_id']);
        }
        $this->route = $_GET['url'] ?? 'dashboard';
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    protected function requireAuth() {
        if (!SecurityHelper::isAuthenticated()) {
            header('Location: /shop_v2/index.php?url=login');
            exit;
        }
    }

    protected function requireRole($role) {
        $this->requireAuth();
        if (!SecurityHelper::isAuthorized($role)) {
            header('HTTP/1.1 403 Forbidden');
            die('Access Denied');
        }
    }

    protected function render($view, $data = []) {
        extract($data);
        include APP_DIR . '/Views/' . $view . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    protected function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    protected function getPostData() {
        return $_POST;
    }

    protected function hasPostData($key) {
        return isset($_POST[$key]);
    }

    protected function getPostValue($key, $default = '') {
        return $_POST[$key] ?? $default;
    }
}
