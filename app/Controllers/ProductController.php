<?php
/**
 * Product Management Controller
 */

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $supplierModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
    }

    public function index() {
        $categoryId = $_GET['category'] ?? null;
        $filter = $categoryId ? ['category_id' => $categoryId, 'status' => 'active'] : ['status' => 'active'];
        $products = $this->productModel->getAll($filter);
        $categories = $this->categoryModel->getAll();

        $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'csrf_token' => SecurityHelper::generateCsrfToken()
        ]);
    }

    public function create() {
        $this->requireRole('boss');

        if ($this->method === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
                $this->renderCreateForm(['error' => 'Security token expired']);
                return;
            }

            $data = [
                'barcode' => SecurityHelper::sanitizeInput($_POST['barcode'] ?? ''),
                'name' => SecurityHelper::sanitizeInput($_POST['name']),
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'buying_price' => (float)($_POST['buying_price'] ?? 0),
                'selling_price' => (float)($_POST['selling_price'] ?? 0),
                'cigarette_price' => (float)($_POST['cigarette_price'] ?? 0),
                'cigarettes_per_pack' => (int)($_POST['cigarettes_per_pack'] ?? 20),
                'quantity' => (float)($_POST['quantity'] ?? 0),
                'minimum_stock' => (int)($_POST['minimum_stock'] ?? 5),
                'description' => SecurityHelper::sanitizeInput($_POST['description'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->renderCreateForm(['error' => 'Product name is required']);
                return;
            }

            if ($this->productModel->create($data)) {
                $this->redirect('/shop_v2/index.php?url=products');
            } else {
                $this->renderCreateForm(['error' => 'Error creating product']);
            }
        } else {
            $this->renderCreateForm();
        }
    }

    private function renderCreateForm($data = []) {
        $categories = $this->categoryModel->getAll();
        $suppliers = $this->supplierModel->getAll();
        $data['categories'] = $categories;
        $data['suppliers'] = $suppliers;
        $data['csrf_token'] = SecurityHelper::generateCsrfToken();
        $this->render('products/form', $data);
    }

    public function edit() {
        $this->requireRole('boss');
        $productId = (int)($_GET['id'] ?? 0);

        if (!$productId) {
            $this->redirect('/shop_v2/index.php?url=products');
        }

        $product = $this->productModel->findById($productId);
        if (!$product) {
            $this->redirect('/shop_v2/index.php?url=products');
        }

        if ($this->method === 'POST') {
            $csrf_token = $_POST['csrf_token'] ?? '';
            if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
                $this->renderEditForm($product, ['error' => 'Security token expired']);
                return;
            }

            $data = [
                'barcode' => SecurityHelper::sanitizeInput($_POST['barcode'] ?? ''),
                'name' => SecurityHelper::sanitizeInput($_POST['name']),
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'buying_price' => (float)($_POST['buying_price'] ?? 0),
                'selling_price' => (float)($_POST['selling_price'] ?? 0),
                'cigarette_price' => (float)($_POST['cigarette_price'] ?? 0),
                'cigarettes_per_pack' => (int)($_POST['cigarettes_per_pack'] ?? 20),
                'minimum_stock' => (int)($_POST['minimum_stock'] ?? 5),
                'description' => SecurityHelper::sanitizeInput($_POST['description'] ?? ''),
                'status' => in_array($_POST['status'] ?? 'active', ['active', 'inactive']) ? $_POST['status'] : 'active'
            ];

            if (empty($data['name'])) {
                $this->renderEditForm($product, ['error' => 'Product name is required']);
                return;
            }

            if ($this->productModel->update($productId, $data)) {
                $this->redirect('/shop_v2/index.php?url=products');
            } else {
                $this->renderEditForm($product, ['error' => 'Error updating product']);
            }
        } else {
            $this->renderEditForm($product);
        }
    }

    private function renderEditForm($product, $data = []) {
        $categories = $this->categoryModel->getAll();
        $suppliers = $this->supplierModel->getAll();
        $data['product'] = $product;
        $data['categories'] = $categories;
        $data['suppliers'] = $suppliers;
        $data['csrf_token'] = SecurityHelper::generateCsrfToken();
        $data['isEdit'] = true;
        $this->render('products/form', $data);
    }

    public function delete() {
        $this->requireRole('boss');
        if ($this->method !== 'POST') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
            $this->jsonResponse(['error' => 'Security token expired'], 403);
        }

        $productId = (int)($_POST['id'] ?? 0);
        if (!$productId) {
            $this->jsonResponse(['error' => 'Invalid product ID'], 400);
        }

        if ($this->productModel->delete($productId)) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['error' => 'Error deleting product'], 500);
        }
    }

    public function categories() {
        $this->requireRole('boss');
        $categories = $this->categoryModel->getAll();
        $this->render('products/categories', [
            'categories' => $categories,
            'csrf_token' => SecurityHelper::generateCsrfToken()
        ]);
    }

    public function addCategory() {
        $this->requireRole('boss');
        if ($this->method !== 'POST') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!SecurityHelper::verifyCsrfToken($csrf_token)) {
            $this->jsonResponse(['error' => 'Security token expired'], 403);
        }

        $name = SecurityHelper::sanitizeInput($_POST['name'] ?? '');
        if (empty($name)) {
            $this->jsonResponse(['error' => 'Category name is required'], 400);
        }

        if ($this->categoryModel->create($name, $_POST['description'] ?? '')) {
            $this->jsonResponse(['success' => true]);
        } else {
            $this->jsonResponse(['error' => 'Error creating category'], 500);
        }
    }

    public function suppliers() {
        $this->requireRole('boss');
        $suppliers = $this->supplierModel->getAll();
        $this->render('products/suppliers', [
            'suppliers' => $suppliers,
            'csrf_token' => SecurityHelper::generateCsrfToken()
        ]);
    }
}
