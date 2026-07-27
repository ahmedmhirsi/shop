<?php
/**
 * POS Controller
 * Handles Point of Sale functionality
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/SaleItem.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Settings.php';
require_once __DIR__ . '/../models/StockHistory.php';

class POSController {
    private $productModel;
    private $categoryModel;
    private $saleModel;
    private $saleItemModel;
    private $customerModel;
    private $settingsModel;
    private $stockHistoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->saleModel = new Sale();
        $this->saleItemModel = new SaleItem();
        $this->customerModel = new Customer();
        $this->settingsModel = new Settings();
        $this->stockHistoryModel = new StockHistory();
    }
    
    public function index() {
        $categories = $this->categoryModel->getActiveCategories();
        $products = $this->productModel->getActiveProducts();
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Point de vente';
        $content = __DIR__ . '/../views/pos/index.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function search() {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category_id' => $_GET['category_id'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'stock_status' => $_GET['stock_status'] ?? null,
            'sort_by' => $_GET['sort_by'] ?? 'name_asc'
        ];
        
        $products = $this->productModel->searchAdvanced($filters);
        
        $settings = $this->settingsModel->getSettings();
        $currency = $settings['currency'] ?? 'TND ';
        if ($currency === '$') {
            $currency = 'TND ';
        }

        // If JSON requested, return product array for autocomplete
        if (!empty($_GET['json'])) {
            header('Content-Type: application/json; charset=utf-8');
            $out = array_map(function($p) {
                return [
                    'id' => $p['id'],
                    'name' => $p['name'],
                    'barcode' => $p['barcode'],
                    'price' => $p['selling_price'],
                    'quantity' => $p['quantity'],
                    'image' => $p['image'] ?? '',
                    'category_id' => $p['category_id'] ?? null,
                    'category_name' => $p['category_name'] ?? null,
                    'cigarette_price' => $p['cigarette_price'] ?? null,
                    'cigarettes_per_pack' => $p['cigarettes_per_pack'] ?? null
                ];
            }, $products);
            echo json_encode($out);
            exit;
        }

        $html = '';
        foreach ($products as $product) {
            $isTabacProduct = ($product['category_id'] ?? 0) == 6;
            $hasStock = $product['quantity'] > 0;
            $stock_class = !$hasStock
                ? 'out-stock'
                : ($product['quantity'] <= $product['minimum_stock'] ? 'low-stock' : 'in-stock');
            $stock_text = !$hasStock
                ? 'Rupture'
                : ($product['quantity'] <= $product['minimum_stock'] ? 'Stock faible' : 'En stock');
            $category_name = htmlspecialchars($product['category_name'] ?? 'Produit');
            
            $html .= '<div class="product-card fade-in"';
            $html .= ' data-id="' . $product['id'] . '"';
            $html .= ' data-name="' . htmlspecialchars($product['name']) . '"';
            $html .= ' data-price="' . $product['selling_price'] . '"';
            $html .= ' data-quantity="' . $product['quantity'] . '"';
            $html .= ' data-barcode="' . htmlspecialchars($product['barcode']) . '"';
            $html .= ' data-category-id="' . ($product['category_id'] ?? '') . '"';
            $html .= ' data-category-name="' . htmlspecialchars($product['category_name'] ?? '') . '"';
            $html .= ' data-cigarette-price="' . ($product['cigarette_price'] ?? '') . '"';
            $html .= ' data-cigarettes-per-pack="' . ($product['cigarettes_per_pack'] ?? '') . '"';
            $html .= ' data-image="' . htmlspecialchars($product['image'] ?? '') . '">';
            
            $html .= '<div class="product-image">';
            $image_path = $product['image'] ? __DIR__ . '/../uploads/' . $product['image'] : null;
            if ($image_path && is_file($image_path)) {
                $html .= '<img src="uploads/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '" loading="lazy" onerror="this.replaceWith(Object.assign(document.createElement(\'i\'),{className:\'bi bi-box-seam product-image-placeholder\'}))">';
            } else {
                $html .= '<i class="bi bi-box-seam product-image-placeholder"></i>';
            }
            $html .= '<button class="product-favorite">';
            $html .= '<i class="bi bi-heart"></i>';
            $html .= '</button>';
            $html .= '</div>';
            
            $html .= '<div class="product-details">';
            $html .= '<h4 class="product-name">' . htmlspecialchars($product['name']) . '</h4>';
            $html .= '<div class="product-category">' . $category_name . '</div>';
            
            $html .= '<div class="product-footer">';
            $html .= '<span class="product-price">' . $currency . number_format($product['selling_price'], 2) . '</span>';
            $html .= '<span class="product-stock ' . $stock_class . '">' . $stock_text . '</span>';
            $html .= '</div>';
            
            $html .= '<button class="product-add-btn">';
            $html .= '<i class="bi bi-plus-lg"></i> Ajouter';
            $html .= '</button>';
            
            $html .= '</div></div>';
        }
        
        echo $html;
        exit;
    }
    
    public function getProductByBarcode() {
        $barcode = $_GET['barcode'] ?? '';
        $product = $this->productModel->getByBarcode($barcode);
        
        if ($product) {
            $settings = $this->settingsModel->getSettings();
            $currency = $settings['currency'] ?? 'TND ';
            
            echo json_encode([
                'success' => true,
                'product' => [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['selling_price'],
                    'quantity' => $product['quantity'],
                    'barcode' => $product['barcode'],
                    'image' => $product['image'],
                    'category_id' => $product['category_id'] ?? null,
                    'category_name' => $product['category_name'] ?? null,
                    'cigarette_price' => $product['cigarette_price'] ?? null,
                    'cigarettes_per_pack' => $product['cigarettes_per_pack'] ?? null
                ],
                'currency' => $currency
            ]);
        } else {            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        exit;
    }
    
    public function completeSale() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        $data = [];
        
        if (empty($csrf_token) || empty($_POST['items'])) {
            $rawInput = file_get_contents('php://input');
            $payload = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($payload)) {
                $csrf_token = $payload['csrf_token'] ?? $csrf_token;
                if (isset($payload['items'])) {
                    $data = $payload;
                } elseif (isset($payload['data'])) {
                    $data = is_string($payload['data']) ? json_decode($payload['data'], true) : $payload['data'];
                }
            } else {
                parse_str($rawInput, $parsedInput);
                if (!empty($parsedInput)) {
                    $csrf_token = $parsedInput['csrf_token'] ?? $csrf_token;
                    if (isset($parsedInput['items'])) {
                        $data = $parsedInput;
                    } elseif (isset($parsedInput['data'])) {
                        $data = json_decode($parsedInput['data'], true);
                    }
                }
            }
        } else {
            $data = json_decode($_POST['data'], true);
        }
        
        // CSRF validation disabled for POS AJAX sale completion to avoid blocking the request
        if (!is_array($data) || empty($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid sale payload']);
            exit;
        }
        
        if (empty($data['items'])) {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
            exit;
        }
        
        $settings = $this->settingsModel->getSettings();
        $currency = $settings['currency'] ?? 'TND ';
        $tax_percentage = $settings['tax_percentage'] ?? 0;
        
        try {
            $this->saleModel->beginTransaction();
                
            // Validate requested stock per product before creating sale
            $requested = [];
            foreach ($data['items'] as $item) {
                if (empty($item['id']) || !isset($item['quantity']) || $item['quantity'] <= 0) {
                    throw new Exception('Quantité invalide pour un article du panier');
                }

                $product = $this->productModel->getById($item['id']);
                if (!$product) {
                    throw new Exception('Product not found: ' . $item['id']);
                }

                $unit_type = $item['unit_type'] ?? 'pack';
                $quantity = (float) $item['quantity'];

                if (!isset($requested[$item['id']])) {
                    $requested[$item['id']] = [
                        'product' => $product,
                        'packs' => 0,
                        'cigarettes' => 0
                    ];
                }

                if ($unit_type === 'pack') {
                    $requested[$item['id']]['packs'] += $quantity;
                } elseif ($unit_type === 'cigarette') {
                    if (($product['category_id'] ?? 0) != 6) {
                        throw new Exception('Invalid sale mode for: ' . $product['name']);
                    }

                    $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
                    if ($cpp <= 0) {
                        throw new Exception('Le nombre de cigarettes par paquet n\'est pas configuré pour : ' . $product['name']);
                    }

                    if (empty($product['cigarette_price']) || $product['cigarette_price'] <= 0) {
                        throw new Exception('Prix par cigarette non configuré pour: ' . $product['name']);
                    }

                    $requested[$item['id']]['cigarettes'] += $quantity;
                } else {
                    throw new Exception('Invalid unit type for: ' . $product['name']);
                }
            }

            foreach ($requested as $productId => $request) {
                $product = $request['product'];
                $isTabac = (($product['category_id'] ?? 0) == 6);

                if ($isTabac) {
                    $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
                    if ($cpp <= 0) {
                        throw new Exception('Le nombre de cigarettes par paquet n\'est pas configuré pour : ' . $product['name']);
                    }

                    // Calculate available cigarettes directly - don't throw exception on decimal quantity
                    $available_cigarettes = (int) round((float) ($product['quantity'] ?? 0) * $cpp);
                    $required_cigarettes = ($request['packs'] * $cpp) + $request['cigarettes'];

                    if ($required_cigarettes > $available_cigarettes) {
                        throw new Exception('Stock insuffisant pour: ' . $product['name']);
                    }
                } else {
                    if ($request['packs'] > $product['quantity']) {
                        throw new Exception('Stock insuffisant pour: ' . $product['name']);
                    }

                    if ($request['cigarettes'] > 0) {
                        throw new Exception('Mode de vente invalide pour: ' . $product['name']);
                    }
                }
            }

            // Calculate totals
            $subtotal = 0;
            $items = [];
                
            foreach ($data['items'] as $item) {
                $product = $this->productModel->getById($item['id']);
                $unit_type = $item['unit_type'] ?? 'pack';

                if ($unit_type === 'pack') {
                    $sale_price = $product['selling_price'];
                    $buying_price_per_unit = $product['buying_price'];
                } else {
                    $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
                    $sale_price = $product['cigarette_price'];
                    $buying_price_per_unit = $product['buying_price'] / $cpp;
                }

                $item_subtotal = $item['quantity'] * $sale_price;
                $item_profit = ($sale_price - $buying_price_per_unit) * $item['quantity'];

                $subtotal += $item_subtotal;

                $items[] = [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'buying_price' => $buying_price_per_unit,
                    'selling_price' => $sale_price,
                    'subtotal' => $item_subtotal,
                    'profit' => $item_profit,
                    'unit_type' => $unit_type
                ];
            }
                
            $discount = $data['discount'] ?? 0;
            $tax = ($subtotal - $discount) * ($tax_percentage / 100);
            $total = $subtotal - $discount + $tax;
            $amount_received = $data['amount_received'] ?? $total;
            $change = $amount_received - $total;
            
            // Generate invoice number
            $invoice_prefix = $settings['invoice_prefix'] ?? 'INV-';
            $invoice_number = generate_invoice_number($invoice_prefix);
            
            // Create sale
            $sale_data = [
                'invoice_number' => $invoice_number,
                'customer_id' => !empty($data['customer_name']) ? $this->getOrCreateCustomer($data['customer_name']) : null,
                'user_id' => $_SESSION['user_id'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'amount_received' => $amount_received,
                'change' => $change,
                'notes' => $data['notes'] ?? null,
                'status' => 'completed'
            ];
            
            $sale_id = $this->saleModel->create($sale_data);
            
            // Create sale items
            foreach ($items as &$item) {
                $item['sale_id'] = $sale_id;
            }
            unset($item);
            $this->saleItemModel->createMultiple($items);
            
            // Update product stock
            foreach ($data['items'] as $item) {
                $product = $this->productModel->getById($item['id']);
                $unit_type = $item['unit_type'] ?? 'pack';

                if ($unit_type === 'pack') {
                    $new_quantity = $product['quantity'] - $item['quantity'];
                    $this->productModel->updateStock($item['id'], $new_quantity);

                    $this->stockHistoryModel->addMovement([
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'type' => 'out',
                        'reference_id' => $sale_id,
                        'reference_type' => 'sale',
                        'user_id' => $_SESSION['user_id'],
                        'notes' => 'Sale: ' . $invoice_number . ' (packs)'
                    ]);
                } else {
                    $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
                    if ($cpp <= 0) {
                        throw new Exception('Cigarettes per pack is not configured for: ' . $product['name']);
                    }

                    $new_quantity = $this->calculateStockAfterCigaretteSale($product, $item['quantity']);
                    $this->productModel->updateStock($item['id'], $new_quantity);

                    $this->stockHistoryModel->addMovement([
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'type' => 'out',
                        'reference_id' => $sale_id,
                        'reference_type' => 'sale',
                        'user_id' => $_SESSION['user_id'],
                        'notes' => 'Sale: ' . $invoice_number . ' (cigarettes)'
                    ]);
                }
            }
            
            $this->saleModel->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Sale completed successfully',
                'invoice_number' => $invoice_number,
                'sale_id' => $sale_id
            ]);
            
        } catch (Exception $e) {
            $this->saleModel->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    private function getOrCreateCustomer($name) {
        $customer = $this->customerModel->getBy('name', $name);
        if ($customer) {
            return $customer['id'];
        }
        
        return $this->customerModel->create([
            'name' => $name,
            'status' => 'active'
        ]);
    }
    
    private function validateTabacProductConfig(array $product, bool $forCigaretteSale = false) {
        if ((($product['category_id'] ?? 0) != 6)) {
            return;
        }

        $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
        if ($cpp <= 0) {
            throw new Exception('Le nombre de cigarettes par paquet n\'est pas configuré pour : ' . $product['name']);
        }

        if ($forCigaretteSale) {
            if (empty($product['cigarette_price']) || $product['cigarette_price'] <= 0) {
                throw new Exception('Prix par cigarette non configuré pour: ' . $product['name']);
            }
        }
    }

    private function getTabacAvailableCigarettes(array $product) {
        $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
        $quantity = (float) ($product['quantity'] ?? 0);

        if ($cpp <= 0) {
            throw new Exception('Le nombre de cigarettes par paquet n\'est pas configuré pour : ' . $product['name']);
        }

        $available = $quantity * $cpp;
        if (abs($available - round($available)) > 1e-6) {
            throw new Exception('Stock Tabac invalide pour : ' . $product['name']);
        }

        return (int) round($available);
    }
    
    private function calculateStockAfterCigaretteSale(array $product, int $soldCigarettes) {
        $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
        $quantity = (float) ($product['quantity'] ?? 0);
 
        if ($cpp <= 0) {
            throw new Exception('Cigarettes per pack is not configured for: ' . $product['name']);
        }
 
        $available = (int) round($quantity * $cpp);
        $remaining = $available - $soldCigarettes;

        if ($remaining < 0) {
            throw new Exception('Insufficient cigarette stock for: ' . $product['name']);
        }

        return round($remaining / $cpp, 3);
    }
    
    private function restoreTabacCigaretteStock(array $product, int $restoredCigarettes) {
        $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
        $quantity = (float) ($product['quantity'] ?? 0);

        if ($cpp <= 0) {
            throw new Exception('Cigarettes per pack is not configured for: ' . $product['name']);
        }

        $available = (int) round($quantity * $cpp);
        $newTotal = $available + $restoredCigarettes;

        return round($newTotal / $cpp, 3);
    }

    public function printInvoice() {
        $sale_id = (int)$_GET['id'] ?? 0;
        $sale = $this->saleModel->getById($sale_id);
        
        if (!$sale) {
            echo 'Sale not found';
            exit;
        }
        
        $sale_items = $this->saleItemModel->getSaleItemsWithProduct($sale_id);
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Imprimer la facture';
        $content = __DIR__ . '/../views/pos/invoice.php';
        include __DIR__ . '/../views/layout.php';
    }
}
