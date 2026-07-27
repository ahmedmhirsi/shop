<?php $page_title = 'Dashboard'; ob_start(); ?>

<div class="dashboard-container">
    <!-- Top Header Section -->
    <div class="dashboard-header">
        <div class="dashboard-header-left">
            <h1 class="dashboard-greeting">Bonjour, <?php echo SecurityHelper::escapeHtml($_SESSION['full_name']); ?> 👋</h1>
            <p class="dashboard-date-time" id="dateTime"></p>
        </div>
        <div class="dashboard-header-right">
            <div class="shift-badge" id="shiftBadge">
                <span class="shift-dot"></span>
                <span class="shift-text">Shift 1: 07:00 - 16:00 - En cours</span>
            </div>
            <a href="/shop_v2/index.php?url=pos" class="btn btn-primary btn-sm" style="margin-left: 16px;">
                <i class="fas fa-cash-register"></i> Aller au POS (F1)
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Row -->
    <div class="kpi-cards-grid">
        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Chiffre d'Affaires</span>
                <span class="kpi-trend trend-up">
                    <i class="fas fa-arrow-up"></i> +12.5%
                </span>
            </div>
            <div class="kpi-value" id="caValue"><?php echo FormatterHelper::formatCurrency($stats['total_revenue'] ?? 0); ?></div>
            <div class="kpi-subtext">vs hier: <span id="caCompare">+0 TND</span></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Profit Net</span>
                <span class="kpi-trend trend-up">
                    <i class="fas fa-arrow-up"></i> +8.3%
                </span>
            </div>
            <div class="kpi-value" id="profitValue"><?php echo FormatterHelper::formatCurrency($profitStats['total_profit'] ?? 0); ?></div>
            <div class="kpi-subtext">Marge: <span id="profitMargin"><?php echo ($stats['total_revenue'] > 0 ? round((($profitStats['total_profit'] ?? 0) / $stats['total_revenue']) * 100, 1) : 0); ?>%</span></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Ticket Moyen</span>
                <span class="kpi-stat-badge">
                    <i class="fas fa-shopping-cart"></i> <span id="transactionCount">0</span>
                </span>
            </div>
            <div class="kpi-value" id="avgTicketValue"><?php echo (!empty($stats['total_sales']) ? FormatterHelper::formatCurrency(($stats['total_revenue'] ?? 0) / $stats['total_sales']) : '0.00 TND'); ?></div>
            <div class="kpi-subtext"><span id="transactionCountText"><?php echo (int)($stats['total_sales'] ?? 0); ?></span> transactions</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-card-header">
                <span class="kpi-label">Alertes Stock</span>
                <span class="kpi-alert-badge" id="lowStockBadge">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo count($lowStockProducts); ?>
                </span>
            </div>
            <div class="kpi-value" id="lowStockCount"><?php echo count($lowStockProducts); ?></div>
            <div class="kpi-subtext">articles critiques</div>
        </div>
    </div>

    <!-- Shift Performance Widget -->
    <div class="shift-performance-card">
        <div class="card-header">
            <h3 class="card-title">Performance par Shift</h3>
            <span class="card-date">Aujourd'hui</span>
        </div>
        <div class="shift-performance-grid">
            <div class="shift-row">
                <div class="shift-info">
                    <div class="shift-title">Shift 1</div>
                    <div class="shift-time">07:00 - 16:00</div>
                </div>
                <div class="shift-metrics">
                    <div class="shift-metric">
                        <span class="metric-value" id="shift1Revenue">0.00 TND</span>
                        <span class="metric-label">CA</span>
                    </div>
                    <div class="shift-metric">
                        <span class="metric-value" id="shift1Sales">0</span>
                        <span class="metric-label">Ventes</span>
                    </div>
                </div>
                <div class="shift-employee">
                    <i class="fas fa-user-circle"></i>
                    <span id="shift1Employee">--</span>
                </div>
            </div>

            <div class="shift-row">
                <div class="shift-info">
                    <div class="shift-title">Shift 2</div>
                    <div class="shift-time">16:00 - 22:00</div>
                </div>
                <div class="shift-metrics">
                    <div class="shift-metric">
                        <span class="metric-value" id="shift2Revenue">0.00 TND</span>
                        <span class="metric-label">CA</span>
                    </div>
                    <div class="shift-metric">
                        <span class="metric-value" id="shift2Sales">0</span>
                        <span class="metric-label">Ventes</span>
                    </div>
                </div>
                <div class="shift-employee">
                    <i class="fas fa-user-circle"></i>
                    <span id="shift2Employee">--</span>
                </div>
            </div>

            <div class="shift-row">
                <div class="shift-info">
                    <div class="shift-title">Shift 3</div>
                    <div class="shift-time">22:00 - 07:00</div>
                </div>
                <div class="shift-metrics">
                    <div class="shift-metric">
                        <span class="metric-value" id="shift3Revenue">0.00 TND</span>
                        <span class="metric-label">CA</span>
                    </div>
                    <div class="shift-metric">
                        <span class="metric-value" id="shift3Sales">0</span>
                        <span class="metric-label">Ventes</span>
                    </div>
                </div>
                <div class="shift-employee">
                    <i class="fas fa-user-circle"></i>
                    <span id="shift3Employee">--</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="card-header">
                <h3 class="card-title">Tendance Chiffre d'Affaires (7 jours)</h3>
                <div class="chart-controls">
                    <button class="chart-period-btn active" data-period="7d">7j</button>
                    <button class="chart-period-btn" data-period="30d">30j</button>
                    <button class="chart-period-btn" data-period="90d">90j</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-header">
                <h3 class="card-title">Ventes par Catégorie</h3>
            </div>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tobacco Special Insights -->
    <div class="tobacco-insights-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-smoking"></i> Insights Tabac (Mode Paquet vs Cigarettes)
            </h3>
        </div>
        <div class="tobacco-grid">
            <div class="tobacco-stat">
                <div class="tobacco-stat-label">Paquets Vendus</div>
                <div class="tobacco-stat-value" id="packsSold"><?php echo (int)($tobaccoInsights['pack']['quantity'] ?? 0); ?></div>
                <div class="tobacco-stat-revenue" id="packsRevenue"><?php echo FormatterHelper::formatCurrency($tobaccoInsights['pack']['revenue'] ?? 0); ?></div>
            </div>
            <div class="tobacco-stat">
                <div class="tobacco-stat-label">Cigarettes Vendues (Unités)</div>
                <div class="tobacco-stat-value" id="cigarettesSold"><?php echo (int)($tobaccoInsights['cigarette']['quantity'] ?? 0); ?></div>
                <div class="tobacco-stat-revenue" id="cigarettesRevenue"><?php echo FormatterHelper::formatCurrency($tobaccoInsights['cigarette']['revenue'] ?? 0); ?></div>
            </div>
            <div class="tobacco-stat">
                <div class="tobacco-stat-label">Marge Paquets</div>
                <div class="tobacco-stat-value" id="packMargin"><?php echo isset($tobaccoInsights['pack']['revenue']) && $tobaccoInsights['pack']['revenue'] > 0 ? round(($tobaccoInsights['pack']['profit'] / $tobaccoInsights['pack']['revenue']) * 100, 1) . '%' : '0%'; ?></div>
                <div class="tobacco-stat-revenue">Mode Standard</div>
            </div>
            <div class="tobacco-stat">
                <div class="tobacco-stat-label">Marge Cigarettes</div>
                <div class="tobacco-stat-value" id="cigaretteMargin"><?php echo isset($tobaccoInsights['cigarette']['revenue']) && $tobaccoInsights['cigarette']['revenue'] > 0 ? round(($tobaccoInsights['cigarette']['profit'] / $tobaccoInsights['cigarette']['revenue']) * 100, 1) . '%' : '0%'; ?></div>
                <div class="tobacco-stat-revenue">Mode Unitaire</div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts Table -->
    <div class="low-stock-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle"></i> Alertes Stock Critique
            </h3>
            <a href="/shop_v2/index.php?url=products" class="btn btn-secondary btn-xs">Voir tous</a>
        </div>
        <div class="table-responsive">
            <table class="alert-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Stock Actuel</th>
                        <th>Minimum</th>
                        <th>Niveau</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="lowStockTableBody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #64748B;">
                            Aucun article en stock critique ✓
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Sales & Top Products Split -->
    <div class="split-grid">
        <!-- Recent Transactions -->
        <div class="recent-sales-card">
            <div class="card-header">
                <h3 class="card-title">Ventes Récentes</h3>
                <a href="/shop_v2/index.php?url=pos/sales" class="btn btn-secondary btn-xs">Tous</a>
            </div>
            <div class="transactions-list">
                <div id="recentTransactionsContainer">
                    <div style="text-align: center; padding: 20px; color: #94A3B8;">
                        <i class="fas fa-inbox" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                        Aucune vente aujourd'hui
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="top-products-card">
            <div class="card-header">
                <h3 class="card-title">Top 5 Produits</h3>
                <span class="card-period">30 derniers jours</span>
            </div>
            <div class="products-list">
                <div id="topProductsContainer">
                    <div style="text-align: center; padding: 20px; color: #94A3B8;">
                        <i class="fas fa-box-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                        Pas assez de données
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<script>
    window.dashboardData = {
        stats: <?php echo json_encode($stats); ?>,
        profitStats: <?php echo json_encode($profitStats); ?>,
        dailyTrends: <?php echo json_encode($dailyTrends); ?>,
        topProducts: <?php echo json_encode($topProducts); ?>,
        categoryStats: <?php echo json_encode($categoryStats); ?>,
        lowStockProducts: <?php echo json_encode($lowStockProducts); ?>,
        recentSales: <?php echo json_encode($recentSales); ?>,
        tobaccoInsights: <?php echo json_encode($tobaccoInsights); ?>
    };
</script>

<link rel="stylesheet" href="/shop_v2/public/css/dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="/shop_v2/public/js/dashboard.js"></script>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
