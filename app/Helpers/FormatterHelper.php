<?php
/**
 * Formatter Helper Class
 */

class FormatterHelper {
    
    public static function formatCurrency($amount, $currency = 'TND') {
        return number_format($amount, 2, '.', ',') . ' ' . $currency;
    }

    public static function formatDate($date, $format = 'd/m/Y H:i') {
        if (is_string($date)) {
            $date = strtotime($date);
        }
        return date($format, $date);
    }

    public static function formatQuantity($quantity) {
        if ($quantity == intval($quantity)) {
            return (int)$quantity;
        }
        return number_format($quantity, 3, '.', '');
    }

    public static function calculateProfit($selling_price, $buying_price, $quantity) {
        return ($selling_price - $buying_price) * $quantity;
    }

    public static function calculateChange($total, $amount_received) {
        $change = $amount_received - $total;
        return $change > 0 ? $change : 0;
    }

    public static function truncateText($text, $length = 50) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length) . '...';
        }
        return $text;
    }

    public static function fileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
