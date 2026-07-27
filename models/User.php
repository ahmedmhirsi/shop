<?php
/**
 * User Model
 * Handles user authentication and user management
 */

require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    /**
     * Authenticate user
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE username = :username AND status = 'active' 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create new user
     * @param array $data
     * @return int|false
     */
    public function register($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }
    
    /**
     * Update user password
     * @param int $id
     * @param string $new_password
     * @return bool
     */
    public function updatePassword($id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashed_password]);
    }
    
    /**
     * Get all users with role filter
     * @param string $role
     * @return array
     */
    public function getByRole($role) {
        return $this->getAll(['role' => $role], 'created_at DESC');
    }
    
    /**
     * Get active users
     * @return array
     */
    public function getActiveUsers() {
        return $this->getAll(['status' => 'active'], 'created_at DESC');
    }
    
    /**
     * Deactivate user
     * @param int $id
     * @return bool
     */
    public function deactivate($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Activate user
     * @param int $id
     * @return bool
     */
    public function activate($id) {
        return $this->update($id, ['status' => 'active']);
    }
    
    /**
     * Check if username exists
     * @param string $username
     * @param int $exclude_id
     * @return bool
     */
    public function usernameExists($username, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        $params = ['username' => $username];
        
        if ($exclude_id) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $exclude_id;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
}
