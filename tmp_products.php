<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Product.php';

$product = new Product();
$products = $product->getActiveProducts();

echo "Active products count: " . count($products) . "\n";
foreach (array_slice($products,0,20) as $p) {
    echo "ID: {$p['id']} | Name: {$p['name']} | Barcode: {$p['barcode']} | Price: {$p['selling_price']} | Qty: {$p['quantity']}\n";
}
