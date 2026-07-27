<?php
/**
 * Sale Model
 */

class Sale {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id) {
        $stmt = $this->db->prepare('
            SELECT s.*, u.username, u.full_name, c.name as customer_name 
            FROM sales s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN customers c ON s.customer_id = c.id 
            WHERE s.id = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByInvoiceNumber($invoice_number) {
        $stmt = $this->db->prepare('
            SELECT s.*, u.username, u.full_name, c.name as customer_name 
            FROM sales s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN customers c ON s.customer_id = c.id 
            WHERE s.invoice_number = ?
        ');
        $stmt->execute([$invoice_number]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare('
            INSERT INTO sales (invoice_number, customer_id, user_id, subtotal, discount, tax, total, 
                              payment_method, amount_received, `change`, notes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['invoice_number'],
            $data['customer_id'] ?? null,
            $data['user_id'],
            $data['subtotal'],
            $data['discount'] ?? 0,
            $data['tax'] ?? 0,
            $data['total'],
            $data['payment_method'] ?? 'cash',
            $data['amount_received'],
            $data['change'] ?? 0,
            $data['notes'] ?? '',
            'completed'
        ]);
    }

    public function getLastSaleId() {
        $stmt = $this->db->prepare('SELECT id FROM sales ORDER BY id DESC LIMIT 1');
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['id'] : 0;
    }

    public function getConnection() {
        return $this->db;
    }

    public function getAll($filter = []) {
        $query = 'SELECT s.*, u.username, u.full_name, c.name as customer_name 
                  FROM sales s 
                  LEFT JOIN users u ON s.user_id = u.id 
                  LEFT JOIN customers c ON s.customer_id = c.id 
                  WHERE 1=1';
        $params = [];

        if (!empty($filter['start_date'])) {
            $query .= ' AND DATE(s.created_at) >= ?';
            $params[] = $filter['start_date'];
        }
        if (!empty($filter['end_date'])) {
            $query .= ' AND DATE(s.created_at) <= ?';
            $params[] = $filter['end_date'];
        }
        if (!empty($filter['user_id'])) {
            $query .= ' AND s.user_id = ?';
            $params[] = $filter['user_id'];
        }

        $query .= ' ORDER BY s.created_at DESC LIMIT 500';
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStats($filter = []) {
        $query = 'SELECT 
                    COUNT(*) as total_sales,
                    SUM(s.total) as total_revenue,
                    SUM(CASE WHEN s.payment_method = "cash" THEN s.total ELSE 0 END) as cash_sales,
                    SUM(CASE WHEN s.payment_method = "card" THEN s.total ELSE 0 END) as card_sales
                  FROM sales s WHERE 1=1';
        $params = [];

        if (!empty($filter['start_date'])) {
            $query .= ' AND DATE(s.created_at) >= ?';
            $params[] = $filter['start_date'];
        }
        if (!empty($filter['end_date'])) {
            $query .= ' AND DATE(s.created_at) <= ?';
            $params[] = $filter['end_date'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getDailyRevenueProfitTrend($startDate, $endDate) {
        $stmt = $this->db->prepare('SELECT DATE(created_at) AS sale_date, SUM(total) AS revenue FROM sales WHERE status = ? AND DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY DATE(created_at)');
        $stmt->execute(['completed', $startDate, $endDate]);
        $revenueRows = $stmt->fetchAll();

        $stmt = $this->db->prepare('SELECT DATE(s.created_at) AS sale_date, SUM(si.profit) AS profit FROM sale_items si JOIN sales s ON si.sale_id = s.id WHERE s.status = ? AND DATE(s.created_at) BETWEEN ? AND ? GROUP BY DATE(s.created_at) ORDER BY DATE(s.created_at)');
        $stmt->execute(['completed', $startDate, $endDate]);
        $profitRows = $stmt->fetchAll();

        $trends = [];
        foreach ($revenueRows as $row) {
            $trends[$row['sale_date']] = [
                'date' => $row['sale_date'],
                'revenue' => (float)$row['revenue'],
                'profit' => 0.0
            ];
        }

        foreach ($profitRows as $row) {
            if (!isset($trends[$row['sale_date']])) {
                $trends[$row['sale_date']] = [
                    'date' => $row['sale_date'],
                    'revenue' => 0.0,
                    'profit' => (float)$row['profit']
                ];
            } else {
                $trends[$row['sale_date']]['profit'] = (float)$row['profit'];
            }
        }

        return array_values($trends);
    }

    public function getProfitStats($filter = []) {
        $query = 'SELECT 
                    SUM(si.profit) as total_profit,
                    COUNT(DISTINCT s.id) as total_transactions
                  FROM sale_items si 
                  JOIN sales s ON si.sale_id = s.id 
                  WHERE 1=1';
        $params = [];

        if (!empty($filter['start_date'])) {
            $query .= ' AND DATE(s.created_at) >= ?';
            $params[] = $filter['start_date'];
        }
        if (!empty($filter['end_date'])) {
            $query .= ' AND DATE(s.created_at) <= ?';
            $params[] = $filter['end_date'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getSalesByDate($date) {
        $stmt = $this->db->prepare('
            SELECT s.*, u.username, u.full_name, c.name as customer_name 
            FROM sales s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN customers c ON s.customer_id = c.id 
            WHERE DATE(s.created_at) = ? 
            ORDER BY s.created_at DESC
        ');
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    public function cancel($id) {
        $sale = $this->findById($id);
        if (!$sale || $sale['status'] === 'cancelled') {
            return false;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('UPDATE sales SET status = ? WHERE id = ?');
            $stmt->execute(['cancelled', $id]);

            $saleItems = new SaleItem();
            $items = $saleItems->getBySaleId($id);
            
            $product = new Product();
            foreach ($items as $item) {
                $product->updateQuantity($item['product_id'], $item['quantity'], 'increment');
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
