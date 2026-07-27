<?php
/**
 * Sale Model
 * Handles sales management
 */

require_once __DIR__ . '/Model.php';

class Sale extends Model {
    protected $table = 'sales';
    protected $primaryKey = 'id';
    
    /**
     * Get sales with details
     * @param array $filters
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getSalesWithDetails($filters = [], $order = 's.created_at DESC', $limit = null, $offset = null) {
        $sql = "SELECT s.*, u.full_name as cashier_name, c.name as customer_name 
                FROM {$this->table} s 
                LEFT JOIN users u ON s.user_id = u.id 
                LEFT JOIN customers c ON s.customer_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND s.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(s.created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(s.created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['invoice_number'])) {
            $sql .= " AND s.invoice_number LIKE :invoice_number";
            $params['invoice_number'] = "%{$filters['invoice_number']}%";
        }
        
        $sql .= " ORDER BY {$order}";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        if ($offset) {
            $sql .= " OFFSET {$offset}";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get sale by invoice number
     * @param string $invoice_number
     * @return array|false
     */
    public function getByInvoiceNumber($invoice_number) {
        $sql = "SELECT s.*, u.full_name as cashier_name, c.name as customer_name 
                FROM {$this->table} s 
                LEFT JOIN users u ON s.user_id = u.id 
                LEFT JOIN customers c ON s.customer_id = c.id 
                WHERE s.invoice_number = :invoice_number 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['invoice_number' => $invoice_number]);
        return $stmt->fetch();
    }
    
    /**
     * Get today's sales
     * @return array
     */
    public function getTodaySales() {
        return $this->getSalesWithDetails([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
            'status' => 'completed'
        ]);
    }
    
    /**
     * Get today's revenue
     * @return float
     */
    public function getTodayRevenue() {
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} 
                WHERE DATE(created_at) = CURDATE() AND status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['revenue'] ?? 0);
    }
    
    /**
     * Get today's profit
     * @return float
     */
    public function getTodayProfit() {
        $sql = "SELECT SUM(si.profit) as profit 
                FROM {$this->table} s 
                INNER JOIN sale_items si ON s.id = si.sale_id 
                WHERE DATE(s.created_at) = CURDATE() AND s.status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['profit'] ?? 0);
    }
    
    /**
     * Get week revenue
     * @return float
     */
    public function getWeekRevenue() {
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} 
                WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['revenue'] ?? 0);
    }
    
    /**
     * Get month revenue
     * @return float
     */
    public function getMonthRevenue() {
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} 
                WHERE MONTH(created_at) = MONTH(CURDATE()) 
                AND YEAR(created_at) = YEAR(CURDATE()) 
                AND status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['revenue'] ?? 0);
    }
    
    /**
     * Get year revenue
     * @return float
     */
    public function getYearRevenue() {
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} 
                WHERE YEAR(created_at) = YEAR(CURDATE()) AND status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['revenue'] ?? 0);
    }
    
    /**
     * Get total revenue
     * @return float
     */
    public function getTotalRevenue() {
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} WHERE status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['revenue'] ?? 0);
    }
    
    /**
     * Get total sales count
     * @return int
     */
    public function getTotalSalesCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result['count'];
    }
    
    /**
     * Get recent sales
     * @param int $limit
     * @return array
     */
    public function getRecentSales($limit = 10) {
        return $this->getSalesWithDetails(['status' => 'completed'], 's.created_at DESC', $limit);
    }
    
    /**
     * Get revenue by month
     * @param int $year
     * @return array
     */
    public function getRevenueByMonth($year = null) {
        $year = $year ?? date('Y');
        $sql = "SELECT MONTH(created_at) as month, SUM(total) as revenue 
                FROM {$this->table} 
                WHERE YEAR(created_at) = :year AND status = 'completed'
                GROUP BY MONTH(created_at) 
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['year' => $year]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get sales by month
     * @param int $year
     * @return array
     */
    public function getSalesByMonth($year = null) {
        $year = $year ?? date('Y');
        $sql = "SELECT MONTH(created_at) as month, COUNT(*) as sales 
                FROM {$this->table} 
                WHERE YEAR(created_at) = :year AND status = 'completed'
                GROUP BY MONTH(created_at) 
                ORDER BY month ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['year' => $year]);
        return $stmt->fetchAll();
    }

    /**
     * Get performance by shift (today - 24h operating window)
     * Shifts:
     * - Shift 1: 07:00 - 16:00
     * - Shift 2: 16:00 - 22:00
     * - Shift 3: 22:00 - 07:00 (traverse minuit : 22h-23h59 du jour courant + 00h-06h59 du lendemain)
     *
     * La "journée d'exploitation" est calée sur le début du Shift 1 (07:00).
     * On regarde donc la fenêtre [aujourd'hui 07:00 -> demain 07:00[ pour être sûr
     * de capturer le Shift 3 en entier (avant ET après minuit) sans le couper.
     *
     * @return array
     */
    public function getShiftPerformance() {
        // Fenêtre d'exploitation : de 07:00 aujourd'hui à 07:00 demain (24h)
        $windowStart = date('Y-m-d') . ' 00:00:00'; // borne large, le filtrage fin se fait via CASE sur TIME()
        $windowEnd = date('Y-m-d', strtotime('+1 day')) . ' 23:59:59';

        $sql = "
            SELECT 
                CASE
                    WHEN TIME(s.created_at) >= '07:00:00' AND TIME(s.created_at) < '16:00:00' THEN 1
                    WHEN TIME(s.created_at) >= '16:00:00' AND TIME(s.created_at) < '22:00:00' THEN 2
                    ELSE 3
                END AS shift_id,
                SUM(s.total) AS revenue,
                COUNT(*) AS sales_count,
                SUM(COALESCE(si.item_profit, 0)) AS profit
            FROM {$this->table} s
            LEFT JOIN (
                SELECT sale_id, SUM(
                    COALESCE(profit, (COALESCE(selling_price, 0) - COALESCE(buying_price, 0)) * COALESCE(quantity, 1))
                ) AS item_profit
                FROM sale_items
                GROUP BY sale_id
            ) si ON si.sale_id = s.id
            WHERE s.status = 'completed'
              AND (
                    -- Shift 1 et 2 : dans la journée calendaire du jour courant
                    (DATE(s.created_at) = CURDATE() AND TIME(s.created_at) >= '07:00:00')
                    OR
                    -- Shift 3 partie 1 : 22:00-23:59 du jour courant
                    (DATE(s.created_at) = CURDATE() AND TIME(s.created_at) >= '22:00:00')
                    OR
                    -- Shift 3 partie 2 : 00:00-06:59 du lendemain (nuit en cours)
                    (DATE(s.created_at) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND TIME(s.created_at) < '07:00:00')
              )
            GROUP BY shift_id
            ORDER BY shift_id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $byShift = [
            1 => ['shift_id' => 1, 'revenue' => 0.0, 'sales_count' => 0, 'profit' => 0.0],
            2 => ['shift_id' => 2, 'revenue' => 0.0, 'sales_count' => 0, 'profit' => 0.0],
            3 => ['shift_id' => 3, 'revenue' => 0.0, 'sales_count' => 0, 'profit' => 0.0],
        ];

        foreach ($rows as $row) {
            $shiftId = (int) ($row['shift_id'] ?? 0);
            if (!isset($byShift[$shiftId])) {
                continue;
            }

            $byShift[$shiftId]['revenue'] = (float) ($row['revenue'] ?? 0);
            $byShift[$shiftId]['sales_count'] = (int) ($row['sales_count'] ?? 0);
            $byShift[$shiftId]['profit'] = (float) ($row['profit'] ?? 0);
        }

        // Ticket moyen = CA / nombre de ventes (protection division par zéro)
        foreach ($byShift as $shiftId => $data) {
            $salesCount = (int) $data['sales_count'];
            $revenue = (float) $data['revenue'];
            $ticket = $salesCount > 0 ? ($revenue / $salesCount) : 0.0;
            $byShift[$shiftId]['ticket_avg'] = $ticket;
        }

        return array_values($byShift);
    }

    /**
     * Get shift performance history over a date range
     * Shifts:
     * - Shift 1: 07:00 - 16:00
     * - Shift 2: 16:00 - 22:00
     * - Shift 3: 22:00 - 07:00 (traverse minuit)
     *
     * Pour chaque "journée d'exploitation" (calée sur 07:00), le Shift 3 regroupe :
     *   22:00 -> 23:59 du jour J   +   00:00 -> 06:59 du jour J+1
     * On rattache donc cette portion du lendemain à la journée J (business_date),
     * afin que le Shift 3 apparaisse comme UNE seule ligne par jour, pas deux.
     *
     * @param string $date_from Format Y-m-d
     * @param string $date_to Format Y-m-d
     * @return array Liste de lignes : business_date, shift_id, revenue, sales_count, profit, ticket_avg
     */
    public function getShiftHistory($date_from, $date_to) {
        $sql = "
            SELECT
                business_date,
                shift_id,
                SUM(revenue) AS revenue,
                SUM(sales_count) AS sales_count,
                SUM(profit) AS profit
            FROM (
                SELECT
                    CASE
                        WHEN TIME(s.created_at) < '07:00:00'
                            THEN DATE_SUB(DATE(s.created_at), INTERVAL 1 DAY)
                        ELSE DATE(s.created_at)
                    END AS business_date,
                    CASE
                        WHEN TIME(s.created_at) >= '07:00:00' AND TIME(s.created_at) < '16:00:00' THEN 1
                        WHEN TIME(s.created_at) >= '16:00:00' AND TIME(s.created_at) < '22:00:00' THEN 2
                        ELSE 3
                    END AS shift_id,
                    s.total AS revenue,
                    1 AS sales_count,
                    COALESCE(si.item_profit, 0) AS profit
                FROM {$this->table} s
                LEFT JOIN (
                    SELECT sale_id, SUM(
                        COALESCE(profit, (COALESCE(selling_price, 0) - COALESCE(buying_price, 0)) * COALESCE(quantity, 1))
                    ) AS item_profit
                    FROM sale_items
                    GROUP BY sale_id
                ) si ON si.sale_id = s.id
                WHERE s.status = 'completed'
                  AND DATE(s.created_at) BETWEEN DATE_SUB(:date_from, INTERVAL 1 DAY) AND DATE_ADD(:date_to, INTERVAL 1 DAY)
            ) t
            WHERE business_date BETWEEN :date_from2 AND :date_to2
            GROUP BY business_date, shift_id
            ORDER BY business_date ASC, shift_id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date_from', $date_from);
        $stmt->bindValue(':date_to', $date_to);
        $stmt->bindValue(':date_from2', $date_from);
        $stmt->bindValue(':date_to2', $date_to);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $history = [];
        foreach ($rows as $row) {
            $revenue = (float) ($row['revenue'] ?? 0);
            $salesCount = (int) ($row['sales_count'] ?? 0);
            $profit = (float) ($row['profit'] ?? 0);
            $ticketAvg = $salesCount > 0 ? ($revenue / $salesCount) : 0.0;

            $history[] = [
                'business_date' => $row['business_date'],
                'shift_id' => (int) $row['shift_id'],
                'revenue' => $revenue,
                'sales_count' => $salesCount,
                'profit' => $profit,
                'ticket_avg' => $ticketAvg,
            ];
        }

        return $history;
    }

    /**
     * Cancel sale
     * @param int $id
     * @return bool
     */
    public function cancelSale($id) {
        return $this->update($id, ['status' => 'cancelled']);
    }
    
    /**
     * Count sales with filters
     * @param array $filters
     * @return int
     */
    public function countFiltered($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['invoice_number'])) {
            $sql .= " AND invoice_number LIKE :invoice_number";
            $params['invoice_number'] = "%{$filters['invoice_number']}%";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }
}

