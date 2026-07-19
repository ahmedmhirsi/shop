<?php
/**
 * SaleItem Model
 * Handles sale items management
 */

require_once __DIR__ . '/Model.php';

class SaleItem extends Model {
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    
    /**
     * Get sale items by sale ID
     * @param int $sale_id
     * @return array
     */
    public function getBySaleId($sale_id) {
        $sql = "SELECT si.*, p.name as product_name, p.barcode 
                FROM {$this->table} si 
                INNER JOIN products p ON si.product_id = p.id 
                WHERE si.sale_id = :sale_id 
                ORDER BY si.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sale_id' => $sale_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get sale items with product details
     * @param int $sale_id
     * @return array
     */
    public function getSaleItemsWithProduct($sale_id) {
        $sql = "SELECT si.*, p.name as product_name, p.barcode, c.name as category_name 
                FROM {$this->table} si 
                INNER JOIN products p ON si.product_id = p.id 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE si.sale_id = :sale_id 
                ORDER BY si.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sale_id' => $sale_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total items sold for a product
     * @param int $product_id
     * @return int
     */
    public function getTotalSoldByProduct($product_id) {
        $sql = "SELECT SUM(si.quantity) as total_sold 
                FROM {$this->table} si 
                INNER JOIN sales s ON si.sale_id = s.id 
                WHERE si.product_id = :product_id AND s.status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        $result = $stmt->fetch();
        return (int) ($result['total_sold'] ?? 0);
    }
    
    /**
     * Get total profit by product
     * @param int $product_id
     * @return float
     */
    public function getTotalProfitByProduct($product_id) {
        $sql = "SELECT SUM(si.profit) as total_profit 
                FROM {$this->table} si 
                INNER JOIN sales s ON si.sale_id = s.id 
                WHERE si.product_id = :product_id AND s.status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_id' => $product_id]);
        $result = $stmt->fetch();
        return (float) ($result['total_profit'] ?? 0);
    }
    
    /**
     * Create multiple sale items
     * @param array $items
     * @return bool
     */
    public function createMultiple($items) {
        $sql = "INSERT INTO {$this->table} (sale_id, product_id, quantity, buying_price, selling_price, subtotal, profit, unit_type) 
                VALUES (:sale_id, :product_id, :quantity, :buying_price, :selling_price, :subtotal, :profit, :unit_type)";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($items as $item) {
            $stmt->execute([
                'sale_id' => $item['sale_id'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'buying_price' => $item['buying_price'],
                'selling_price' => $item['selling_price'],
                'subtotal' => $item['subtotal'],
                'profit' => $item['profit'],
                'unit_type' => $item['unit_type'] ?? 'pack'
            ]);
        }
        
        return true;
    }
}
