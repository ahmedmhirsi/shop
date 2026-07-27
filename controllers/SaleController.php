<?php
/**
 * Sale Controller
 * Handles sales history viewing
 */

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/SaleItem.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Settings.php';

class SaleController {
    private $saleModel;
    private $saleItemModel;
    private $settingsModel;
    
    public function __construct() {
        $this->saleModel = new Sale();
        $this->saleItemModel = new SaleItem();
        $this->settingsModel = new Settings();
    }
    
    public function index() {
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $filters = [
            'status' => $_GET['status'] ?? 'completed',
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'invoice_number' => $_GET['invoice_number'] ?? null
        ];
        
        $sales = $this->saleModel->getSalesWithDetails($filters, 's.created_at DESC', ITEMS_PER_PAGE, ($page - 1) * ITEMS_PER_PAGE);
        $total = $this->saleModel->countFiltered($filters);
        $pagination = paginate($total, $page, ITEMS_PER_PAGE);
        
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Historique des ventes';
        $content = __DIR__ . '/../views/sales/index.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function view() {
        $id = (int)$_GET['id'] ?? 0;
        $sale = $this->saleModel->getById($id);
        
        if (!$sale) {
            redirect_with_flash('index.php?page=sales', 'error', 'Sale not found');
        }
        
        $sale_items = $this->saleItemModel->getSaleItemsWithProduct($id);
        $settings = $this->settingsModel->getSettings();
        
        $page_title = 'Détails de la vente';
        $content = __DIR__ . '/../views/sales/view.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function cancel() {
        $id = (int)$_GET['id'] ?? 0;
        $sale = $this->saleModel->getById($id);
        
        if (!$sale) {
            redirect_with_flash('index.php?page=sales', 'error', 'Sale not found');
        }
        
        if ($sale['status'] === 'cancelled') {
            redirect_with_flash('index.php?page=sales', 'error', 'Sale already cancelled');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!validate_csrf($csrf_token)) {
                redirect_with_flash('index.php?page=sales', 'error', 'Invalid request');
            }
            
            if ($this->saleModel->cancelSale($id)) {
                // Restore stock
                $sale_items = $this->saleItemModel->getBySaleId($id);
                foreach ($sale_items as $item) {
                    $productModel = new Product();
                    $product = $productModel->getById($item['product_id']);
                    if ($product) {
                        if (!empty($item['unit_type']) && $item['unit_type'] === 'cigarette') {
                            $cpp = (int) ($product['cigarettes_per_pack'] ?? 0);
                            if ($cpp <= 0) {
                                throw new Exception('Configuration du produit invalide pour la restauration du stock : ' . $product['name']);
                            }
                            $available = (int) round((float) $product['quantity'] * $cpp);
                            $remaining = $available + $item['quantity'];
                            $new_quantity = round($remaining / $cpp, 3);
                            $productModel->updateStock($item['product_id'], $new_quantity);
                        } else {
                            $new_quantity = $product['quantity'] + $item['quantity'];
                            $productModel->updateStock($item['product_id'], $new_quantity);
                        }
                    }
                }
                
                redirect_with_flash('index.php?page=sales', 'success', 'Sale cancelled successfully');
            } else {
                redirect_with_flash('index.php?page=sales', 'error', 'Failed to cancel sale');
            }
        }
        
        $page_title = 'Annuler la vente';
        $content = __DIR__ . '/../views/sales/cancel.php';
        include __DIR__ . '/../views/layout.php';
    }
}
