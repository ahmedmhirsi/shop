<?php
/**
 * User Model
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->prepare('SELECT id, username, full_name, role, status, created_at FROM users ORDER BY id DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($username, $password, $full_name, $role = 'employee') {
        $stmt = $this->db->prepare('
            INSERT INTO users (username, password, full_name, role, status) 
            VALUES (?, ?, ?, ?, ?)
        ');
        return $stmt->execute([$username, $password, $full_name, $role, 'active']);
    }

    public function update($id, $full_name, $role, $status) {
        $stmt = $this->db->prepare('
            UPDATE users SET full_name = ?, role = ?, status = ? WHERE id = ?
        ');
        return $stmt->execute([$full_name, $role, $status, $id]);
    }

    public function updatePassword($id, $new_password) {
        $hashed = SecurityHelper::hashPassword($new_password);
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$hashed, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function toggleStatus($id) {
        $user = $this->findById($id);
        if (!$user) return false;
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        return $this->update($id, $user['full_name'], $user['role'], $newStatus);
    }

    public function login($username, $password) {
        $user = $this->findByUsername($username);
        if ($user && SecurityHelper::verifyPassword($password, $user['password']) && $user['status'] === 'active') {
            return $user;
        }
        return false;
    }
}
