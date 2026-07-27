<?php $page_title = 'User Management'; ob_start(); ?>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">Team Members</h3>
        <a href="/shop_v2/index.php?url=register" class="btn btn-primary btn-sm">Add User</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><code><?php echo SecurityHelper::escapeHtml($user['username']); ?></code></td>
                    <td><?php echo SecurityHelper::escapeHtml($user['full_name']); ?></td>
                    <td>
                        <span class="badge <?php echo $user['role'] === 'boss' ? 'badge-danger' : 'badge-primary'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?php echo $user['status'] === 'active' ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </span>
                    </td>
                    <td><?php echo FormatterHelper::formatDate($user['created_at'], 'd/m/Y'); ?></td>
                    <td>
                        <button class="btn btn-sm btn-secondary toggle-btn" data-id="<?php echo $user['id']; ?>">
                            <?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
