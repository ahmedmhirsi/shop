<?php
/**
 * Common Functions
 * Utility functions used throughout the application
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    die('Direct access not permitted');
}

/**
 * Sanitize input data
 * @param mixed $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate CSRF token
 * @param string $token
 * @return bool
 */
function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token input
 * @return string
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is boss/admin
 * @return bool
 */
function is_boss() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'boss';
}

/**
 * Check if user is employee
 * @return bool
 */
function is_employee() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'employee';
}

/**
 * Require login
 * Redirects to login page if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require boss role
 * Redirects to dashboard if not boss
 */
function require_boss() {
    require_login();
    if (!is_boss()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Format currency
 * @param float $amount
 * @param string $currency
 * @return string
 */
function format_currency($amount, $currency = 'TND ') {
    return $currency . number_format($amount, 2);
}

/**
 * Format date
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Generate invoice number
 * @param string $prefix
 * @return string
 */
function generate_invoice_number($prefix = 'INV-') {
    return $prefix . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Generate barcode
 * @return string
 */
function generate_barcode() {
    return str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
}

/**
 * Upload file
 * @param array $file
 * @param string $path
 * @return string|false
 */
function upload_file($file, $path) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return false;
    }

    $filename = uniqid() . '.' . $ext;
    $destination = $path . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }

    return false;
}

/**
 * Delete file
 * @param string $filename
 * @param string $path
 * @return bool
 */
function delete_file($filename, $path) {
    $file = $path . $filename;
    if (file_exists($file)) {
        return unlink($file);
    }
    return false;
}

/**
 * Set flash message
 * @param string $type
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect with flash message
 * @param string $url
 * @param string $type
 * @param string $message
 */
function redirect_with_flash($url, $type, $message) {
    set_flash($type, $message);
    header('Location: ' . $url);
    exit;
}

/**
 * JSON response
 * @param array $data
 * @param int $status_code
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get current date and time
 * @return string
 */
function now() {
    return date('Y-m-d H:i:s');
}

/**
 * Calculate profit
 * @param float $selling_price
 * @param float $buying_price
 * @param int $quantity
 * @return float
 */
function calculate_profit($selling_price, $buying_price, $quantity = 1) {
    return ($selling_price - $buying_price) * $quantity;
}

/**
 * Pagination helper
 * @param int $total
 * @param int $current_page
 * @param int $per_page
 * @return array
 */
function paginate($total, $current_page, $per_page = ITEMS_PER_PAGE) {
    $total_pages = ceil($total / $per_page);
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'has_next' => $current_page < $total_pages,
        'has_prev' => $current_page > 1
    ];
}

/**
 * Get pagination links
 * @param array $pagination
 * @param string $url
 * @return string
 */
function pagination_links($pagination, $url, $pageParam = 'page_num') {
    $html = '<div class="pagination">';
    $separator = strpos($url, '?') === false ? '?' : '&';
    
    if ($pagination['has_prev']) {
        $html .= '<a href="' . $url . $separator . $pageParam . '=' . ($pagination['current_page'] - 1) . '" class="btn">Previous</a>';
    }
    
    $html .= '<span>Page ' . $pagination['current_page'] . ' of ' . $pagination['total_pages'] . '</span>';
    
    if ($pagination['has_next']) {
        $html .= '<a href="' . $url . $separator . $pageParam . '=' . ($pagination['current_page'] + 1) . '" class="btn">Next</a>';
    }
    
    $html .= '</div>';
    return $html;
}
