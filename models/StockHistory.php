<?php
/**
 * StockHistory Model
 * Handles stock movement history
 */

require_once __DIR__ . '/Model.php';

class StockHistory extends Model {
    protected $table = 'stock_history';
    protected $primaryKey = 'id';
    
    /**
     * Get stock history by product
     * @param int $product_id
     * @return array
     */
    public function getByProduct($product_id) {
        $sql = "SELECT sh.*, u.full_name as user_name, p.name as product_name 
                FROM {$this->table} sh 
                LEFT JOIN users u ON sh.user_id = u.id 
                LEFT JOIN products p ON sh.product_id = p.id 
                WHERE sh.product_id = :product_id 
                ORDER BY sh.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent stock movements
     * @param int $limit
     * @return array
     */
    public function getRecentMovements($limit = 20) {
        $sql = "SELECT sh.*, u.full_name as user_name, p.name as product_name 
                FROM {$this->table} sh 
                LEFT JOIN users u ON sh.user_id = u.id 
                LEFT JOIN products p ON sh.product_id = p.id 
                ORDER BY sh.created_at DESC 
                LIMIT {$limit}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get stock movements by date range
     * @param string $date_from
     * @param string $date_to
     * @return array
     */
    public function getByDateRange($date_from, $date_to) {
        $sql = "SELECT sh.*, u.full_name as user_name, p.name as product_name 
                FROM {$this->table} sh 
                LEFT JOIN users u ON sh.user_id = u.id 
                LEFT JOIN products p ON sh.product_id = p.id 
                WHERE DATE(sh.created_at) >= :date_from 
                AND DATE(sh.created_at) <= :date_to 
                ORDER BY sh.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date_from' => $date_from, 'date_to' => $date_to]);
        return $stmt->fetchAll();
    }
    
    /**
     * Add stock movement
     * @param array $data
     * @return int|false
     */
    public function addMovement($data) {
        return $this->create($data);
    }
    
    /**
     * Get stock in movements
     * @param int $product_id
     * @return int
     */
    public function getTotalIn($product_id) {
        $sql = "SELECT SUM(quantity) as total FROM {$this->table} 
                WHERE product_id = :product_id AND type = 'in'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }
    
    /**
     * Get stock out movements
     * @param int $product_id
     * @return int
     */
    public function getTotalOut($product_id) {
        $sql = "SELECT SUM(quantity) as total FROM {$this->table} 
                WHERE product_id = :product_id AND type = 'out'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }
}
