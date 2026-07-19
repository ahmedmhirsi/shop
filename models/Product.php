<?php
/**
 * Product Model
 * Handles product management
 */

require_once __DIR__ . '/Model.php';

class Product extends Model {
    protected $table = 'products';
    protected $primaryKey = 'id';
    
    /**
     * Get active products
     * @return array
     */
    public function getActiveProducts() {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE p.status = 'active'
                ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get products by category
     * @param int $category_id
     * @return array
     */
    public function getByCategory($category_id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = :category_id AND p.status = 'active'
                ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category_id' => $category_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search products
     * @param string $search
     * @return array
     */
    public function search($search) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
            FROM {$this->table} p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            WHERE p.status = 'active' 
            AND (p.barcode LIKE :search OR p.name LIKE :search_name OR c.name LIKE :search_category)
            ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $like = "%{$search}%";
        $stmt->execute(['search' => $like, 'search_name' => $like, 'search_category' => $like]);
        return $stmt->fetchAll();
    }
    
    /**
     * Advanced Search products
     * @param array $filters
     * @return array
     */
    public function searchAdvanced($filters = []) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE p.status = 'active'";
        
        $params = [];
        
        // Search query
        if (!empty($filters['search'])) {
            $sql .= " AND (p.barcode LIKE :search OR p.name LIKE :search_name OR c.name LIKE :search_category)";
            $like = "%{$filters['search']}%";
            $params['search'] = $like;
            $params['search_name'] = $like;
            $params['search_category'] = $like;
        }
        
        // Category filter
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        // Price min filter
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $sql .= " AND p.selling_price >= :min_price";
            $params['min_price'] = (float)$filters['min_price'];
        }
        
        // Price max filter
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $sql .= " AND p.selling_price <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }
        
        // Stock status filter
        if (!empty($filters['stock_status']) && $filters['stock_status'] !== 'all') {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $sql .= " AND p.quantity > p.minimum_stock";
                    break;
                case 'low_stock':
                    $sql .= " AND p.quantity > 0 AND p.quantity <= p.minimum_stock";
                    break;
                case 'out_of_stock':
                    $sql .= " AND p.quantity = 0";
                    break;
            }
        }
        
        // Sorting
        $sort_by = $filters['sort_by'] ?? 'name_asc';
        switch ($sort_by) {
            case 'name_desc':
                $sql .= " ORDER BY p.name DESC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY p.selling_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.selling_price DESC";
                break;
            case 'quantity_asc':
                $sql .= " ORDER BY p.quantity ASC";
                break;
            case 'quantity_desc':
                $sql .= " ORDER BY p.quantity DESC";
                break;
            case 'name_asc':
            default:
                $sql .= " ORDER BY p.name ASC";
                break;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get product by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE p.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Get product by barcode
     * @param string $barcode
     * @return array|false
     */
    public function getByBarcode($barcode) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE p.barcode = :barcode AND p.status = 'active'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['barcode' => $barcode]);
        return $stmt->fetch();
    }
    
    /**
     * Get low stock products
     * @return array
     */
    public function getLowStockProducts() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' 
                AND p.quantity > 0 AND p.quantity <= p.minimum_stock
                ORDER BY p.quantity ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get out of stock products
     * @return array
     */
    public function getOutOfStockProducts() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' AND p.quantity = 0
                ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get top selling products
     * @param int $limit
     * @return array
     */
    public function getTopSellingProducts($limit = 10) {
        $sql = "SELECT p.*, SUM(si.quantity) as total_sold 
                FROM {$this->table} p 
                INNER JOIN sale_items si ON p.id = si.product_id 
                INNER JOIN sales s ON si.sale_id = s.id 
                WHERE p.status = 'active' AND s.status = 'completed'
                GROUP BY p.id 
                ORDER BY total_sold DESC 
                LIMIT {$limit}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Update stock quantity
     * @param int $id
     * @param float|string $quantity
     * @return bool
     */
    public function updateStock($id, $quantity) {
        return $this->update($id, ['quantity' => $quantity]);
    }

    /**
     * Check if barcode exists
     * @param string $barcode
     * @param int $exclude_id
     * @return bool
     */
    public function barcodeExists($barcode, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE barcode = :barcode";
        $params = ['barcode' => $barcode];
        
        if ($exclude_id) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $exclude_id;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Deactivate product
     * @param int $id
     * @return bool
     */
    public function deactivate($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Activate product
     * @param int $id
     * @return bool
     */
    public function activate($id) {
        return $this->update($id, ['status' => 'active']);
    }
    
    /**
     * Get products with pagination
     * @param int $page
     * @param int $per_page
     * @param array $filters
     * @return array
     */
    public function getPaginated($page = 1, $per_page = 10, $filters = []) {
        $offset = ($page - 1) * $per_page;
        
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['supplier_id'])) {
            $sql .= " AND p.supplier_id = :supplier_id";
            $params['supplier_id'] = $filters['supplier_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.barcode LIKE :search OR p.name LIKE :search_name)";
            $params['search'] = "%{$filters['search']}%";
            $params['search_name'] = $params['search'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        } else {
            $sql .= " AND p.status = 'active'";
        }
        
        $sql .= " ORDER BY p.name ASC LIMIT {$per_page} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);

        // Ensure we only pass parameters that exist in the SQL to avoid PDO HY093 errors
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $matches);
        $placeholders = $matches[1] ?? [];
        $execParams = [];
        foreach ($placeholders as $ph) {
            if (isset($params[$ph])) {
                $execParams[$ph] = $params[$ph];
            }
        }

        $stmt->execute($execParams);
        return $stmt->fetchAll();
    }
    
    /**
     * Get filtered products without pagination
     * @param array $filters
     * @return array
     */
    public function getFiltered($filters = []) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['supplier_id'])) {
            $sql .= " AND p.supplier_id = :supplier_id";
            $params['supplier_id'] = $filters['supplier_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.barcode LIKE :search OR p.name LIKE :search_name)";
            $params['search'] = "%{$filters['search']}%";
            $params['search_name'] = $params['search'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        } else {
            $sql .= " AND p.status = 'active'";
        }
        
        $sql .= " ORDER BY p.name ASC";
        
        $stmt = $this->db->prepare($sql);

        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $matches);
        $placeholders = $matches[1] ?? [];
        $execParams = [];
        foreach ($placeholders as $ph) {
            if (isset($params[$ph])) {
                $execParams[$ph] = $params[$ph];
            }
        }

        $stmt->execute($execParams);
        return $stmt->fetchAll();
    }
    
    /**
     * Count products with filters
     * @param array $filters
     * @return int
     */
    public function countFiltered($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['supplier_id'])) {
            $sql .= " AND supplier_id = :supplier_id";
            $params['supplier_id'] = $filters['supplier_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (barcode LIKE :search OR name LIKE :search_name)";
            $params['search'] = "%{$filters['search']}%";
            $params['search_name'] = $params['search'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        } else {
            $sql .= " AND status = 'active'";
        }
        
        $stmt = $this->db->prepare($sql);

        // Only pass params that are actually used in the query
        preg_match_all('/:([a-zA-Z0-9_]+)/', $sql, $matches);
        $placeholders = $matches[1] ?? [];
        $execParams = [];
        foreach ($placeholders as $ph) {
            if (isset($params[$ph])) {
                $execParams[$ph] = $params[$ph];
            }
        }

        $stmt->execute($execParams);
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }
}
