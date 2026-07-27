<?php $page_title = 'Sales History'; ob_start(); ?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Sales Records</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Date/Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><strong><?php echo SecurityHelper::escapeHtml($sale['invoice_number']); ?></strong></td>
                    <td><?php echo SecurityHelper::escapeHtml($sale['customer_name'] ?? 'Counter'); ?></td>
                    <td><?php echo SecurityHelper::escapeHtml($sale['full_name'] ?? '—'); ?></td>
                    <td><?php echo FormatterHelper::formatCurrency($sale['total']); ?></td>
                    <td><span class="badge badge-primary"><?php echo ucfirst($sale['payment_method']); ?></span></td>
                    <td><?php echo FormatterHelper::formatDate($sale['created_at'], 'd/m/Y H:i'); ?></td>
                    <td>
                        <a href="/shop_v2/index.php?url=pos/printReceipt&id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-primary" target="_blank">Receipt</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
