<?php $page_title = 'Products'; ob_start(); ?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Product Inventory</h3>
        <?php if ($_SESSION['role'] === 'boss'): ?>
        <a href="/shop_v2/index.php?url=products/create" class="btn btn-primary btn-sm">Add Product</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Buying Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Min Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><code><?php echo SecurityHelper::escapeHtml($product['barcode'] ?? 'N/A'); ?></code></td>
                    <td><?php echo SecurityHelper::escapeHtml($product['name']); ?></td>
                    <td><?php echo SecurityHelper::escapeHtml($product['category_name'] ?? '—'); ?></td>
                    <td><?php echo FormatterHelper::formatCurrency($product['buying_price']); ?></td>
                    <td><?php echo FormatterHelper::formatCurrency($product['selling_price']); ?></td>
                    <td>
                        <span class="badge <?php echo $product['quantity'] <= $product['minimum_stock'] ? 'badge-danger' : 'badge-success'; ?>">
                            <?php echo FormatterHelper::formatQuantity($product['quantity']); ?>
                        </span>
                    </td>
                    <td><?php echo $product['minimum_stock']; ?></td>
                    <td>
                        <?php if ($_SESSION['role'] === 'boss'): ?>
                        <a href="/shop_v2/index.php?url=products/edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $product['id']; ?>">Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Delete this product?')) {
                const formData = new FormData();
                formData.append('id', this.dataset.id);
                formData.append('csrf_token', '<?php echo $csrf_token; ?>');
                
                fetch('/shop_v2/index.php?url=products/delete', {
                    method: 'POST',
                    body: formData
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
