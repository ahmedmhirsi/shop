<?php
/**
 * Product Controller
 * Handles product management
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../models/StockHistory.php';
require_once __DIR__ . '/../models/Settings.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    private $supplierModel;
    private $stockHistoryModel;
    private $settingsModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
        $this->stockHistoryModel = new StockHistory();
        $this->settingsModel = new Settings();
    }
    
    public function index() {
        $page = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
        $filters = [
            'category_id' => $_GET['category_id'] ?? null,
            'supplier_id' => $_GET['supplier_id'] ?? null,
            'search' => $_GET['search'] ?? null,
            'status' => $_GET['status'] ?? 'active'
        ];
        
        $products = $this->productModel->getPaginated($page, ITEMS_PER_PAGE, $filters);
        $total = $this->productModel->countFiltered($filters);
        $pagination = paginate($total, $page, ITEMS_PER_PAGE);
        
        $categories = $this->categoryModel->getActiveCategories();
        $suppliers = $this->supplierModel->getActiveSuppliers();
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Produits';
        $content = __DIR__ . '/../views/products/index.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=products', 'error', 'Invalid request');
            }
            
            $data = [
                'barcode' => sanitize($_POST['barcode']),
                'name' => sanitize($_POST['name']),
                'category_id' => (int)$_POST['category_id'],
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'buying_price' => (float)$_POST['buying_price'],
                'selling_price' => (float)$_POST['selling_price'],
                'quantity' => (float)$_POST['quantity'],
                'minimum_stock' => (int)$_POST['minimum_stock'],
                'description' => sanitize($_POST['description'] ?? ''),
                'cigarette_price' => isset($_POST['cigarette_price']) && $_POST['cigarette_price'] !== '' ? (float)$_POST['cigarette_price'] : null,
                'cigarettes_per_pack' => isset($_POST['cigarettes_per_pack']) && $_POST['cigarettes_per_pack'] !== '' ? (int)$_POST['cigarettes_per_pack'] : null,
                'status' => 'active'
            ];
            
            try {
                $this->validateTobaccoProductData($data);
            } catch (Exception $e) {
                redirect_with_flash('index.php?page=products&action=create', 'error', $e->getMessage());
            }
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $filename = upload_file($_FILES['image'], UPLOAD_PATH);
                if ($filename) {
                    $data['image'] = $filename;
                }
            }
            
            // Validate
            if ($this->productModel->barcodeExists($data['barcode'])) {
                redirect_with_flash('index.php?page=products&action=create', 'error', 'Barcode already exists');
            }
            
            $productId = $this->productModel->create($data);
            if ($productId) {
                // Add stock history
                $this->stockHistoryModel->addMovement([
                    'product_id' => $productId,
                    'quantity' => $data['quantity'],
                    'type' => 'in',
                    'reference_type' => 'initial_stock',
                    'user_id' => $_SESSION['user_id'],
                    'notes' => 'Initial stock'
                ]);
                
                redirect_with_flash('index.php?page=products', 'success', 'Product added successfully');
            } else {
                redirect_with_flash('index.php?page=products&action=create', 'error', 'Failed to add product');
            }
        }
        
        $categories = $this->categoryModel->getActiveCategories();
        $suppliers = $this->supplierModel->getActiveSuppliers();
        
        $page_title = 'Ajouter un produit';
        $content = __DIR__ . '/../views/products/create.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function edit() {
        $id = (int)$_GET['id'] ?? 0;
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            redirect_with_flash('index.php?page=products', 'error', 'Product not found');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=products', 'error', 'Invalid request');
            }
            
            $data = [
                'barcode' => sanitize($_POST['barcode']),
                'name' => sanitize($_POST['name']),
                'category_id' => (int)$_POST['category_id'],
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'buying_price' => (float)$_POST['buying_price'],
                'selling_price' => (float)$_POST['selling_price'],
                'quantity' => (float)$_POST['quantity'],
                'minimum_stock' => (int)$_POST['minimum_stock'],
                'description' => sanitize($_POST['description'] ?? ''),
                'cigarette_price' => isset($_POST['cigarette_price']) && $_POST['cigarette_price'] !== '' ? (float)$_POST['cigarette_price'] : null,
                'cigarettes_per_pack' => isset($_POST['cigarettes_per_pack']) && $_POST['cigarettes_per_pack'] !== '' ? (int)$_POST['cigarettes_per_pack'] : null
            ];
            
            try {
                $this->validateTobaccoProductData($data);
            } catch (Exception $e) {
                redirect_with_flash('index.php?page=products&action=edit&id=' . $id, 'error', $e->getMessage());
            }
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $filename = upload_file($_FILES['image'], UPLOAD_PATH);
                if ($filename) {
                    // Delete old image
                    if ($product['image']) {
                        delete_file($product['image'], UPLOAD_PATH);
                    }
                    $data['image'] = $filename;
                }
            }
            
            // Validate barcode
            if ($this->productModel->barcodeExists($data['barcode'], $id)) {
                redirect_with_flash('index.php?page=products&action=edit&id=' . $id, 'error', 'Barcode already exists');
            }
            
            // Check if quantity changed
            $old_quantity = $product['quantity'];
            $new_quantity = $data['quantity'];
            
            if ($this->productModel->update($id, $data)) {
                // Add stock history if quantity changed
                if ($old_quantity != $new_quantity) {
                    $difference = $new_quantity - $old_quantity;
                    $this->stockHistoryModel->addMovement([
                        'product_id' => $id,
                        'quantity' => abs($difference),
                        'type' => $difference > 0 ? 'in' : 'out',
                        'reference_type' => 'manual_adjustment',
                        'user_id' => $_SESSION['user_id'],
                        'notes' => 'Manual stock adjustment'
                    ]);
                }
                
                redirect_with_flash('index.php?page=products', 'success', 'Product updated successfully');
            } else {
                redirect_with_flash('index.php?page=products&action=edit&id=' . $id, 'error', 'Failed to update product');
            }
        }
        
        $categories = $this->categoryModel->getActiveCategories();
        $suppliers = $this->supplierModel->getActiveSuppliers();
        
        $page_title = 'Modifier le produit';
        $content = __DIR__ . '/../views/products/edit.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function delete() {
        $id = (int)$_GET['id'] ?? 0;
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            redirect_with_flash('index.php?page=products', 'error', 'Product not found');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=products', 'error', 'Invalid request');
            }
            
            // Delete image
            if ($product['image']) {
                delete_file($product['image'], UPLOAD_PATH);
            }
            
            if ($this->productModel->delete($id)) {
                redirect_with_flash('index.php?page=products', 'success', 'Product deleted successfully');
            } else {
                redirect_with_flash('index.php?page=products', 'error', 'Failed to delete product');
            }
        }
        
        $page_title = 'Supprimer le produit';
        $content = __DIR__ . '/../views/products/delete.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function view() {
        $id = (int)$_GET['id'] ?? 0;
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            redirect_with_flash('index.php?page=products', 'error', 'Product not found');
        }
        
        $stock_history = $this->stockHistoryModel->getByProduct($id);
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Détails du produit';
        $content = __DIR__ . '/../views/products/view.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    /**
     * Validate Tabac product configuration for cigarette sales.
     * @param array $data
     * @throws Exception
     */
    private function validateTobaccoProductData(array $data) {
        if (($data['category_id'] ?? 0) != 6) {
            return;
        }

        $cigarettesPerPack = $data['cigarettes_per_pack'];
        $cigarettePrice = $data['cigarette_price'];
        $quantity = isset($data['quantity']) ? (float) $data['quantity'] : 0.0;

        if ($cigarettesPerPack !== null) {
            if ($cigarettesPerPack <= 0) {
                throw new Exception('Le nombre de cigarettes par paquet doit être supérieur à zéro pour les produits Tabac.');
            }
        }

        if ($cigarettePrice !== null) {
            if ($cigarettePrice <= 0) {
                throw new Exception('Le prix par cigarette doit être supérieur à zéro pour les produits Tabac.');
            }

            if ($cigarettesPerPack === null) {
                throw new Exception('Le nombre de cigarettes par paquet doit être configuré lorsque le prix par cigarette est renseigné.');
            }
        }

        if ($cigarettesPerPack !== null && $cigarettesPerPack > 0) {
            $cigarettes = round($quantity * $cigarettesPerPack, 6);
            if (abs($cigarettes - round($cigarettes)) > 1e-6) {
                throw new Exception('La quantité Tabac doit être exprimée en paquets et cigarettes entières. Exemple : 1.05 pour 1 paquet + 1 cigarette.');
            }
        } elseif ($quantity != (int) $quantity) {
            throw new Exception('La quantité Tabac doit être un nombre entier de paquets si le nombre de cigarettes par paquet n\'est pas configuré.');
        }
    }
}
