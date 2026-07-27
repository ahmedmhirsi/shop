<?php
/**
 * Security Helper Class
 */

class SecurityHelper {
    
    public static function escapeHtml($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return trim(strip_tags($data));
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isAuthorized($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    public static function generateInvoiceNumber() {
        return 'INV-' . date('Ymd') . '-' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
