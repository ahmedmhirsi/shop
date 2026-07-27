<?php
/**
 * SaleItem Model
 */

class SaleItem {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare('
            INSERT INTO sale_items (sale_id, product_id, quantity, buying_price, selling_price, 
                                   subtotal, profit, unit_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['sale_id'],
            $data['product_id'],
            $data['quantity'],
            $data['buying_price'],
            $data['selling_price'],
            $data['subtotal'],
            $data['profit'],
            $data['unit_type'] ?? 'pack'
        ]);
    }

    public function getBySaleId($sale_id) {
        $stmt = $this->db->prepare('
            SELECT si.*, p.name, p.barcode, c.name as category_name 
            FROM sale_items si 
            JOIN products p ON si.product_id = p.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE si.sale_id = ?
        ');
        $stmt->execute([$sale_id]);
        return $stmt->fetchAll();
    }

    public function getTopSellingProducts($limit = 10, $days = 30) {
        $stmt = $this->db->prepare('
            SELECT p.id, p.name, p.barcode, COUNT(si.id) as times_sold, SUM(si.quantity) as total_qty, 
                   SUM(si.subtotal) as revenue, SUM(si.profit) as profit
            FROM sale_items si 
            JOIN products p ON si.product_id = p.id 
            JOIN sales s ON si.sale_id = s.id 
            WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) AND s.status = ?
            GROUP BY p.id 
            ORDER BY SUM(si.subtotal) DESC 
            LIMIT ?
        ');
        $stmt->bindValue(1, $days, PDO::PARAM_INT);
        $stmt->bindValue(2, 'completed', PDO::PARAM_STR);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoryStats($days = 30) {
        $stmt = $this->db->prepare('
            SELECT c.name, COUNT(si.id) as total_items, SUM(si.quantity) as total_qty, 
                   SUM(si.subtotal) as revenue, SUM(si.profit) as profit
            FROM sale_items si 
            JOIN products p ON si.product_id = p.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            JOIN sales s ON si.sale_id = s.id 
            WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) AND s.status = ?
            GROUP BY c.id 
            ORDER BY SUM(si.subtotal) DESC
        ');
        $stmt->execute([$days, 'completed']);
        return $stmt->fetchAll();
    }

    public function getTobaccoInsights($startDate, $endDate) {
        $stmt = $this->db->prepare('
            SELECT si.unit_type, 
                   SUM(si.quantity) as quantity, 
                   SUM(si.subtotal) as revenue, 
                   SUM(si.profit) as profit
            FROM sale_items si
            JOIN sales s ON si.sale_id = s.id
            WHERE s.status = ?
              AND si.unit_type IN (?, ?)
              AND DATE(s.created_at) BETWEEN ? AND ?
            GROUP BY si.unit_type
        ');
        $stmt->execute(['completed', 'pack', 'cigarette', $startDate, $endDate]);
        $rows = $stmt->fetchAll();

        $insights = [
            'pack' => ['quantity' => 0, 'revenue' => 0, 'profit' => 0],
            'cigarette' => ['quantity' => 0, 'revenue' => 0, 'profit' => 0]
        ];

        foreach ($rows as $row) {
            $type = $row['unit_type'] ?? 'pack';
            if (!isset($insights[$type])) {
                continue;
            }
            $insights[$type] = [
                'quantity' => (float)$row['quantity'],
                'revenue' => (float)$row['revenue'],
                'profit' => (float)$row['profit']
            ];
        }

        return $insights;
    }
}
