<?php
if (!SecurityHelper::isAuthenticated()) {
    header('Location: /shop_v2/index.php?url=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? SecurityHelper::escapeHtml($page_title) : 'Dashboard'; ?> - Stock & Sales Management</title>
    <link rel="stylesheet" href="/shop_v2/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">📦 Stock & Sales</div>
            </div>

            <nav class="sidebar-menu">
                <?php if ($_SESSION['role'] === 'boss'): ?>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=dashboard" class="sidebar-menu-link">
                        <i class="fas fa-chart-line sidebar-menu-icon"></i>
                        Dashboard
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=pos" class="sidebar-menu-link">
                        <i class="fas fa-cash-register sidebar-menu-icon"></i>
                        POS
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=products" class="sidebar-menu-link">
                        <i class="fas fa-box sidebar-menu-icon"></i>
                        Products
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=pos/sales" class="sidebar-menu-link">
                        <i class="fas fa-receipt sidebar-menu-icon"></i>
                        Sales
                    </a>
                </li>
                <?php if ($_SESSION['role'] === 'boss'): ?>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=products/categories" class="sidebar-menu-link">
                        <i class="fas fa-tags sidebar-menu-icon"></i>
                        Categories
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=products/suppliers" class="sidebar-menu-link">
                        <i class="fas fa-truck sidebar-menu-icon"></i>
                        Suppliers
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=dashboard/analytics" class="sidebar-menu-link">
                        <i class="fas fa-chart-bar sidebar-menu-icon"></i>
                        Analytics
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=dashboard/users" class="sidebar-menu-link">
                        <i class="fas fa-users sidebar-menu-icon"></i>
                        Users
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/shop_v2/index.php?url=dashboard/settings" class="sidebar-menu-link">
                        <i class="fas fa-cog sidebar-menu-icon"></i>
                        Settings
                    </a>
                </li>
                <?php endif; ?>
            </nav>

            <div class="sidebar-user">
                <span class="sidebar-user-name"><?php echo SecurityHelper::escapeHtml($_SESSION['full_name']); ?></span>
                <span style="font-size: 11px; color: rgba(255,255,255,0.5);"><?php echo ucfirst($_SESSION['role']); ?></span>
                <a href="/shop_v2/index.php?url=logout" class="sidebar-logout">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="topbar-title"><?php echo isset($page_title) ? SecurityHelper::escapeHtml($page_title) : 'Dashboard'; ?></h1>
                <div class="topbar-actions">
                    <span style="font-size: 12px; color: #64748B;">
                        <?php echo date('l, d M Y H:i'); ?>
                    </span>
                </div>
            </div>

            <!-- Page Content -->
            <div class="content">
                <?php echo $content; ?>
            </div>
        </div>
    </div>

    <script src="/shop_v2/public/js/app.js"></script>
</body>
</html>
