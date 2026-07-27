<?php
/**
 * Dashboard - Stock & Sales Management System
 * Modern SaaS-inspired interface with real-time metrics and analytics
 * 
 * PHP MVC Template using Chart.js, FontAwesome, and Custom CSS
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock & Sales Management</title>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        
        <!-- ========================
             HEADER & TOP BAR
             ======================== -->
        <header class="dashboard-header">
            <div class="header-content">
                <!-- Left: Welcome & Date/Time -->
                <div class="header-left">
                    <div class="welcome-section">
                        <h1 class="welcome-title">Bonjour, Boss Admin</h1>
                        <p class="current-datetime" id="currentDateTime">
                            <i class="fas fa-calendar"></i> Mardi, 21 Juillet 2025
                        </p>
                    </div>
                </div>

                <!-- Center: Shift Status -->
                <div class="header-center">
                    <div class="shift-status-badge">
                        <span class="shift-indicator pulse"></span>
                        <span class="shift-text">Shift 1: 07:00 - 16:00 - <strong>En cours</strong></span>
                        <span class="shift-timer" id="shiftTimer">8h 21m restant</span>
                    </div>
                </div>

                <!-- Right: POS & Actions -->
                <div class="header-right">
                    <button class="btn-primary btn-pos" title="Accès rapide au POS (F1)">
                        <i class="fas fa-cash-register"></i>
                        <span>Go to POS</span>
                    </button>
                    <div class="user-menu">
                        <img src="<?php echo BASE_URL; ?>assets/images/avatar.png" alt="Admin" class="avatar">
                    </div>
                </div>
            </div>
        </header>

        <!-- ========================
             MAIN CONTENT AREA
             ======================== -->
        <main class="dashboard-main">
            
            <!-- KPI Metrics Cards Row -->
            <section class="kpi-metrics-section">
                <div class="metrics-grid">
                    
                    <!-- KPI Card 1: Chiffre d'Affaires -->
                    <div class="kpi-card kpi-revenue">
                        <div class="kpi-header">
                            <h3 class="kpi-title">Chiffre d'Affaires</h3>
                            <div class="kpi-icon-bg">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="kpi-value">
                            <span id="caValue" class="value-amount"><?php echo FormatterHelper::formatCurrency($stats['total_revenue'] ?? 0); ?></span>
                        </div>
                        <div class="kpi-footer">
                            <span class="trend trend-up">
                                <i class="fas fa-arrow-up"></i> +12.5%
                            </span>
                            <span class="trend-label">vs. Hier</span>
                        </div>
                    </div>

                    <!-- KPI Card 2: Profit Net -->
                    <div class="kpi-card kpi-profit">
                        <div class="kpi-header">
                            <h3 class="kpi-title">Profit Net</h3>
                            <div class="kpi-icon-bg success">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <div class="kpi-value">
                            <span id="profitValue" class="value-amount"><?php echo FormatterHelper::formatCurrency($profitStats['total_profit'] ?? 0); ?></span>
                        </div>
                        <div class="kpi-footer">
                            <span class="trend trend-up">
                                <i class="fas fa-arrow-up"></i> +8.3%
                            </span>
                            <span class="trend-label">vs. Hier</span>
                        </div>
                    </div>

                    <!-- KPI Card 3: Ticket Moyen -->
                    <div class="kpi-card kpi-average">
                        <div class="kpi-header">
                            <h3 class="kpi-title">Ticket Moyen</h3>
                            <div class="kpi-icon-bg info">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="kpi-value">
                            <span id="avgTicketValue" class="value-amount"><?php echo !empty($stats['total_sales']) ? FormatterHelper::formatCurrency(($stats['total_revenue'] ?? 0) / $stats['total_sales']) : '0.00 TND'; ?></span>
                        </div>
                        <div class="kpi-footer">
                            <span id="transactionCountText" class="metric-secondary"><?php echo (int)($stats['total_sales'] ?? 0); ?> Transactions</span>
                        </div>
                    </div>

                    <!-- KPI Card 4: Stock Alerts -->
                    <div class="kpi-card kpi-alerts">
                        <div class="kpi-header">
                            <h3 class="kpi-title">Alertes Stock</h3>
                            <div class="kpi-icon-bg warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="kpi-value">
                            <span id="lowStockCount" class="value-amount alert-count"><?php echo count($lowStockProducts); ?></span>
                            <span class="value-label">Produits</span>
                        </div>
                        <div class="kpi-footer">
                            <button class="btn-action-small">
                                <i class="fas fa-box"></i> Gérer Stock
                            </button>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Main Content Grid: Charts + Widgets -->
            <div class="dashboard-grid">
                
                <!-- Left Column: Charts & Tables -->
                <div class="column column-left">
                    
                    <!-- Chart: 7-Day Revenue & Profit Trend -->
                    <section class="chart-card">
                        <div class="card-header">
                            <h2 class="card-title">Tendance Revenue & Profit</h2>
                            <div class="card-controls">
                                <select class="period-selector" id="periodSelector">
                                    <option value="7days">7 Jours</option>
                                    <option value="30days">30 Jours</option>
                                    <option value="90days">90 Jours</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart" height="80"></canvas>
                        </div>
                    </section>

                    <!-- Chart: Sales by Category -->
                    <section class="chart-card">
                        <div class="card-header">
                            <h2 class="card-title">Ventes par Catégorie</h2>
                        </div>
                        <div class="chart-container-small">
                            <canvas id="categoryChart" height="60"></canvas>
                        </div>
                    </section>

                    <!-- Low Stock Alert Table -->
                    <section class="alert-table-card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-box-open"></i> Alertes Stock - Réapprovisionner
                            </h2>
                            <span id="lowStockBadge" class="badge badge-warning"><?php echo count($lowStockProducts); ?> Produits</span>
                        </div>
                        <div class="table-responsive">
                            <table class="alert-table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Catégorie</th>
                                        <th>Stock Actuel</th>
                                        <th>Minimum</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="lowStockTableBody">
                                    <?php if (!empty($lowStockProducts)): ?>
                                        <?php foreach ($lowStockProducts as $product): ?>
                                            <tr class="<?php echo ($product['quantity'] <= 0 ? 'alert-critical' : 'alert-warning'); ?>">
                                                <td class="product-name">
                                                    <i class="fas fa-box-open"></i> <?php echo SecurityHelper::escapeHtml($product['name']); ?>
                                                </td>
                                                <td><span class="badge badge-category"><?php echo SecurityHelper::escapeHtml($product['category_name'] ?? 'N/A'); ?></span></td>
                                                <td><?php echo FormatterHelper::formatQuantity($product['quantity']); ?></td>
                                                <td><?php echo FormatterHelper::formatQuantity($product['minimum_stock']); ?></td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>index.php?url=products/edit/<?php echo $product['id']; ?>" class="btn-action-mini btn-restock">
                                                        <i class="fas fa-plus"></i> Réapprovisionner
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun article en stock critique ✓</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <a href="#stock-management" class="link-action">
                                Voir tous les alertes <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </section>

                </div>

                <!-- Right Column: Shift & Insights -->
                <div class="column column-right">
                    
                    <!-- Shift Performance Tracker -->
                    <section class="shift-tracker-card">
                        <div class="card-header">
                            <h2 class="card-title">Performance par Shift</h2>
                        </div>
                        <div class="shift-comparison">
                            
                            <!-- Shift 1 -->
                            <div class="shift-item shift-item-active">
                                <div class="shift-header">
                                    <h3 class="shift-name">Shift 1</h3>
                                    <span class="shift-time">07:00 - 16:00</span>
                                </div>
                                <div class="shift-metrics">
                                    <div class="metric-row">
                                        <span class="metric-label">Revenue</span>
                                        <span id="shift1Revenue" class="metric-value">650.000 TND</span>
                                    </div>
                                    <div class="metric-row">
                                        <span class="metric-label">Transactions</span>
                                        <span id="shift1Sales" class="metric-value">28</span>
                                    </div>
                                    <div class="metric-row">
                                        <span class="metric-label">Employee</span>
                                        <span id="shift1Employee" class="metric-value-emp">Abdelaziz Ben</span>
                                    </div>
                                </div>
                                <div class="shift-badge-active">
                                    <i class="fas fa-circle-play"></i> En cours
                                </div>
                            </div>

                            <!-- Shift 2 -->
                            <div class="shift-item">
                                <div class="shift-header">
                                    <h3 class="shift-name">Shift 2</h3>
                                    <span class="shift-time">16:00 - 22:00</span>
                                </div>
                                <div class="shift-metrics">
                                    <div class="metric-row">
                                        <span class="metric-label">Revenue</span>
                                        <span id="shift2Revenue" class="metric-value">420.000 TND</span>
                                    </div>
                                    <div class="metric-row">
                                        <span class="metric-label">Transactions</span>
                                        <span id="shift2Sales" class="metric-value">18</span>
                                    </div>
                                    <div class="metric-row">
                                        <span class="metric-label">Employee</span>
                                        <span id="shift2Employee" class="metric-value-emp">Fatima Ali</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Shift 3 -->
                            <div class="shift-item">
                                <div class="shift-header">
                                    <h3 class="shift-name">Shift 3</h3>
                                    <span class="shift-time">22:00 - 07:00</span>
                                </div>
                                <div class="shift-metrics">
                                    <div class="metric-row">
                                        <span class="metric-label">Revenue</span>
                                        <span id="shift3Revenue" class="metric-value">180.000 TND</span>
                                    </div>
                                    <div class="metric-row">
                                        <span class="metric-label">Transactions</span>
                                        <span id="shift3Sales" class="metric-value">3</span>
                                    </div>
                                    <div class="metric-row">
                                        <span class="metric-label">Employee</span>
                                        <span id="shift3Employee" class="metric-value-emp">Mohamed Sami</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    <!-- Tobacco Insights Card -->
                    <section class="tobacco-insights-card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-leaf"></i> Mode Tabac - Insights
                            </h2>
                        </div>
                        <div class="tobacco-metrics">
                            
                            <div class="tobacco-metric-item">
                                <div class="metric-icon full-pack">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="metric-content">
                                    <h4 class="metric-name">Full Packs</h4>
                                    <p class="metric-data">
                                        <span id="packsSold" class="number"><?php echo (int)($tobaccoInsights['pack']['quantity'] ?? 0); ?></span> packs vendus
                                    </p>
                                    <p id="packsRevenue" class="metric-revenue">Revenue: <?php echo FormatterHelper::formatCurrency($tobaccoInsights['pack']['revenue'] ?? 0); ?></p>
                                </div>
                            </div>

                            <div class="divider-vertical"></div>

                            <div class="tobacco-metric-item">
                                <div class="metric-icon single-unit">
                                    <i class="fas fa-cigarette"></i>
                                </div>
                                <div class="metric-content">
                                    <h4 class="metric-name">Single Units</h4>
                                    <p class="metric-data">
                                        <span id="cigarettesSold" class="number"><?php echo (int)($tobaccoInsights['cigarette']['quantity'] ?? 0); ?></span> cigarettes
                                    </p>
                                    <p id="cigarettesRevenue" class="metric-revenue">Revenue: <?php echo FormatterHelper::formatCurrency($tobaccoInsights['cigarette']['revenue'] ?? 0); ?></p>
                                </div>
                            </div>

                        </div>
                        <div class="tobacco-comparison">
                            <div class="comparison-item">
                                <span class="label">Margin Packs</span>
                                <span id="packMargin" class="percentage positive">
                                    <?php echo isset($tobaccoInsights['pack']['revenue']) && $tobaccoInsights['pack']['revenue'] > 0 ? round(($tobaccoInsights['pack']['profit'] / $tobaccoInsights['pack']['revenue']) * 100, 1) . '%' : '0%'; ?>
                                </span>
                            </div>
                            <div class="comparison-item">
                                <span class="label">Margin Unit</span>
                                <span id="cigaretteMargin" class="percentage positive">
                                    <?php echo isset($tobaccoInsights['cigarette']['revenue']) && $tobaccoInsights['cigarette']['revenue'] > 0 ? round(($tobaccoInsights['cigarette']['profit'] / $tobaccoInsights['cigarette']['revenue']) * 100, 1) . '%' : '0%'; ?>
                                </span>
                            </div>
                        </div>
                    </section>

                    <!-- Recent Transactions -->
                    <section class="recent-transactions-card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-clock"></i> 10 Dernières Transactions
                            </h2>
                        </div>
                        <div id="recentTransactionsContainer" class="transaction-list">
                             
                            <div class="transaction-item">
                                <div class="tx-info">
                                    <p class="tx-invoice">
                                        <strong>INV-2025-0847</strong>
                                    </p>
                                    <p class="tx-time">11:45 AM</p>
                                </div>
                                <div class="tx-amount">
                                    <span class="amount">52.500 TND</span>
                                </div>
                                <div class="tx-payment">
                                    <span class="payment-badge cash">Cash</span>
                                </div>
                                <div class="tx-action">
                                    <button class="btn-icon-small" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="tx-info">
                                    <p class="tx-invoice">
                                        <strong>INV-2025-0846</strong>
                                    </p>
                                    <p class="tx-time">11:32 AM</p>
                                </div>
                                <div class="tx-amount">
                                    <span class="amount">28.300 TND</span>
                                </div>
                                <div class="tx-payment">
                                    <span class="payment-badge card">Card</span>
                                </div>
                                <div class="tx-action">
                                    <button class="btn-icon-small" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="tx-info">
                                    <p class="tx-invoice">
                                        <strong>INV-2025-0845</strong>
                                    </p>
                                    <p class="tx-time">11:18 AM</p>
                                </div>
                                <div class="tx-amount">
                                    <span class="amount">15.750 TND</span>
                                </div>
                                <div class="tx-payment">
                                    <span class="payment-badge cash">Cash</span>
                                </div>
                                <div class="tx-action">
                                    <button class="btn-icon-small" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="tx-info">
                                    <p class="tx-invoice">
                                        <strong>INV-2025-0844</strong>
                                    </p>
                                    <p class="tx-time">11:05 AM</p>
                                </div>
                                <div class="tx-amount">
                                    <span class="amount">67.200 TND</span>
                                </div>
                                <div class="tx-payment">
                                    <span class="payment-badge card">Card</span>
                                </div>
                                <div class="tx-action">
                                    <button class="btn-icon-small" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="transaction-item">
                                <div class="tx-info">
                                    <p class="tx-invoice">
                                        <strong>INV-2025-0843</strong>
                                    </p>
                                    <p class="tx-time">10:52 AM</p>
                                </div>
                                <div class="tx-amount">
                                    <span class="amount">34.100 TND</span>
                                </div>
                                <div class="tx-payment">
                                    <span class="payment-badge cash">Cash</span>
                                </div>
                                <div class="tx-action">
                                    <button class="btn-icon-small" title="Imprimer">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <a href="#transactions" class="link-action">
                                Voir toutes les transactions <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </section>

                </div>

            </div>

            <!-- Top Selling Products Section -->
            <section class="top-products-section">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-fire"></i> Top 5 Best-Selling Products
                    </h2>
                </div>
                <div id="topProductsContainer" class="products-grid">
                     
                    <!-- Product 1 -->
                    <div class="product-card">
                        <div class="product-rank">
                            <span class="rank-badge rank-1">1</span>
                        </div>
                        <div class="product-image">
                            <div class="image-placeholder">
                                <i class="fas fa-cigarette"></i>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Marlboro Red Pack</h3>
                            <p class="product-category">Tabac</p>
                        </div>
                        <div class="product-stats">
                            <div class="stat">
                                <span class="stat-label">Vendus</span>
                                <span class="stat-value">156</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">3,900 TND</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="product-card">
                        <div class="product-rank">
                            <span class="rank-badge rank-2">2</span>
                        </div>
                        <div class="product-image">
                            <div class="image-placeholder">
                                <i class="fas fa-water"></i>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Coca Cola 1.5L</h3>
                            <p class="product-category">Boissons</p>
                        </div>
                        <div class="product-stats">
                            <div class="stat">
                                <span class="stat-label">Vendus</span>
                                <span class="stat-value">89</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">1,780 TND</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="product-card">
                        <div class="product-rank">
                            <span class="rank-badge rank-3">3</span>
                        </div>
                        <div class="product-image">
                            <div class="image-placeholder">
                                <i class="fas fa-chips"></i>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Chips Lay's</h3>
                            <p class="product-category">Snacks</p>
                        </div>
                        <div class="product-stats">
                            <div class="stat">
                                <span class="stat-label">Vendus</span>
                                <span class="stat-value">74</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">940 TND</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="product-card">
                        <div class="product-rank">
                            <span class="rank-badge rank-4">4</span>
                        </div>
                        <div class="product-image">
                            <div class="image-placeholder">
                                <i class="fas fa-candy"></i>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Energy Drink</h3>
                            <p class="product-category">Boissons</p>
                        </div>
                        <div class="product-stats">
                            <div class="stat">
                                <span class="stat-label">Vendus</span>
                                <span class="stat-value">52</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">624 TND</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product 5 -->
                    <div class="product-card">
                        <div class="product-rank">
                            <span class="rank-badge rank-5">5</span>
                        </div>
                        <div class="product-image">
                            <div class="image-placeholder">
                                <i class="fas fa-bread-slice"></i>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Sandwich Panini</h3>
                            <p class="product-category">Food</p>
                        </div>
                        <div class="product-stats">
                            <div class="stat">
                                <span class="stat-label">Vendus</span>
                                <span class="stat-value">38</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Revenue</span>
                                <span class="stat-value">456 TND</span>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Barcode Generator Widget -->
            <section class="barcode-generator-section">
                <div class="barcode-card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">
                                <i class="fas fa-barcode"></i> Générateur de Code-Barres
                            </h3>
                            <p class="card-subtitle">Générez des codes-barres automatiques pour vos produits</p>
                        </div>
                    </div>

                    <div class="barcode-content">
                        <div class="barcode-input-group">
                            <label for="barcodeInput" class="label">Code/ID du Produit</label>
                            <div class="input-wrapper">
                                <input 
                                    type="text" 
                                    id="barcodeInput" 
                                    class="barcode-input" 
                                    placeholder="Entrez ou générez un code..."
                                    maxlength="12"
                                >
                                <button class="btn-generate-barcode" id="generateBarcodeBtn">
                                    <i class="fas fa-sync-alt"></i> Générer
                                </button>
                            </div>
                            <small class="input-helper">Format: 12 chiffres (EAN-12)</small>
                        </div>

                        <div class="barcode-display-area">
                            <div class="barcode-canvas-wrapper">
                                <svg id="barcodeCanvas"></svg>
                            </div>
                            <div class="barcode-info">
                                <p>Code généré: <strong id="barcodeValue">---</strong></p>
                            </div>
                        </div>

                        <div class="barcode-actions">
                            <button class="btn-action btn-download" id="downloadBarcodeBtn">
                                <i class="fas fa-download"></i> Télécharger
                            </button>
                            <button class="btn-action btn-print" id="printBarcodeBtn">
                                <i class="fas fa-print"></i> Imprimer
                            </button>
                            <button class="btn-action btn-copy" id="copyBarcodeBtn">
                                <i class="fas fa-copy"></i> Copier le Code
                            </button>
                        </div>
                    </div>
                </div>
            </section>

        </main>

    </div>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    
    <!-- Chart.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <!-- Dashboard JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/dashboard.js?v=2"></script>

</body>
</html>
