<?php
/**
 * Modern App Layout - Professional Design System
 * Responsive, accessible, and beautiful UI
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Check authentication
require_login();

// Get current page and user info
$current_page = $_GET['page'] ?? 'dashboard';
$user_name = htmlspecialchars($_SESSION['full_name'] ?? 'User');
$user_role = $_SESSION['role'] ?? 'employee';
$is_boss = is_boss();

// Get current time
$current_time = date('H:i');
$current_date = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
    <title><?php echo $page_title ?? 'Tableau de bord'; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Modern Design System -->
    <link rel="stylesheet" href="assets/css/modern-design.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Page-specific styles -->
    <?php if ($current_page === 'pos'): ?>
        <link rel="stylesheet" href="assets/css/pos-premium.css">
        <link rel="stylesheet" href="assets/css/pos-clean.css">
        <script src="assets/js/pos-premium.js" defer></script>
    <?php else: ?>
        <link rel="stylesheet" href="assets/css/layout-modern.css">
    <?php endif; ?>
    
    <!-- Global utilities -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        // Global helpers for compatibility
        window.getCSRFToken = function() {
            const el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : '';
        };
    </script>
</head>
<body>
<?php if ($current_page === 'pos'): ?>
    <!-- POS Page - Full screen mode -->
    <?php include $content; ?>
<?php else: ?>
    <!-- Standard App Layout -->
    <div class="app-wrapper">
        <!-- SIDEBAR -->
        <aside class="app-sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <div style="font-size: 20px; font-weight: 700; color: #2563eb;">
                    <i class="fas fa-store"></i> <?php echo APP_NAME; ?>
                </div>
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <?php if ($is_boss): ?>
                    <!-- Admin Menu Items -->
                    <a href="index.php?page=dashboard" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <span>Tableau de bord</span>
                    </a>
                    
                    <a href="index.php?page=products" class="nav-item <?php echo $current_page === 'products' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <span>Produits</span>
                    </a>
                    
                    <a href="index.php?page=categories" class="nav-item <?php echo $current_page === 'categories' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tag"></i>
                        <span>Catégories</span>
                    </a>
                    
                    <a href="index.php?page=suppliers" class="nav-item <?php echo $current_page === 'suppliers' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-truck"></i>
                        <span>Fournisseurs</span>
                    </a>
                    
                    <a href="index.php?page=stock" class="nav-item <?php echo $current_page === 'stock' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <span>Historique Stock</span>
                    </a>
                    
                    <a href="index.php?page=sales" class="nav-item <?php echo $current_page === 'sales' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-receipt"></i>
                        <span>Historique Ventes</span>
                    </a>
                    
                    <a href="index.php?page=reports" class="nav-item <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-file-chart-line"></i>
                        <span>Rapports</span>
                    </a>
                    
                    <a href="index.php?page=settings" class="nav-item <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                <?php endif; ?>
                
                <!-- POS for all users -->
                <a href="index.php?page=pos" class="nav-item nav-item-primary <?php echo $current_page === 'pos' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-cash-register"></i>
                    <span>Caisse</span>
                </a>
                
                <?php if ($is_boss): ?>
                    <a href="index.php?page=shift_history" class="nav-item <?php echo $current_page === 'shift_history' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-clock"></i>
                        <span>Shifts</span>
                    </a>
                <?php endif; ?>
            </nav>
            
            <!-- Sidebar Footer -->
            <div style="margin-top: auto; padding: 16px; border-top: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: #1f2937;">
                            <?php echo $user_name; ?>
                        </div>
                        <div style="font-size: 12px; color: #6b7280;">
                            <?php echo $user_role === 'boss' ? 'Administrateur' : 'Employé'; ?>
                        </div>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-ghost" style="width: 100%; justify-content: center; padding: 8px 12px; font-size: 12px;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </aside>
        
        <!-- MAIN CONTENT -->
        <div class="app-main">
            <!-- HEADER -->
            <header class="app-header">
                <div class="header-left">
                    <!-- Mobile Menu Toggle -->
                    <button class="btn btn-ghost btn-icon" id="sidebar-toggle" style="display: none;">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- Search Bar -->
                    <div class="header-search">
                        <i class="header-search-icon fas fa-search"></i>
                        <input type="text" placeholder="Rechercher..." id="global-search">
                    </div>
                </div>
                
                <div class="header-right">
                    <!-- Current Time -->
                    <div class="header-time">
                        <div style="font-weight: 700;" id="current-time"><?php echo $current_time; ?></div>
                        <div style="font-size: 11px; color: #9ca3af;"><?php echo $current_date; ?></div>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="btn btn-ghost btn-icon" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span style="position: absolute; top: -4px; right: -4px; width: 18px; height: 18px; background: #ef4444; color: white; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700;">2</span>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="header-user">
                        <div class="user-avatar" style="width: 32px; height: 32px;">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 12px; font-weight: 600;"><?php echo explode(' ', $user_name)[0]; ?></span>
                            <span style="font-size: 11px; color: #6b7280;">
                                <?php echo $user_role === 'boss' ? 'Admin' : 'Caissier'; ?>
                            </span>
                        </div>
                        <button class="btn btn-ghost" style="padding: 4px; margin-left: auto;">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- PAGE CONTENT -->
            <main class="app-content">
                <?php include $content; ?>
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999;"></div>
    
    <!-- JavaScript for interactive elements -->
    <script>
        // Update time every minute
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('fr-FR', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
        updateTime();
        setInterval(updateTime, 60000);
        
        // Responsive sidebar toggle (mobile)
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (window.innerWidth <= 768) {
            sidebarToggle.style.display = 'inline-flex';
        }
        
        sidebarToggle?.addEventListener('click', function() {
            sidebar.style.transform = sidebar.style.transform === 'translateX(0px)' ? 'translateX(-100%)' : 'translateX(0px)';
            overlay.style.display = sidebar.style.transform === 'translateX(0px)' ? 'none' : 'block';
        });
        
        overlay?.addEventListener('click', function() {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.style.display = 'none';
        });
    </script>
<?php endif; ?>
</body>
</html>
