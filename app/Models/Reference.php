<?php
/**
 * Category Model
 */

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = 'active') {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE status = ? ORDER BY name ASC');
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $description = '') {
        $stmt = $this->db->prepare('INSERT INTO categories (name, description, status) VALUES (?, ?, ?)');
        return $stmt->execute([$name, $description, 'active']);
    }

    public function update($id, $name, $description = '') {
        $stmt = $this->db->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
        return $stmt->execute([$name, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('UPDATE categories SET status = ? WHERE id = ?');
        return $stmt->execute(['inactive', $id]);
    }
}

/**
 * Supplier Model
 */

class Supplier {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = 'active') {
        $stmt = $this->db->prepare('SELECT * FROM suppliers WHERE status = ? ORDER BY name ASC');
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('SELECT * FROM suppliers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('
            INSERT INTO suppliers (name, contact_person, phone, email, address, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['name'],
            $data['contact_person'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            'active'
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare('
            UPDATE suppliers SET name = ?, contact_person = ?, phone = ?, email = ?, address = ? 
            WHERE id = ?
        ');
        return $stmt->execute([
            $data['name'],
            $data['contact_person'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('UPDATE suppliers SET status = ? WHERE id = ?');
        return $stmt->execute(['inactive', $id]);
    }
}

/**
 * Customer Model
 */

class Customer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($status = 'active') {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE status = ? ORDER BY name ASC');
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByPhone($phone) {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE phone = ? AND status = ?');
        $stmt->execute([$phone, 'active']);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('
            INSERT INTO customers (name, phone, email, address, status) 
            VALUES (?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['name'],
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            'active'
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare('
            UPDATE customers SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?
        ');
        return $stmt->execute([
            $data['name'],
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('UPDATE customers SET status = ? WHERE id = ?');
        return $stmt->execute(['inactive', $id]);
    }

    public function getDefaultCustomer() {
        $stmt = $this->db->prepare('SELECT * FROM customers WHERE name = ? LIMIT 1');
        $stmt->execute(['Passager / Comptoir']);
        return $stmt->fetch();
    }
}
