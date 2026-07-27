<?php $page_title = 'Suppliers'; ob_start(); ?>

<div class="card" style="max-width: 600px; margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Add Supplier</h3>
    </div>
    <div class="card-body">
        <form id="supplierForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label class="form-label">Company Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contact Person</label>
                <input type="text" name="contact_person" class="form-control">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Supplier</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Suppliers</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?php echo SecurityHelper::escapeHtml($supplier['name']); ?></td>
                    <td><?php echo SecurityHelper::escapeHtml($supplier['contact_person'] ?? '—'); ?></td>
                    <td><?php echo SecurityHelper::escapeHtml($supplier['phone'] ?? '—'); ?></td>
                    <td><?php echo SecurityHelper::escapeHtml($supplier['email'] ?? '—'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
