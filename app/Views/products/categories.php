<?php $page_title = 'Categories'; ob_start(); ?>

<div class="card" style="max-width: 600px; margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Add Category</h3>
    </div>
    <div class="card-body">
        <form id="categoryForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" id="categoryName" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Categories</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?php echo SecurityHelper::escapeHtml($cat['name']); ?></td>
                    <td><?php echo SecurityHelper::escapeHtml(FormatterHelper::truncateText($cat['description'], 50)); ?></td>
                    <td><span class="badge badge-success"><?php echo ucfirst($cat['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/shop_v2/index.php?url=products/addCategory', {
            method: 'POST',
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
</script>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
