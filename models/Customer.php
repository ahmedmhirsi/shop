<?php
/**
 * Customer Model
 * Handles customer management
 */

require_once __DIR__ . '/Model.php';

class Customer extends Model {
    protected $table = 'customers';
    protected $primaryKey = 'id';
    
    /**
     * Get active customers
     * @return array
     */
    public function getActiveCustomers() {
        return $this->getAll(['status' => 'active'], 'name ASC');
    }
    
    /**
     * Search customers
     * @param string $search
     * @return array
     */
    public function search($search) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                AND (name LIKE :search OR phone LIKE :search OR email LIKE :search)
                ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['search' => "%{$search}%"]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if customer phone exists
     * @param string $phone
     * @param int $exclude_id
     * @return bool
     */
    public function phoneExists($phone, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE phone = :phone";
        $params = ['phone' => $phone];
        
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
     * Deactivate customer
     * @param int $id
     * @return bool
     */
    public function deactivate($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Activate customer
     * @param int $id
     * @return bool
     */
    public function activate($id) {
        return $this->update($id, ['status' => 'active']);
    }
}
