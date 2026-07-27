<?php
/**
 * Stock Controller
 * Handles stock history viewing
 */

require_once __DIR__ . '/../models/StockHistory.php';

class StockController {
    private $stockHistoryModel;
    
    public function __construct() {
        $this->stockHistoryModel = new StockHistory();
    }
    
    public function index() {
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;
        
        if ($date_from && $date_to) {
            $movements = $this->stockHistoryModel->getByDateRange($date_from, $date_to);
        } else {
            $movements = $this->stockHistoryModel->getRecentMovements(50);
        }
        
        $page_title = 'Historique des stocks';
        $content = __DIR__ . '/../views/stock/index.php';
        include __DIR__ . '/../views/layout.php';
    }
}
