<?php $page_title = 'Analytics & Reports'; ob_start(); ?>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-value"><?php echo $stats['total_sales'] ?? 0; ?></div>
        <div class="stat-label">Total Transactions</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo FormatterHelper::formatCurrency($stats['total_revenue'] ?? 0); ?></div>
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo FormatterHelper::formatCurrency($profitStats['total_profit'] ?? 0); ?></div>
        <div class="stat-label">Total Profit</div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Sales by Payment Method</h3>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 28px; font-weight: 700; color: #10B981;">
                    <?php echo FormatterHelper::formatCurrency($stats['cash_sales'] ?? 0); ?>
                </div>
                <div style="font-size: 12px; color: #64748B; margin-top: 8px;">Cash Sales</div>
            </div>
            <div style="text-align: center; padding: 20px;">
                <div style="font-size: 28px; font-weight: 700; color: #4F46E5;">
                    <?php echo FormatterHelper::formatCurrency($stats['card_sales'] ?? 0); ?>
                </div>
                <div style="font-size: 12px; color: #64748B; margin-top: 8px;">Card Sales</div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Top Selling Products</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Revenue</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $product): ?>
                    <tr>
                        <td><?php echo SecurityHelper::escapeHtml($product['name']); ?></td>
                        <td><?php echo FormatterHelper::formatCurrency($product['revenue']); ?></td>
                        <td><?php echo FormatterHelper::formatCurrency($product['profit']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sales by Category</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Revenue</th>
                        <th>Profit %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoryStats as $cat): ?>
                    <tr>
                        <td><?php echo SecurityHelper::escapeHtml($cat['name'] ?? 'Uncategorized'); ?></td>
                        <td><?php echo FormatterHelper::formatCurrency($cat['revenue']); ?></td>
                        <td><?php echo $cat['revenue'] > 0 ? round(($cat['profit'] / $cat['revenue']) * 100, 1) : 0; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
