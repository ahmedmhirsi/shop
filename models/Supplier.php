<?php
/**
 * Supplier Model
 * Handles supplier management
 */

require_once __DIR__ . '/Model.php';

class Supplier extends Model {
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    
    /**
     * Get active suppliers
     * @return array
     */
    public function getActiveSuppliers() {
        return $this->getAll(['status' => 'active'], 'name ASC');
    }
    
    /**
     * Get supplier with product count
     * @return array
     */
    public function getSupplierWithProductCount() {
        $sql = "SELECT s.*, COUNT(p.id) as product_count 
                FROM {$this->table} s 
                LEFT JOIN products p ON s.id = p.supplier_id AND p.status = 'active'
                WHERE s.status = 'active'
                GROUP BY s.id 
                ORDER BY s.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Check if supplier name exists
     * @param string $name
     * @param int $exclude_id
     * @return bool
     */
    public function nameExists($name, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name";
        $params = ['name' => $name];
        
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
     * Deactivate supplier
     * @param int $id
     * @return bool
     */
    public function deactivate($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Activate supplier
     * @param int $id
     * @return bool
     */
    public function activate($id) {
        return $this->update($id, ['status' => 'active']);
    }
}
