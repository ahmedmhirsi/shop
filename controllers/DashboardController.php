<?php
/**
 * Dashboard Controller
 * Handles dashboard statistics and charts
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Settings.php';
require_once __DIR__ . '/../models/Category.php';

class DashboardController {
    private $productModel;
    private $saleModel;
    private $settingsModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->saleModel = new Sale();
        $this->settingsModel = new Settings();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        // Get statistics
        $stats = [
            'today_revenue' => $this->saleModel->getTodayRevenue(),
            'today_sales' => count($this->saleModel->getTodaySales()),
            'today_profit' => $this->saleModel->getTodayProfit(),
            'week_revenue' => $this->saleModel->getWeekRevenue(),
            'month_revenue' => $this->saleModel->getMonthRevenue(),
            'year_revenue' => $this->saleModel->getYearRevenue(),
            'total_revenue' => $this->saleModel->getTotalRevenue(),
            'total_sales' => $this->saleModel->getTotalSalesCount(),
            'total_products' => $this->productModel->count(['status' => 'active']),
            'low_stock' => count($this->productModel->getLowStockProducts()),
            'out_of_stock' => count($this->productModel->getOutOfStockProducts())
        ];

        // Performance by shift (today)
        $shift_stats = $this->saleModel->getShiftPerformance();

        // Variables individuelles par shift (shift1_revenue, shift1_sales, shift1_profit, shift1_average, ...)
        $shift1_revenue = 0.0; $shift1_sales = 0; $shift1_profit = 0.0; $shift1_average = 0.0;
        $shift2_revenue = 0.0; $shift2_sales = 0; $shift2_profit = 0.0; $shift2_average = 0.0;
        $shift3_revenue = 0.0; $shift3_sales = 0; $shift3_profit = 0.0; $shift3_average = 0.0;

        foreach ($shift_stats as $shift) {
            $sid = (int) ($shift['shift_id'] ?? 0);
            $revenue = (float) ($shift['revenue'] ?? 0);
            $salesCount = (int) ($shift['sales_count'] ?? 0);
            $profit = (float) ($shift['profit'] ?? 0);
            $average = (float) ($shift['ticket_avg'] ?? 0);

            switch ($sid) {
                case 1:
                    $shift1_revenue = $revenue;
                    $shift1_sales = $salesCount;
                    $shift1_profit = $profit;
                    $shift1_average = $average;
                    break;
                case 2:
                    $shift2_revenue = $revenue;
                    $shift2_sales = $salesCount;
                    $shift2_profit = $profit;
                    $shift2_average = $average;
                    break;
                case 3:
                    $shift3_revenue = $revenue;
                    $shift3_sales = $salesCount;
                    $shift3_profit = $profit;
                    $shift3_average = $average;
                    break;
            }
        }
        
        // Get recent sales
        $recent_sales = $this->saleModel->getRecentSales(10);
        
        // Get top products
        $top_products = $this->productModel->getTopSellingProducts(5);
        
        // Get revenue by month for chart
        $revenue_by_month = $this->saleModel->getRevenueByMonth();
        $sales_by_month = $this->saleModel->getSalesByMonth();
        
        // Get settings
        $settings = $this->settingsModel->getSettings();
        $currency = $settings['currency'] ?? 'TND ';
        if ($currency === '$') {
            $currency = 'TND ';
        }
        
        // Get low stock products
        $low_stock_products = $this->productModel->getLowStockProducts();
        
        // Pass data to view
        $page_title = 'Tableau de bord';
        $content = __DIR__ . '/../views/dashboard.php';
        
        include __DIR__ . '/../views/layout.php';
    }
}
