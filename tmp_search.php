<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Product.php';

$product = new Product();
$term = $argv[1] ?? 'dunap';
$results = $product->search($term);

echo "Search term: {$term}\n";
echo "Results count: " . count($results) . "\n";
print_r(array_map(function($r) { return ['id'=>$r['id'],'name'=>$r['name'],'barcode'=>$r['barcode']]; }, $results));
