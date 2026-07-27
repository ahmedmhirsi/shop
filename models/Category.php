<?php
/**
 * Category Model
 * Handles category management
 */

require_once __DIR__ . '/Model.php';

class Category extends Model {
    protected $table = 'categories';
    protected $primaryKey = 'id';
    
    /**
     * Get active categories
     * @return array
     */
    public function getActiveCategories() {
        return $this->getAll(['status' => 'active'], 'name ASC');
    }
    
    /**
     * Get category with product count
     * @return array
     */
    public function getCategoryWithProductCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                WHERE c.status = 'active'
                GROUP BY c.id 
                ORDER BY c.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Check if category name exists
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
     * Deactivate category
     * @param int $id
     * @return bool
     */
    public function deactivate($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Activate category
     * @param int $id
     * @return bool
     */
    public function activate($id) {
        return $this->update($id, ['status' => 'active']);
    }
}
