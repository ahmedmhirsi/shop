<?php $page_title = 'Settings'; ob_start(); ?>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">System Settings</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label class="form-label">Store Name</label>
                <input type="text" name="store_name" class="form-control" value="Mon Magasin">
            </div>

            <div class="form-group">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" class="form-control" value="TND">
            </div>

            <div class="form-group">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" name="tax_rate" class="form-control" step="0.01" value="0">
            </div>

            <div class="form-group">
                <label class="form-label">Low Stock Alert Threshold</label>
                <input type="number" name="low_stock_alert" class="form-control" value="5">
            </div>

            <div class="form-group">
                <label class="form-label">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" class="form-control" value="INV-">
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
