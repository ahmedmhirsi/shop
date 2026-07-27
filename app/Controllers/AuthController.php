<?php
/**
 * Authentication Controller
 */

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function login() {
        if ($this->method === 'POST') {
            $csrf_token = $this->getPostValue('csrf_token');
            if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
                $this->render('auth/login', ['error' => 'Security token expired. Please try again.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
                return;
            }

            $username = SecurityHelper::sanitizeInput($this->getPostValue('username'));
            $password = $this->getPostValue('password');

            $user = $this->userModel->login($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $this->redirect('/shop_v2/index.php?url=dashboard');
            } else {
                $this->render('auth/login', ['error' => 'Invalid credentials.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
            }
        } else {
            if (SecurityHelper::isAuthenticated()) {
                $this->redirect('/shop_v2/index.php?url=dashboard');
            }
            $this->render('auth/login', ['csrf_token' => SecurityHelper::generateCsrfToken()]);
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/shop_v2/index.php?url=login');
    }

    public function register() {
        $this->requireRole('boss');

        if ($this->method === 'POST') {
            $csrf_token = $this->getPostValue('csrf_token');
            if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
                $this->render('auth/register', ['error' => 'Security token expired.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
                return;
            }

            $username = SecurityHelper::sanitizeInput($this->getPostValue('username'));
            $password = $this->getPostValue('password');
            $confirm_password = $this->getPostValue('confirm_password');
            $full_name = SecurityHelper::sanitizeInput($this->getPostValue('full_name'));
            $role = in_array($this->getPostValue('role'), ['boss', 'employee']) ? $this->getPostValue('role') : 'employee';

            if (strlen($username) < 3) {
                $this->render('auth/register', ['error' => 'Username must be at least 3 characters.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
                return;
            }

            if (strlen($password) < 6) {
                $this->render('auth/register', ['error' => 'Password must be at least 6 characters.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
                return;
            }

            if ($password !== $confirm_password) {
                $this->render('auth/register', ['error' => 'Passwords do not match.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
                return;
            }

            if ($this->userModel->findByUsername($username)) {
                $this->render('auth/register', ['error' => 'Username already exists.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
                return;
            }

            $hashed_password = SecurityHelper::hashPassword($password);
            if ($this->userModel->create($username, $hashed_password, $full_name, $role)) {
                $this->render('auth/register', ['success' => 'User created successfully.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
            } else {
                $this->render('auth/register', ['error' => 'Error creating user.', 'csrf_token' => SecurityHelper::generateCsrfToken()]);
            }
        } else {
            $this->render('auth/register', ['csrf_token' => SecurityHelper::generateCsrfToken()]);
        }
    }
}
