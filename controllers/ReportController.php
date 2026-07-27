<?php
/**
 * Report Controller
 * Handles report generation and viewing
 */

require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/SaleItem.php';
require_once __DIR__ . '/../models/Settings.php';

class ReportController {
    private $saleModel;
    private $saleItemModel;
    private $settingsModel;
    
    public function __construct() {
        $this->saleModel = new Sale();
        $this->saleItemModel = new SaleItem();
        $this->settingsModel = new Settings();
    }
    
    public function index() {
        $page_title = 'Rapports';
        $content = __DIR__ . '/../views/reports/index.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function daily() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $filters = [
            'date_from' => $date,
            'date_to' => $date,
            'status' => 'completed'
        ];
        
        $sales = $this->saleModel->getSalesWithDetails($filters);
        $currency = $this->settingsModel->getCurrency();
        
        $total_revenue = array_sum(array_column($sales, 'total'));
        $total_profit = 0;
        $total_products = 0;
        
        foreach ($sales as $sale) {
            $items = $this->saleItemModel->getBySaleId($sale['id']);
            $total_profit += array_sum(array_column($items, 'profit'));
            $total_products += array_sum(array_column($items, 'quantity'));
        }
        
        $report = [
            'title' => 'Daily Report',
            'date' => $date,
            'sales' => $sales,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit,
            'total_products' => $total_products,
            'total_transactions' => count($sales)
        ];
        
        $page_title = 'Rapport journalier';
        $content = __DIR__ . '/../views/reports/daily.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function weekly() {
        $week = $_GET['week'] ?? date('Y-\W');
        $year = substr($week, 0, 4);
        $week_num = substr($week, 5);
        
        // Get week start and end dates
        $week_start = date('Y-m-d', strtotime($year . 'W' . $week_num . '1'));
        $week_end = date('Y-m-d', strtotime($year . 'W' . $week_num . '7'));
        
        $filters = [
            'date_from' => $week_start,
            'date_to' => $week_end,
            'status' => 'completed'
        ];
        
        $sales = $this->saleModel->getSalesWithDetails($filters);
        $currency = $this->settingsModel->getCurrency();
        
        $total_revenue = array_sum(array_column($sales, 'total'));
        $total_profit = 0;
        $total_products = 0;
        
        foreach ($sales as $sale) {
            $items = $this->saleItemModel->getBySaleId($sale['id']);
            $total_profit += array_sum(array_column($items, 'profit'));
            $total_products += array_sum(array_column($items, 'quantity'));
        }
        
        $report = [
            'title' => 'Weekly Report',
            'date_range' => $week_start . ' to ' . $week_end,
            'sales' => $sales,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit,
            'total_products' => $total_products,
            'total_transactions' => count($sales)
        ];
        
        $page_title = 'Rapport hebdomadaire';
        $content = __DIR__ . '/../views/reports/weekly.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function monthly() {
        $month = $_GET['month'] ?? date('Y-m');
        $month_start = $month . '-01';
        $month_end = date('Y-m-t', strtotime($month_start));
        
        $filters = [
            'date_from' => $month_start,
            'date_to' => $month_end,
            'status' => 'completed'
        ];
        
        $sales = $this->saleModel->getSalesWithDetails($filters);
        $currency = $this->settingsModel->getCurrency();
        
        $total_revenue = array_sum(array_column($sales, 'total'));
        $total_profit = 0;
        $total_products = 0;
        
        foreach ($sales as $sale) {
            $items = $this->saleItemModel->getBySaleId($sale['id']);
            $total_profit += array_sum(array_column($items, 'profit'));
            $total_products += array_sum(array_column($items, 'quantity'));
        }
        
        $report = [
            'title' => 'Monthly Report',
            'date_range' => $month_start . ' to ' . $month_end,
            'sales' => $sales,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit,
            'total_products' => $total_products,
            'total_transactions' => count($sales)
        ];
        
        $page_title = 'Rapport mensuel';
        $content = __DIR__ . '/../views/reports/monthly.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function yearly() {
        $year = $_GET['year'] ?? date('Y');
        $year_start = $year . '-01-01';
        $year_end = $year . '-12-31';
        
        $filters = [
            'date_from' => $year_start,
            'date_to' => $year_end,
            'status' => 'completed'
        ];
        
        $sales = $this->saleModel->getSalesWithDetails($filters);
        $currency = $this->settingsModel->getCurrency();
        
        $total_revenue = array_sum(array_column($sales, 'total'));
        $total_profit = 0;
        $total_products = 0;
        
        foreach ($sales as $sale) {
            $items = $this->saleItemModel->getBySaleId($sale['id']);
            $total_profit += array_sum(array_column($items, 'profit'));
            $total_products += array_sum(array_column($items, 'quantity'));
        }
        
        $report = [
            'title' => 'Yearly Report',
            'date_range' => $year_start . ' to ' . $year_end,
            'sales' => $sales,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit,
            'total_products' => $total_products,
            'total_transactions' => count($sales)
        ];
        
        $page_title = 'Rapport annuel';
        $content = __DIR__ . '/../views/reports/yearly.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function custom() {
        $date_from = $_GET['date_from'] ?? date('Y-m-01');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        
        $filters = [
            'date_from' => $date_from,
            'date_to' => $date_to,
            'status' => 'completed'
        ];
        
        $sales = $this->saleModel->getSalesWithDetails($filters);
        $currency = $this->settingsModel->getCurrency();
        
        $total_revenue = array_sum(array_column($sales, 'total'));
        $total_profit = 0;
        $total_products = 0;
        
        foreach ($sales as $sale) {
            $items = $this->saleItemModel->getBySaleId($sale['id']);
            $total_profit += array_sum(array_column($items, 'profit'));
            $total_products += array_sum(array_column($items, 'quantity'));
        }
        
        $report = [
            'title' => 'Custom Report',
            'date_range' => $date_from . ' to ' . $date_to,
            'sales' => $sales,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit,
            'total_products' => $total_products,
            'total_transactions' => count($sales)
        ];
        
        $page_title = 'Rapport personnalisé';
        $content = __DIR__ . '/../views/reports/custom.php';
        include __DIR__ . '/../views/layout.php';
    }
    
    public function export() {
        $reportType = $_GET['type'] ?? 'daily';
        $format = $_GET['format'] ?? 'pdf';
        
        // Generate report based on type
        switch ($reportType) {
            case 'daily':
                $this->daily();
                break;
            case 'weekly':
                $this->weekly();
                break;
            case 'monthly':
                $this->monthly();
                break;
            case 'yearly':
                $this->yearly();
                break;
            case 'custom':
                $this->custom();
                break;
            default:
                $this->daily();
        }
    }
}
