<?php
/**
 * POS (Point of Sale) Controller
 */

class POSController extends Controller {
    private $productModel;
    private $saleModel;
    private $saleItemModel;
    private $customerModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->productModel = new Product();
        $this->saleModel = new Sale();
        $this->saleItemModel = new SaleItem();
        $this->customerModel = new Customer();
    }

    public function index() {
        $products = $this->productModel->getAll();
        $customers = $this->customerModel->getAll();
        $this->render('pos/index', [
            'products' => $products,
            'customers' => $customers,
            'csrf_token' => SecurityHelper::generateCsrfToken()
        ]);
    }

    public function searchProducts() {
        if ($this->method !== 'POST') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $query = SecurityHelper::sanitizeInput($_POST['query'] ?? '');
        if (strlen($query) < 2) {
            $this->jsonResponse(['products' => []]);
        }

        $products = $this->productModel->searchByName($query, 30);
        $this->jsonResponse(['products' => $products]);
    }

    public function getProductByBarcode() {
        if ($this->method !== 'POST') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $barcode = SecurityHelper::sanitizeInput($_POST['barcode'] ?? '');
        $product = $this->productModel->findByBarcode($barcode);

        if (!$product) {
            $this->jsonResponse(['error' => 'Product not found'], 404);
        }

        $this->jsonResponse(['product' => $product]);
    }

    public function checkout() {
        if ($this->method !== 'POST') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
            $this->jsonResponse(['error' => 'Security token expired'], 403);
        }

        $items = json_decode($_POST['items'] ?? '[]', true);
        if (empty($items)) {
            $this->jsonResponse(['error' => 'Cart is empty'], 400);
        }

        $customer_id = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
        $discount = (float)($_POST['discount'] ?? 0);
        $payment_method = in_array($_POST['payment_method'] ?? 'cash', ['cash', 'card']) ? $_POST['payment_method'] : 'cash';
        $amount_received = (float)($_POST['amount_received'] ?? 0);

        $invoice_number = SecurityHelper::generateInvoiceNumber();
        $subtotal = 0;
        $tax = TAX_RATE;
        $taxAmount = 0;

        $this->saleModel->getConnection()->beginTransaction();

        try {
            foreach ($items as $item) {
                $product = $this->productModel->findById($item['product_id']);
                if (!$product) {
                    throw new Exception('Product not found: ' . $item['product_id']);
                }

                $qty = $item['unit_type'] === 'cigarette' ? $item['quantity'] / $product['cigarettes_per_pack'] : $item['quantity'];
                if ($product['quantity'] < $qty) {
                    throw new Exception('Insufficient stock for ' . $product['name']);
                }

                $itemSubtotal = $item['selling_price'] * $item['quantity'];
                $itemProfit = ($item['selling_price'] - $item['buying_price']) * $item['quantity'];
                $subtotal += $itemSubtotal;

                $this->productModel->updateQuantity($item['product_id'], $qty, 'decrement');
            }

            $taxAmount = $subtotal * ($tax / 100);
            $total = $subtotal + $taxAmount - $discount;
            $change = FormatterHelper::calculateChange($total, $amount_received);

            $saleData = [
                'invoice_number' => $invoice_number,
                'customer_id' => $customer_id,
                'user_id' => $_SESSION['user_id'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $taxAmount,
                'total' => $total,
                'payment_method' => $payment_method,
                'amount_received' => $amount_received,
                'change' => $change,
                'notes' => $_POST['notes'] ?? ''
            ];

            if (!$this->saleModel->create($saleData)) {
                throw new Exception('Failed to create sale');
            }

            $saleId = $this->saleModel->getLastSaleId();

            foreach ($items as $item) {
                $product = $this->productModel->findById($item['product_id']);
                $itemSubtotal = $item['selling_price'] * $item['quantity'];
                $itemProfit = ($item['selling_price'] - $item['buying_price']) * $item['quantity'];

                $saleItemData = [
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price'],
                    'subtotal' => $itemSubtotal,
                    'profit' => $itemProfit,
                    'unit_type' => $item['unit_type'] ?? 'pack'
                ];

                if (!$this->saleItemModel->create($saleItemData)) {
                    throw new Exception('Failed to create sale item');
                }
            }

            $this->saleModel->getConnection()->commit();

            $this->jsonResponse([
                'success' => true,
                'invoice_number' => $invoice_number,
                'sale_id' => $saleId,
                'total' => $total,
                'change' => $change
            ]);
        } catch (Exception $e) {
            $this->saleModel->getConnection()->rollBack();
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function getConnection() {
        return Database::getInstance()->getConnection();
    }

    public function printReceipt() {
        $saleId = (int)($_GET['id'] ?? 0);
        if (!$saleId) {
            $this->jsonResponse(['error' => 'Invalid sale ID'], 400);
        }

        $sale = $this->saleModel->findById($saleId);
        if (!$sale) {
            $this->jsonResponse(['error' => 'Sale not found'], 404);
        }

        $items = $this->saleItemModel->getBySaleId($saleId);

        $this->render('pos/receipt', [
            'sale' => $sale,
            'items' => $items
        ]);
    }

    public function sales() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $sales = $this->saleModel->getAll(['start_date' => $startDate, 'end_date' => $endDate]);
        $this->render('pos/sales', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'csrf_token' => SecurityHelper::generateCsrfToken()
        ]);
    }
}
