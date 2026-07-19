<?php
/**
 * Main Layout
 * Includes sidebar, header, and content area
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Check authentication
require_login();

// Get current page
$current_page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <title><?php echo $page_title ?? 'Tableau de bord'; ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if ($current_page === 'pos'): ?>
        <link rel="stylesheet" href="assets/css/pos-premium.css">
        <link rel="stylesheet" href="assets/css/pos-clean.css">
        <link rel="stylesheet" href="assets/css/sidebar-pos.css">
        <link rel="stylesheet" href="assets/css/pos-enhance.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css">
        <script>
            // Bridge: provide legacy global functions/vars expected by older POS code
            // (prevents Encaisser/boutons from failing due to missing modal helpers)
            window.getCSRFToken = window.getCSRFToken || function() {
                const el = document.querySelector('meta[name="csrf-token"]');
                return el ? el.getAttribute('content') : '';
            };
            window.openModal = window.openModal || function() {};
            window.closeModal = window.closeModal || function() {};
            window.safeParseFloat = window.safeParseFloat || function(v){
                const n = parseFloat(v);
                return isNaN(n) ? 0 : n;
            };
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <script src="assets/js/pos-premium.js" defer></script>
        <style>
            /* Hide old layout elements on POS page for non-admin users to prevent conflicts */
            <?php if ($current_page === 'pos' && !is_boss()): ?>
                .app-container > .sidebar, 
                .app-container > .main-content,
                .app-container > .sidebar *,
                .app-container > .main-content * {
                    display: none !important;
                }
                .app-container {
                    display: block !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }
                body {
                    margin: 0 !important;
                    padding: 0 !important;
                    overflow-x: hidden !important;
                }
            <?php endif; ?>
        </style>
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="<?php echo $current_page === 'pos' ? 'page-pos' : ''; ?>">
<?php if ($current_page === 'pos' && !is_boss()): ?>
    <?php include $content; ?>
<?php else: ?>


        <!-- Other pages - Use standard layout -->
        <div class="app-container">
            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <h2><?php echo APP_NAME; ?></h2>
                </div>
                
                <nav class="sidebar-menu">
                    <?php if (is_boss()): ?>
                        <a href="index.php?page=dashboard" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i>
                            Tableau de bord
                        </a>
                        <a href="index.php?page=products" class="<?php echo $current_page === 'products' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i>
                            Produits
                        </a>
                        <a href="index.php?page=categories" class="<?php echo $current_page === 'categories' ? 'active' : ''; ?>">
                            <i class="fas fa-tags"></i>
                            Catégories
                        </a>
                        <a href="index.php?page=suppliers" class="<?php echo $current_page === 'suppliers' ? 'active' : ''; ?>">
                            <i class="fas fa-truck"></i>
                            Fournisseurs
                        </a>
                        <a href="index.php?page=reports" class="<?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                            <i class="fas fa-file-alt"></i>
                            Rapports
                        </a>
                        <a href="index.php?page=settings" class="<?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i>
                            Paramètres
                        </a>
                    <?php endif; ?>
                    
                    <a href="index.php?page=pos" class="<?php echo $current_page === 'pos' ? 'active' : ''; ?>">
                        <i class="fas fa-cash-register"></i>
                        Point de vente
                    </a>
                    
                    <?php if (is_boss()): ?>
                        <a href="index.php?page=sales" class="<?php echo $current_page === 'sales' ? 'active' : ''; ?>">
                            <i class="fas fa-receipt"></i>
                            Historique des ventes
                        </a>
                        <a href="index.php?page=stock" class="<?php echo $current_page === 'stock' ? 'active' : ''; ?>">
                            <i class="fas fa-warehouse"></i>
                            Historique du stock
                        </a>
                        <a href="index.php?page=shift_history" class="<?php echo $current_page === 'shift_history' ? 'active' : ''; ?>">
                            <i class="fas fa-clock"></i>
                            Historique Shifts
                        </a>
                    <?php endif; ?>
                </nav>
                
                <div class="sidebar-footer">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                            <div class="user-role"><?php echo $_SESSION['role'] === 'boss' ? 'Administrateur' : 'Employé'; ?></div>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-danger btn-sm btn-block">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="main-content">
                <!-- Header -->
                <header class="header">
                    <div class="header-left">
                    <button class="btn btn-outline btn-sm" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title"><?php echo $page_title ?? 'Tableau de bord'; ?></h1>
                </div>
                
                <div class="header-actions">
                    <button class="btn btn-outline btn-sm" id="darkModeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <?php if (is_boss()): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline btn-sm">
                                <i class="fas fa-bell"></i>
                                <span class="badge badge-danger" id="notificationBadge">0</span>
                            </button>
                            <div class="dropdown-content">
                                <a href="index.php?page=products">Alertes de stock faible</a>
                                <a href="index.php?page=products">Ruptures de stock</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </header>
            
            <!-- Content Area -->
            <div class="content">
                <?php
                $flash = get_flash();
                if ($flash):
                ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>">
                        <?php echo htmlspecialchars($flash['message']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($content) && file_exists($content)): ?>
                    <?php include $content; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php endif; ?>
    
    <!-- Notification Container -->
    <div id="notificationContainer"></div>
    
    <?php if ($current_page === 'pos'): ?>
    <script>
        // Sidebar toggle for POS context (mobile / compact)
        (function(){
            var btn = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('sidebar');
            if (btn && sidebar) {
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    sidebar.classList.toggle('open');
                });
            }
            // Ensure payment bar stays on top if present
            var paymentBar = document.querySelector('.payment-bar-fixed');
            if (paymentBar) {
                paymentBar.style.zIndex = 11050;
            }
        })();
    </script>
    <?php endif; ?>

    <script src="assets/js/main.js"></script>
    <?php if ($current_page === 'dashboard'): ?>
        <script src="assets/js/charts.js"></script>
    <?php endif; ?>
</body>
</html>
