<?php
/**
 * Product Model
 */

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('
            SELECT p.*, c.name as category_name, s.name as supplier_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            WHERE p.id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByBarcode($barcode) {
        $stmt = $this->db->prepare('
            SELECT p.*, c.name as category_name, s.name as supplier_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            WHERE p.barcode = ? AND p.status = ?
        ');
        $stmt->execute([$barcode, 'active']);
        return $stmt->fetch();
    }

    public function searchByName($name, $limit = 20) {
        $stmt = $this->db->prepare('
            SELECT p.*, c.name as category_name, s.name as supplier_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            WHERE (p.name LIKE ? OR p.barcode LIKE ?) AND p.status = ?
            LIMIT ?
        ');
        $stmt->bindValue(1, '%' . $name . '%', PDO::PARAM_STR);
        $stmt->bindValue(2, '%' . $name . '%', PDO::PARAM_STR);
        $stmt->bindValue(3, 'active', PDO::PARAM_STR);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAll($filter = []) {
        $query = 'SELECT p.*, c.name as category_name, s.name as supplier_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN suppliers s ON p.supplier_id = s.id WHERE 1=1';
        $params = [];

        if (!empty($filter['category_id'])) {
            $query .= ' AND p.category_id = ?';
            $params[] = $filter['category_id'];
        }
        if (!empty($filter['status'])) {
            $query .= ' AND p.status = ?';
            $params[] = $filter['status'];
        } else {
            $query .= ' AND p.status = ?';
            $params[] = 'active';
        }

        $query .= ' ORDER BY p.name ASC';
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare('
            INSERT INTO products (barcode, name, category_id, supplier_id, buying_price, selling_price, 
                                 cigarette_price, cigarettes_per_pack, quantity, minimum_stock, image, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['barcode'] ?? null,
            $data['name'],
            $data['category_id'] ?? null,
            $data['supplier_id'] ?? null,
            $data['buying_price'] ?? 0,
            $data['selling_price'] ?? 0,
            $data['cigarette_price'] ?? 0,
            $data['cigarettes_per_pack'] ?? 20,
            $data['quantity'] ?? 0,
            $data['minimum_stock'] ?? 5,
            $data['image'] ?? 'default_product.png',
            $data['description'] ?? '',
            'active'
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare('
            UPDATE products SET barcode = ?, name = ?, category_id = ?, supplier_id = ?, 
                               buying_price = ?, selling_price = ?, cigarette_price = ?, 
                               cigarettes_per_pack = ?, minimum_stock = ?, image = ?, 
                               description = ?, status = ? WHERE id = ?
        ');
        return $stmt->execute([
            $data['barcode'] ?? null,
            $data['name'],
            $data['category_id'] ?? null,
            $data['supplier_id'] ?? null,
            $data['buying_price'] ?? 0,
            $data['selling_price'] ?? 0,
            $data['cigarette_price'] ?? 0,
            $data['cigarettes_per_pack'] ?? 20,
            $data['minimum_stock'] ?? 5,
            $data['image'] ?? 'default_product.png',
            $data['description'] ?? '',
            $data['status'] ?? 'active',
            $id
        ]);
    }

    public function updateQuantity($id, $quantity, $type = 'set') {
        if ($type === 'set') {
            $stmt = $this->db->prepare('UPDATE products SET quantity = ? WHERE id = ?');
            return $stmt->execute([$quantity, $id]);
        } elseif ($type === 'increment') {
            $stmt = $this->db->prepare('UPDATE products SET quantity = quantity + ? WHERE id = ?');
            return $stmt->execute([$quantity, $id]);
        } elseif ($type === 'decrement') {
            $stmt = $this->db->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?');
            return $stmt->execute([$quantity, $id]);
        }
        return false;
    }

    public function getLowStockProducts($threshold = 5) {
        $stmt = $this->db->prepare('
            SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.quantity <= ? AND p.status = ?
            ORDER BY p.quantity ASC
        ');
        $stmt->execute([$threshold, 'active']);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare('UPDATE products SET status = ? WHERE id = ?');
        return $stmt->execute(['inactive', $id]);
    }

    public function getTobaccoProducts() {
        $stmt = $this->db->prepare('
            SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE c.name = ? AND p.status = ?
            ORDER BY p.name ASC
        ');
        $stmt->execute(['Tobacco', 'active']);
        return $stmt->fetchAll();
    }
}
