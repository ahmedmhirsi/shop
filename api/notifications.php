<?php
/**
 * Notifications API
 * Returns notification counts for the dashboard
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Product.php';

// Check authentication
if (!is_logged_in()) {
    json_response(['error' => 'Unauthorized'], 401);
}

// Only allow for boss role
if (!is_boss()) {
    json_response(['count' => 0]);
}

$productModel = new Product();
$lowStock = count($productModel->getLowStockProducts());
$outOfStock = count($productModel->getOutOfStockProducts());

$total = $lowStock + $outOfStock;

json_response(['count' => $total]);
