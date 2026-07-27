<?php
/**
 * Premium POS Index View
 * Modern Commercial Point of Sale Interface
 */

// These variables are provided by POSController::index()
$currency = $settings['currency'] ?? 'TND ';
$tax_percentage = $settings['tax_percentage'] ?? 0;
$categories = $categories ?? [];
$products = $products ?? [];
?>

<div class="pos-premium">
    <!-- Sidebar (only for non-admin users — admins use main app sidebar) -->
    <?php if (!is_boss()): ?>
    <aside class="sidebar pos-sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Stock &amp; Sales<br><small style="opacity:.9; font-weight:600; font-size:13px;">Management System</small></h2>
        </div>

        <nav class="sidebar-menu">
            <?php $cp = $current_page ?? ($_GET['page'] ?? 'dashboard'); ?>
            <a href="index.php?page=dashboard" class="nav-item <?php echo $cp === 'dashboard' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-speedometer2"></i></div>
                <span class="nav-item-text">Tableau de bord</span>
            </a>
            <a href="index.php?page=products" class="nav-item <?php echo $cp === 'products' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-box-seam"></i></div>
                <span class="nav-item-text">Produits</span>
            </a>
            <a href="index.php?page=categories" class="nav-item <?php echo $cp === 'categories' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-tags"></i></div>
                <span class="nav-item-text">Catégories</span>
            </a>
            <a href="index.php?page=suppliers" class="nav-item <?php echo $cp === 'suppliers' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-truck"></i></div>
                <span class="nav-item-text">Fournisseurs</span>
            </a>
            <a href="index.php?page=reports" class="nav-item <?php echo $cp === 'reports' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                <span class="nav-item-text">Rapports</span>
            </a>
            <a href="index.php?page=settings" class="nav-item <?php echo $cp === 'settings' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-gear"></i></div>
                <span class="nav-item-text">Paramètres</span>
            </a>
            <a href="index.php?page=pos" class="nav-item <?php echo $cp === 'pos' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-cash-stack"></i></div>
                <span class="nav-item-text">Point de vente</span>
            </a>
            <a href="index.php?page=sales" class="nav-item <?php echo $cp === 'sales' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-receipt"></i></div>
                <span class="nav-item-text">Historique des ventes</span>
            </a>
            <a href="index.php?page=stock" class="nav-item <?php echo $cp === 'stock' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-archive"></i></div>
                <span class="nav-item-text">Historique du stock</span>
            </a>
            <a href="index.php?page=shift_history" class="nav-item <?php echo $cp === 'shift_history' ? 'active' : ''; ?>">
                <div class="nav-item-icon"><i class="bi bi-clock"></i></div>
                <span class="nav-item-text">Historique Shifts</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                <div class="user-role"><?php echo $_SESSION['role'] === 'boss' ? 'Administrateur' : 'Employé'; ?></div>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                <i class="bi bi-box-arrow-right"></i>
                Déconnexion
            </button>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="pos-main-content">
        <!-- Topbar -->
        <header class="pos-topbar">
            <div class="topbar-left">
                <div class="topbar-search">
                    <i class="bi bi-search topbar-search-icon"></i>
                    <input type="text" id="globalSearch" placeholder="Rechercher produit, code-barres..." autocomplete="off">
                    <button type="button" class="advanced-search-toggle" id="advancedSearchToggle" title="Recherche avancée">
                        <i class="bi bi-sliders"></i>
                    </button>
                </div>
            </div>

            <div class="topbar-right">
                <div class="live-clock" id="liveClock">00:00:00</div>
                <button class="topbar-btn" id="notificationBtn">
                    <i class="bi bi-bell"></i>
                    <span class="badge">3</span>
                </button>
                <button class="topbar-btn" id="darkModeToggle">
                    <i class="bi bi-moon"></i>
                </button>
                <button class="topbar-btn" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        <!-- POS Content -->
        <div class="pos-content">
            <!-- Advanced Search Panel -->
            <div class="advanced-search-panel fade-in" id="advancedSearchPanel" style="display: none;">
                <div class="advanced-search-grid">
                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <label class="filter-label">Tranche de prix (TND)</label>
                        <div class="price-inputs">
                            <input type="number" id="filterMinPrice" class="filter-input" placeholder="Min" min="0" step="0.01">
                            <span class="price-separator">à</span>
                            <input type="number" id="filterMaxPrice" class="filter-input" placeholder="Max" min="0" step="0.01">
                        </div>
                    </div>
                    
                    <!-- Stock Status Filter -->
                    <div class="filter-group">
                        <label class="filter-label">État du stock</label>
                        <div class="stock-status-group">
                            <button type="button" class="filter-pill active" data-status="all">Tous</button>
                            <button type="button" class="filter-pill" data-status="in_stock">En stock</button>
                            <button type="button" class="filter-pill" data-status="low_stock">Stock faible</button>
                            <button type="button" class="filter-pill" data-status="out_of_stock">Rupture</button>
                        </div>
                    </div>
                    
                    <!-- Sort Order Filter -->
                    <div class="filter-group">
                        <label class="filter-label">Trier par</label>
                        <select id="filterSortBy" class="filter-select">
                            <option value="name_asc">Nom (A-Z)</option>
                            <option value="name_desc">Nom (Z-A)</option>
                            <option value="price_asc">Prix : croissant</option>
                            <option value="price_desc">Prix : décroissant</option>
                            <option value="quantity_asc">Stock : croissant</option>
                            <option value="quantity_desc">Stock : décroissant</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="filter-group actions">
                        <label class="filter-label">&nbsp;</label>
                        <div class="filter-actions">
                            <button type="button" class="btn-filter-reset" id="btnResetFilters">
                                <i class="bi bi-x-circle"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Panel (at top) -->
            <aside class="pos-cart-panel" id="cartPanel">   
                <div class="cart-items" id="cartItems">
                    <div class="cart-empty-state">
                        <i class="bi bi-cart3 cart-empty-icon"></i>
                        <h4 class="cart-empty-title">Panier vide</h4>
                        <p class="cart-empty-text">Ajoutez des produits pour commencer</p>
                    </div>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span class="summary-label">Sous-total</span>
                        <span class="summary-value" id="cartSubtotal"><?php echo $currency; ?>0.00</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Remise</span>
                        <span class="summary-value" id="cartDiscount">-<?php echo $currency; ?>0.00</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">TVA (<?php echo $tax_percentage; ?>%)</span>
                        <span class="summary-value" id="cartTax"><?php echo $currency; ?>0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span class="summary-label">Total</span>
                        <span class="summary-value" id="cartTotal"><?php echo $currency; ?>0.00</span>
                    </div>

                    <input type="number" id="discountInput" class="discount-input" placeholder="Code de remise ou montant" step="0.01" min="0">
                </div>
            </aside>

            <!-- Products Section (below cart) -->
            <section class="pos-products-section">
                <div class="category-filter" id="posCategories">
                    <button class="category-pill active" data-category="">Tous</button>
                    <?php foreach ($categories as $category): ?>
                        <button class="category-pill" data-category="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="products-grid" id="posProducts">
                    <?php foreach ($products as $product): ?>
                        <?php
                            $hasStock = $product['quantity'] > 0;
                            $stock_class = !$hasStock
                                ? 'out-stock'
                                : ($product['quantity'] <= $product['minimum_stock'] ? 'low-stock' : 'in-stock');
                            $stock_text = !$hasStock
                                ? 'Rupture'
                                : ($product['quantity'] <= $product['minimum_stock'] ? 'Stock faible' : 'En stock');
                        ?>
                        <div class="product-card fade-in"
                             data-id="<?php echo $product['id']; ?>"
                             data-name="<?php echo htmlspecialchars($product['name']); ?>"
                             data-price="<?php echo $product['selling_price']; ?>"
                             data-quantity="<?php echo $product['quantity']; ?>"
                             data-barcode="<?php echo htmlspecialchars($product['barcode']); ?>"
                             data-category-id="<?php echo $product['category_id'] ?? ''; ?>"
                             data-category-name="<?php echo htmlspecialchars($product['category_name'] ?? ''); ?>"
                             data-cigarette-price="<?php echo $product['cigarette_price'] ?? ''; ?>"
                             data-cigarettes-per-pack="<?php echo $product['cigarettes_per_pack'] ?? ''; ?>"
                             data-image="<?php echo $product['image'] ?? ''; ?>">
                            <div class="product-image">
                                <?php if ($product['image']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <i class="bi bi-box-seam product-image-placeholder"></i>
                                <?php endif; ?>
                                <button class="product-favorite">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                            <div class="product-details">
                                <h4 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h4>
                                <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Produit'); ?></div>
                                <div class="product-footer">
                                    <span class="product-price"><?php echo $currency . number_format($product['selling_price'], 2); ?></span>
                                    <span class="product-stock <?php echo $stock_class; ?>"><?php echo $stock_text; ?></span>
                                </div>
                                <button class="product-add-btn">
                                    <i class="bi bi-plus-lg"></i>
                                    Ajouter
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Fixed Payment Bar -->
            <div class="payment-bar-fixed">
                <div class="payment-actions-fixed">
                    <button class="btn-pay btn-pay-primary" id="btnCheckout">
                        <i class="bi bi-credit-card"></i>
                        Encaisser
                    </button>
                    <button class="btn-pay btn-pay-success" id="btnPrint">
                        <i class="bi bi-printer"></i>
                        Imprimer reçu
                    </button>
                    <button class="btn-pay btn-pay-secondary" id="btnHold">
                        <i class="bi bi-clock"></i>
                        Mettre en attente
                    </button>
                    <button class="btn-pay btn-pay-danger" id="btnCancel">
                        <i class="bi bi-x-lg"></i>
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Mobile Cart Toggle -->
<button class="btn-pay btn-pay-primary" id="mobileCartToggle" style="display: none;">
    <i class="bi bi-cart"></i>
    <span id="mobileCartCount">0</span>
</button>

<script>
const currency = '<?php echo $currency; ?>';
const taxPercentage = <?php echo $tax_percentage; ?>;
</script>

