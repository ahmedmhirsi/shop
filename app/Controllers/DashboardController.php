<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    private $saleModel;
    private $saleItemModel;
    private $productModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->saleModel = new Sale();
        $this->saleItemModel = new SaleItem();
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    public function index() {
        $today = date('Y-m-d');
        $stats = $this->saleModel->getStats(['start_date' => $today, 'end_date' => $today]);
        $profitStats = $this->saleModel->getProfitStats(['start_date' => $today, 'end_date' => $today]);
        $dailyTrends = $this->saleModel->getDailyRevenueProfitTrend(date('Y-m-d', strtotime('-6 days')), $today);
        $topProducts = $this->saleItemModel->getTopSellingProducts(5, 30);
        $categoryStats = $this->saleItemModel->getCategoryStats(30);
        $lowStockProducts = $this->productModel->getLowStockProducts(5);
        $recentSales = $this->saleModel->getSalesByDate($today);
        $tobaccoInsights = $this->saleItemModel->getTobaccoInsights($today, $today);
 
        $data = [
            'stats' => $stats,
            'profitStats' => $profitStats,
            'dailyTrends' => $dailyTrends,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'lowStockProducts' => $lowStockProducts,
            'recentSales' => $recentSales,
            'tobaccoInsights' => $tobaccoInsights
        ];

        $this->render('dashboard/index', $data);
    }

    public function analytics() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $stats = $this->saleModel->getStats(['start_date' => $startDate, 'end_date' => $endDate]);
        $profitStats = $this->saleModel->getProfitStats(['start_date' => $startDate, 'end_date' => $endDate]);
        $topProducts = $this->saleItemModel->getTopSellingProducts(10, 30);
        $categoryStats = $this->saleItemModel->getCategoryStats(30);
        $sales = $this->saleModel->getAll(['start_date' => $startDate, 'end_date' => $endDate]);

        $data = [
            'stats' => $stats,
            'profitStats' => $profitStats,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $this->render('dashboard/analytics', $data);
    }

    public function users() {
        $this->requireRole('boss');
        $users = $this->userModel->getAll();
        $this->render('dashboard/users', ['users' => $users, 'csrf_token' => SecurityHelper::generateCsrfToken()]);
    }

    public function settings() {
        $this->requireRole('boss');
        $this->render('dashboard/settings', ['csrf_token' => SecurityHelper::generateCsrfToken()]);
    }

    public function getStats() {
        if ($this->method !== 'GET') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
        }

        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $stats = $this->saleModel->getStats(['start_date' => $startDate, 'end_date' => $endDate]);
        $profitStats = $this->saleModel->getProfitStats(['start_date' => $startDate, 'end_date' => $endDate]);
        $dailyTrends = $this->saleModel->getDailyRevenueProfitTrend($startDate, $endDate);
        $lowStockProducts = $this->productModel->getLowStockProducts(5);
        $topProducts = $this->saleItemModel->getTopSellingProducts(5, 30);
        $categoryStats = $this->saleItemModel->getCategoryStats(30);
        $recentSales = $this->saleModel->getAll(['start_date' => $startDate, 'end_date' => $endDate]);
        $tobaccoInsights = $this->saleItemModel->getTobaccoInsights($startDate, $endDate);
 
        $this->jsonResponse([
            'stats' => $stats,
            'profitStats' => $profitStats,
            'dailyTrends' => $dailyTrends,
            'lowStockProducts' => $lowStockProducts,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'recentSales' => array_slice($recentSales, 0, 10),
            'tobaccoInsights' => $tobaccoInsights
        ]);
    }
}
