<?php
/**
 * Dashboard View — "Digital Ledger" redesign
 *
 * Uses the same variables produced by DashboardController::index():
 *   $stats, $shift1_revenue..$shift3_average, $recent_sales, $top_products,
 *   $revenue_by_month, $sales_by_month, $settings/$currency, $low_stock_products
 *
 * Optional keys (safe fallbacks if not yet returned by the controller):
 *   $stats['total_customers']       -> "Clients" KPI card
 *   $stats['stock_value']           -> shown in the stock card subtitle
 *   $stats['today_revenue_change']  -> trend % on the revenue KPI
 *   $stats['today_sales_change']    -> trend % on the sales KPI
 *   $stats['today_profit_change']   -> trend % on the profit KPI
 *   $product['image_url']           -> product thumbnail in tables
 *   $product['category_name']       -> shown under product name in "top sellers"
 *   $stock_movements                -> array of recent stock movements (see empty state below)
 *
 * None of these are required — every one degrades gracefully when absent.
 */

$stats              = is_array($stats ?? null) ? $stats : [];
$shift_stats         = is_array($shift_stats ?? null) ? $shift_stats : [];
$revenue_by_month    = is_array($revenue_by_month ?? null) ? $revenue_by_month : [];
$sales_by_month      = is_array($sales_by_month ?? null) ? $sales_by_month : [];
$recent_sales        = is_array($recent_sales ?? null) ? $recent_sales : [];
$top_products        = is_array($top_products ?? null) ? $top_products : [];
$low_stock_products  = is_array($low_stock_products ?? null) ? $low_stock_products : [];
$stock_movements     = is_array($stock_movements ?? null) ? $stock_movements : [];

$settings = $settings ?? [];
$currency = $settings['currency'] ?? 'TND ';
if ($currency === '$') { $currency = 'TND '; }

/** Small helper: render a trend chip, or a neutral dash if the metric isn't wired up yet. */
function dv2_trend($value) {
    if ($value === null || $value === '') {
        return '<span class="dv2-trend flat"><i class="fas fa-minus"></i> —</span>';
    }
    $value = (float) $value;
    if ($value > 0) {
        return '<span class="dv2-trend up"><i class="fas fa-arrow-up"></i> +' . number_format($value, 1) . '%</span>';
    } elseif ($value < 0) {
        return '<span class="dv2-trend down"><i class="fas fa-arrow-down"></i> ' . number_format($value, 1) . '%</span>';
    }
    return '<span class="dv2-trend flat"><i class="fas fa-minus"></i> 0%</span>';
}

$total_customers = $stats['total_customers'] ?? null;
$stock_value      = $stats['stock_value'] ?? null;
$out_of_stock     = (int) ($stats['out_of_stock'] ?? 0);
$low_stock_count  = (int) ($stats['low_stock'] ?? 0);
?>
<link rel="stylesheet" href="assets/css/dashboard-ledger.css">

<div class="dashboard-v2">

    <!-- ============ Page header ============ -->
    <div class="dv2-header">
        <div class="dv2-header-title">
            <span class="eyebrow">Aperçu général</span>
            <h1>Tableau de bord</h1>
            <p>Suivi de l'activité, du stock et des performances de vente.</p>
        </div>
        <div class="dv2-header-tools">
            <div class="dv2-search">
                <i class="fas fa-search"></i>
                <input type="text" id="dashboardSearch" placeholder="Rechercher un produit, une vente, un client…">
            </div>
            <button type="button" class="dv2-icon-btn" title="Notifications">
                <i class="fas fa-bell"></i>
                <?php if ($low_stock_count > 0 || $out_of_stock > 0): ?><span class="dot"></span><?php endif; ?>
            </button>
            <a href="index.php?page=pos" class="dv2-cta">
                <i class="fas fa-plus"></i> Nouvelle vente
            </a>
        </div>
    </div>

    <!-- ============ KPI ledger tiles ============ -->
    <div class="dv2-kpi-grid">

        <div class="dv2-kpi">
            <div class="dv2-kpi-top">
                <div class="dv2-kpi-icon tone-primary"><i class="fas fa-sack-dollar"></i></div>
                <?php echo dv2_trend($stats['today_revenue_change'] ?? null); ?>
            </div>
            <div class="dv2-kpi-rule"></div>
            <div class="dv2-kpi-value num"><?php echo format_currency($stats['today_revenue'] ?? 0, $currency); ?></div>
            <div class="dv2-kpi-label">Chiffre d'affaires — aujourd'hui</div>
        </div>

        <div class="dv2-kpi">
            <div class="dv2-kpi-top">
                <div class="dv2-kpi-icon tone-info"><i class="fas fa-cart-shopping"></i></div>
                <?php echo dv2_trend($stats['today_sales_change'] ?? null); ?>
            </div>
            <div class="dv2-kpi-rule"></div>
            <div class="dv2-kpi-value num"><?php echo (int) ($stats['today_sales'] ?? 0); ?></div>
            <div class="dv2-kpi-label">Ventes — aujourd'hui</div>
        </div>

        <div class="dv2-kpi">
            <div class="dv2-kpi-top">
                <div class="dv2-kpi-icon tone-success"><i class="fas fa-chart-line"></i></div>
                <?php echo dv2_trend($stats['today_profit_change'] ?? null); ?>
            </div>
            <div class="dv2-kpi-rule"></div>
            <div class="dv2-kpi-value num"><?php echo format_currency($stats['today_profit'] ?? 0, $currency); ?></div>
            <div class="dv2-kpi-label">Bénéfice net — aujourd'hui</div>
        </div>

        <div class="dv2-kpi">
            <div class="dv2-kpi-top">
                <div class="dv2-kpi-icon tone-gold"><i class="fas fa-box"></i></div>
                <span class="dv2-trend flat"><i class="fas fa-boxes-stacked"></i> actifs</span>
            </div>
            <div class="dv2-kpi-rule"></div>
            <div class="dv2-kpi-value num"><?php echo (int) ($stats['total_products'] ?? 0); ?></div>
            <div class="dv2-kpi-label">Produits au catalogue</div>
        </div>

        <div class="dv2-kpi">
            <div class="dv2-kpi-top">
                <div class="dv2-kpi-icon tone-danger"><i class="fas fa-triangle-exclamation"></i></div>
                <?php if ($out_of_stock > 0): ?>
                    <span class="dv2-trend down"><i class="fas fa-ban"></i> <?php echo $out_of_stock; ?> épuisés</span>
                <?php else: ?>
                    <span class="dv2-trend flat"><i class="fas fa-check"></i> ok</span>
                <?php endif; ?>
            </div>
            <div class="dv2-kpi-rule"></div>
            <div class="dv2-kpi-value num"><?php echo $low_stock_count; ?></div>
            <div class="dv2-kpi-label">Produits en stock faible</div>
        </div>

        <div class="dv2-kpi">
            <div class="dv2-kpi-top">
                <div class="dv2-kpi-icon tone-info"><i class="fas fa-users"></i></div>
                <span class="dv2-trend flat"><i class="fas fa-user-plus"></i> total</span>
            </div>
            <div class="dv2-kpi-rule"></div>
            <div class="dv2-kpi-value num">
                <?php echo $total_customers !== null ? (int) $total_customers : '—'; ?>
            </div>
            <div class="dv2-kpi-label">
                Clients enregistrés
                <?php if ($total_customers === null): ?><br><span style="color:var(--ink-faint);font-weight:400;">non connecté</span><?php endif; ?>
            </div>
        </div>

    </div>

    <!-- ============ Main layout: analytics + stock (left) / actions + activity (right) ============ -->
    <div class="dv2-layout">

        <!-- LEFT COLUMN -->
        <div class="dv2-col-main">

            <!-- Sales analytics -->
            <div class="dv2-card">
                <div class="dv2-card-head">
                    <div>
                        <h3>Analyse des ventes</h3>
                        <p>Évolution du chiffre d'affaires et du volume de ventes</p>
                    </div>
                    <div class="dv2-segment" id="chartPeriodToggle">
                        <button type="button" class="active" data-metric="revenue">Chiffre d'affaires</button>
                        <button type="button" data-metric="sales">Ventes</button>
                    </div>
                </div>
                <div class="dv2-chart-wrap">
                    <canvas id="ledgerRevenueChart"></canvas>
                </div>
                <div class="dv2-chart-legend">
                    <span class="dv2-legend-item"><span class="dv2-legend-dot" style="background:#1F5C4E"></span>Période en cours</span>
                    <span class="dv2-legend-item"><span class="dv2-legend-dot" style="background:#CBD4C7"></span>Période précédente</span>
                </div>
            </div>

            <!-- Shift performance -->
            <div class="dv2-card">
                <div class="dv2-card-head">
                    <div>
                        <h3>Performance par équipe</h3>
                        <p>Répartition du chiffre d'affaires par créneau horaire, aujourd'hui</p>
                    </div>
                </div>
                <div class="dv2-shift-grid">
                    <div class="dv2-shift s1">
                        <div class="dv2-shift-title"><i class="fas fa-sun"></i> Shift 1</div>
                        <div class="dv2-shift-hours">07h – 16h</div>
                        <div class="dv2-shift-row"><span>CA</span><b><?php echo format_currency($shift1_revenue ?? 0, $currency); ?></b></div>
                        <div class="dv2-shift-row"><span>Ventes</span><b><?php echo (int) ($shift1_sales ?? 0); ?></b></div>
                        <div class="dv2-shift-row"><span>Ticket moyen</span><b><?php echo format_currency($shift1_average ?? 0, $currency); ?></b></div>
                    </div>
                    <div class="dv2-shift s2">
                        <div class="dv2-shift-title"><i class="fas fa-cloud-sun"></i> Shift 2</div>
                        <div class="dv2-shift-hours">16h – 22h</div>
                        <div class="dv2-shift-row"><span>CA</span><b><?php echo format_currency($shift2_revenue ?? 0, $currency); ?></b></div>
                        <div class="dv2-shift-row"><span>Ventes</span><b><?php echo (int) ($shift2_sales ?? 0); ?></b></div>
                        <div class="dv2-shift-row"><span>Ticket moyen</span><b><?php echo format_currency($shift2_average ?? 0, $currency); ?></b></div>
                    </div>
                    <div class="dv2-shift s3">
                        <div class="dv2-shift-title"><i class="fas fa-moon"></i> Shift 3</div>
                        <div class="dv2-shift-hours">22h – 07h</div>
                        <div class="dv2-shift-row"><span>CA</span><b><?php echo format_currency($shift3_revenue ?? 0, $currency); ?></b></div>
                        <div class="dv2-shift-row"><span>Ventes</span><b><?php echo (int) ($shift3_sales ?? 0); ?></b></div>
                        <div class="dv2-shift-row"><span>Ticket moyen</span><b><?php echo format_currency($shift3_average ?? 0, $currency); ?></b></div>
                    </div>
                </div>
            </div>

            <!-- Stock overview -->
            <div class="dv2-card">
                <div class="dv2-card-head">
                    <div>
                        <h3>Gestion du stock</h3>
                        <p>
                            <?php if ($stock_value !== null): ?>
                                Valeur du stock : <strong style="color:var(--ink)"><?php echo format_currency($stock_value, $currency); ?></strong>
                            <?php else: ?>
                                Produits en stock faible, meilleures ventes et derniers mouvements
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="index.php?page=products" class="dv2-link-all">Gérer le stock <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="dv2-tabs" id="stockTabs">
                    <button type="button" class="dv2-tab active" data-tab="low">Stock faible <span class="count"><?php echo count($low_stock_products); ?></span></button>
                    <button type="button" class="dv2-tab" data-tab="top">Meilleures ventes <span class="count"><?php echo count($top_products); ?></span></button>
                    <button type="button" class="dv2-tab" data-tab="moves">Mouvements récents <span class="count"><?php echo count($stock_movements); ?></span></button>
                </div>

                <!-- Low stock -->
                <div class="dv2-tabpanel active" id="tab-low">
                    <?php if (empty($low_stock_products)): ?>
                        <div class="dv2-empty">
                            <i class="fas fa-circle-check"></i>
                            <p>Aucun produit en stock faible</p>
                            <span>Tous vos produits sont au-dessus du seuil minimum.</span>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                        <table class="dv2-table">
                            <thead>
                                <tr><th>Produit</th><th>Catégorie</th><th>Quantité</th><th>Seuil min.</th><th>Statut</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="dv2-product-cell">
                                            <div class="dv2-product-thumb">
                                                <?php if (!empty($product['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="">
                                                <?php else: ?>
                                                    <?php echo strtoupper(substr($product['name'] ?? '?', 0, 1)); ?>
                                                <?php endif; ?>
                                            </div>
                                            <span class="dv2-product-name"><?php echo htmlspecialchars($product['name'] ?? ''); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? '—'); ?></td>
                                    <td><?php echo (int) ($product['quantity'] ?? 0); ?></td>
                                    <td><?php echo (int) ($product['minimum_stock'] ?? 0); ?></td>
                                    <td>
                                        <?php if ((int) ($product['quantity'] ?? 0) === 0): ?>
                                            <span class="dv2-badge out"><i class="fas fa-ban"></i> Rupture</span>
                                        <?php else: ?>
                                            <span class="dv2-badge low"><i class="fas fa-triangle-exclamation"></i> Faible</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Top sellers -->
                <div class="dv2-tabpanel" id="tab-top">
                    <?php if (empty($top_products)): ?>
                        <div class="dv2-empty">
                            <i class="fas fa-chart-simple"></i>
                            <p>Pas encore de données de vente</p>
                            <span>Les meilleurs produits apparaîtront ici dès la première vente.</span>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                        <table class="dv2-table">
                            <thead>
                                <tr><th>Produit</th><th>Catégorie</th><th>Quantité vendue</th><th>Statut</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="dv2-product-cell">
                                            <div class="dv2-product-thumb">
                                                <?php if (!empty($product['image_url'])): ?>
                                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="">
                                                <?php else: ?>
                                                    <?php echo strtoupper(substr($product['name'] ?? '?', 0, 1)); ?>
                                                <?php endif; ?>
                                            </div>
                                            <span class="dv2-product-name"><?php echo htmlspecialchars($product['name'] ?? ''); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? '—'); ?></td>
                                    <td><?php echo (int) ($product['total_sold'] ?? 0); ?></td>
                                    <td><span class="dv2-badge ok"><i class="fas fa-fire"></i> Populaire</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Stock movements -->
                <div class="dv2-tabpanel" id="tab-moves">
                    <?php if (empty($stock_movements)): ?>
                        <div class="dv2-empty">
                            <i class="fas fa-clock-rotate-left"></i>
                            <p>Aucun mouvement de stock à afficher</p>
                            <span>Connectez le modèle des mouvements de stock pour les voir apparaître ici.</span>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                        <table class="dv2-table">
                            <thead>
                                <tr><th>Produit</th><th>Type</th><th>Quantité</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stock_movements as $move): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($move['product_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($move['type'] ?? ''); ?></td>
                                    <td><?php echo (int) ($move['quantity'] ?? 0); ?></td>
                                    <td><?php echo format_date($move['created_at'] ?? '', 'M d, H:i'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="dv2-col-side">

            <!-- Quick actions -->
            <div class="dv2-card">
                <div class="dv2-card-head"><div><h3>Actions rapides</h3></div></div>
                <div class="dv2-actions-grid">
                    <a href="index.php?page=products&action=add" class="dv2-action">
                        <span class="dv2-action-icon"><i class="fas fa-box-open"></i></span>
                        <span class="dv2-action-text"><strong>Ajouter un produit</strong><span>Nouveau produit au catalogue</span></span>
                    </a>
                    <a href="index.php?page=pos" class="dv2-action">
                        <span class="dv2-action-icon"><i class="fas fa-cash-register"></i></span>
                        <span class="dv2-action-text"><strong>Nouvelle vente</strong><span>Ouvrir le point de vente</span></span>
                    </a>
                    <a href="index.php?page=purchases&action=add" class="dv2-action">
                        <span class="dv2-action-icon"><i class="fas fa-truck-ramp-box"></i></span>
                        <span class="dv2-action-text"><strong>Nouvel achat</strong><span>Enregistrer une réception</span></span>
                    </a>
                    <a href="index.php?page=customers&action=add" class="dv2-action">
                        <span class="dv2-action-icon"><i class="fas fa-user-plus"></i></span>
                        <span class="dv2-action-text"><strong>Ajouter un client</strong><span>Nouvelle fiche client</span></span>
                    </a>
                    <a href="index.php?page=reports" class="dv2-action">
                        <span class="dv2-action-icon"><i class="fas fa-file-lines"></i></span>
                        <span class="dv2-action-text"><strong>Générer un rapport</strong><span>Export PDF / Excel</span></span>
                    </a>
                </div>
            </div>

            <!-- Recent activity -->
            <div class="dv2-card">
                <div class="dv2-card-head">
                    <div><h3>Activité récente</h3></div>
                    <a href="index.php?page=sales" class="dv2-link-all">Tout voir</a>
                </div>

                <?php if (empty($recent_sales)): ?>
                    <div class="dv2-empty">
                        <i class="fas fa-inbox"></i>
                        <p>Aucune activité récente</p>
                        <span>Les nouvelles ventes apparaîtront ici en temps réel.</span>
                    </div>
                <?php else: ?>
                    <ul class="dv2-timeline">
                        <?php foreach (array_slice($recent_sales, 0, 6) as $sale): ?>
                        <li>
                            <div class="dv2-timeline-dot"><i class="fas fa-receipt"></i></div>
                            <div class="dv2-timeline-body">
                                <p>Vente <?php echo htmlspecialchars($sale['invoice_number'] ?? ''); ?> — <?php echo format_currency($sale['total'] ?? 0, $currency); ?></p>
                                <span><?php echo htmlspecialchars($sale['cashier_name'] ?? ''); ?> · <?php echo format_date($sale['created_at'] ?? '', 'M d, H:i'); ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                        <?php if ($low_stock_count > 0): ?>
                        <li>
                            <div class="dv2-timeline-dot" style="background:var(--warning-soft); color:var(--warning);"><i class="fas fa-triangle-exclamation"></i></div>
                            <div class="dv2-timeline-body">
                                <p><?php echo $low_stock_count; ?> produit(s) en stock faible</p>
                                <span>Vérification recommandée</span>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- Chart + Tab data -->
<script>
(function(){
    const currency = <?php echo json_encode($currency); ?>;
    const revenueData = <?php echo json_encode($revenue_by_month); ?>;
    const salesData = <?php echo json_encode($sales_by_month); ?>;

    function toSeries(raw){
        if (!raw) return { labels: [], values: [] };
        if (Array.isArray(raw)) {
            const labels = raw.map((r, i) => r.label || r.month || r.period || ('#' + (i + 1)));
            const values = raw.map(r => Number(r.value ?? r.total ?? r.revenue ?? r.count ?? r.amount ?? 0));
            return { labels, values };
        }
        const labels = Object.keys(raw);
        const values = labels.map(k => Number(raw[k]));
        return { labels, values };
    }

    const revSeries = toSeries(revenueData);
    const salesSeries = toSeries(salesData);

    let chart;
    const ctx = document.getElementById('ledgerRevenueChart');

    function renderChart(metric){
        if (!ctx || typeof Chart === 'undefined') return;
        const series = metric === 'sales' ? salesSeries : revSeries;
        const color = metric === 'sales' ? '#2B6C8F' : '#1F5C4E';

        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: series.labels,
                datasets: [{
                    label: metric === 'sales' ? 'Ventes' : 'Chiffre d\'affaires',
                    data: series.values,
                    borderColor: color,
                    backgroundColor: color + '22',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: color,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#E3E8E1' }, beginAtZero: true }
                }
            }
        });
    }

    renderChart('revenue');

    const toggle = document.getElementById('chartPeriodToggle');
    if (toggle) {
        toggle.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function () {
                toggle.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                renderChart(this.dataset.metric);
            });
        });
    }

    // Stock tabs
    const tabsWrap = document.getElementById('stockTabs');
    if (tabsWrap) {
        tabsWrap.querySelectorAll('.dv2-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                tabsWrap.querySelectorAll('.dv2-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dv2-tabpanel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });
    }
})();
</script>
