/**
 * Premium POS JavaScript
 * Modern Commercial Point of Sale Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cart array
    let cart = [];
    let selectedPaymentMethod = 'cash';
    
    // DOM Elements
    const posSidebar = document.getElementById('posSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const globalSearch = document.getElementById('globalSearch');
    const posCategories = document.getElementById('posCategories');
    const posProducts = document.getElementById('posProducts');
    const cartPanel = document.getElementById('cartPanel');
    const cartItems = document.getElementById('cartItems');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartDiscount = document.getElementById('cartDiscount');
    const cartTax = document.getElementById('cartTax');
    const cartTotal = document.getElementById('cartTotal');
    const discountInput = document.getElementById('discountInput');
    const btnCheckout = document.getElementById('btnCheckout');
    const btnCancel = document.getElementById('btnCancel');
    const btnHold = document.getElementById('btnHold');
    const btnPrint = document.getElementById('btnPrint');
    const clearCart = document.getElementById('clearCart');
    const darkModeToggle = document.getElementById('darkModeToggle');
    const liveClock = document.getElementById('liveClock');
    const toastContainer = document.getElementById('toastContainer');
    const mobileCartToggle = document.getElementById('mobileCartToggle');
    const mobileCartCount = document.getElementById('mobileCartCount');
    
    // ============================================
    // SIDEBAR FUNCTIONALITY
    // ============================================
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            posSidebar.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            if (posSidebar.classList.contains('collapsed')) {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            } else {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
            }
        });
    }
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            posSidebar.classList.toggle('open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 992 && 
            !posSidebar.contains(e.target) && 
            !mobileMenuToggle.contains(e.target) &&
            posSidebar.classList.contains('open')) {
            posSidebar.classList.remove('open');
        }
    });
    
    // ============================================
    // LIVE CLOCK
    // ============================================
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        if (liveClock) {
            liveClock.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    updateClock();
    setInterval(updateClock, 1000);
    
    // ============================================
    // DARK MODE
    // ============================================
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            const html = document.documentElement;
            const icon = this.querySelector('i');
            
            if (html.getAttribute('data-theme') === 'dark') {
                html.removeAttribute('data-theme');
                icon.classList.remove('bi-sun');
                icon.classList.add('bi-moon');
            } else {
                html.setAttribute('data-theme', 'dark');
                icon.classList.remove('bi-moon');
                icon.classList.add('bi-sun');
            }
        });
    }
    
    // ============================================
    // UNIFIED SEARCH AND FILTER FUNCTION
    // ============================================
    function triggerSearch() {
        const search = globalSearch?.value.trim() || '';
        const category = document.querySelector('.category-pill.active')?.dataset.category || '';
        const minPrice = document.getElementById('filterMinPrice')?.value || '';
        const maxPrice = document.getElementById('filterMaxPrice')?.value || '';
        const stockStatus = document.querySelector('.stock-status-group .filter-pill.active')?.dataset.status || 'all';
        const sortBy = document.getElementById('filterSortBy')?.value || 'name_asc';
        
        const params = new URLSearchParams({
            page: 'pos',
            action: 'search',
            search: search,
            category_id: category,
            min_price: minPrice,
            max_price: maxPrice,
            stock_status: stockStatus,
            sort_by: sortBy
        });
        
        fetch(`index.php?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                if (posProducts) {
                    posProducts.innerHTML = html;
                    attachProductClickHandlers();
                }
            })
            .catch(error => console.error('Error searching products:', error));
    }

    // ============================================
    // GLOBAL SEARCH AND FILTERS
    // ============================================
    if (globalSearch) {
        globalSearch.addEventListener('input', debounce(function() {
            triggerSearch();
        }, 300));
        
        // Barcode scanner support
        globalSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value.trim();
                if (!val) return;
                
                if (/^\d+$/.test(val)) {
                    searchByBarcode(val);
                    this.value = '';
                    return;
                }
                
                const category = document.querySelector('.category-pill.active')?.dataset.category || '';
                const minPrice = document.getElementById('filterMinPrice')?.value || '';
                const maxPrice = document.getElementById('filterMaxPrice')?.value || '';
                const stockStatus = document.querySelector('.stock-status-group .filter-pill.active')?.dataset.status || 'all';
                const sortBy = document.getElementById('filterSortBy')?.value || 'name_asc';

                const params = new URLSearchParams({
                    page: 'pos',
                    action: 'search',
                    search: val,
                    category_id: category,
                    min_price: minPrice,
                    max_price: maxPrice,
                    stock_status: stockStatus,
                    sort_by: sortBy,
                    json: '1'
                });

                fetch(`index.php?${params.toString()}`)
                    .then(res => res.json())
                    .then(list => {
                        showSearchSuggestions(list, this);
                    })
                    .catch(err => console.error('Error fetching suggestions:', err));
            }
        });
        
        // Keyboard shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                globalSearch.focus();
            }
        });
    }
    
    function showSearchSuggestions(list, input) {
        let existing = document.getElementById('posSuggestions');
        if (existing) existing.remove();
        
        if (!list || list.length === 0) return;
        
        const container = document.createElement('div');
        container.id = 'posSuggestions';
        container.style.cssText = `
            position: absolute;
            z-index: 9999;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            max-height: 300px;
            overflow-y: auto;
            width: ${input.offsetWidth}px;
            top: ${input.getBoundingClientRect().bottom + window.scrollY + 8}px;
            left: ${input.getBoundingClientRect().left + window.scrollX}px;
        `;
        
        list.forEach(p => {
            const item = document.createElement('div');
            item.style.cssText = `
                padding: 12px 16px;
                cursor: pointer;
                transition: background 0.2s;
                display: flex;
                align-items: center;
                gap: 12px;
            `;
            item.innerHTML = `
                <div style="width: 40px; height: 40px; background: var(--surface-hover); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    ${p.image ? `<img src="uploads/${p.image}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">` : '<i class="bi bi-box-seam" style="color: var(--text-tertiary);"></i>'}
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary);">${p.name}</div>
                    <div style="font-size: 12px; color: var(--text-tertiary);">${p.barcode}</div>
                </div>
                <div style="margin-left: auto; font-weight: 700; color: var(--primary);">${currency}${parseFloat(p.price).toFixed(2)}</div>
            `;
            item.addEventListener('mouseenter', function() {
                this.style.background = 'var(--surface-hover)';
            });
            item.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
            });
            item.addEventListener('click', function() {
                addToCart(p.id, p.name, parseFloat(p.price), p.quantity, p.barcode, p.image, parseInt(p.category_id, 10) || 0, p.category_name || '', p.cigarette_price || null, p.cigarettes_per_pack || null);
                container.remove();
                input.value = '';
            });
            container.appendChild(item);
        });
        
        document.body.appendChild(container);
        
        document.addEventListener('click', function onDocClick(e) {
            if (!container.contains(e.target) && e.target !== input) {
                container.remove();
                document.removeEventListener('click', onDocClick);
            }
        });
    }
    
    // ============================================
    // CATEGORY FILTER
    // ============================================
    if (posCategories) {
        posCategories.addEventListener('click', function(e) {
            const pill = e.target.closest('.category-pill');
            if (!pill) return;
            
            document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            
            triggerSearch();
        });
    }

    // ============================================
    // ADVANCED SEARCH CONTROLS
    // ============================================
    const advancedSearchToggle = document.getElementById('advancedSearchToggle');
    const advancedSearchPanel = document.getElementById('advancedSearchPanel');
    const filterMinPrice = document.getElementById('filterMinPrice');
    const filterMaxPrice = document.getElementById('filterMaxPrice');
    const filterSortBy = document.getElementById('filterSortBy');
    const btnResetFilters = document.getElementById('btnResetFilters');

    if (advancedSearchToggle && advancedSearchPanel) {
        advancedSearchToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            if (advancedSearchPanel.style.display === 'none') {
                advancedSearchPanel.style.display = 'block';
            } else {
                advancedSearchPanel.style.display = 'none';
            }
        });
    }

    if (filterMinPrice) {
        filterMinPrice.addEventListener('input', debounce(function() {
            triggerSearch();
        }, 300));
    }

    if (filterMaxPrice) {
        filterMaxPrice.addEventListener('input', debounce(function() {
            triggerSearch();
        }, 300));
    }

    if (filterSortBy) {
        filterSortBy.addEventListener('change', function() {
            triggerSearch();
        });
    }

    document.querySelectorAll('.stock-status-group .filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.stock-status-group .filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            triggerSearch();
        });
    });

    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', function() {
            if (filterMinPrice) filterMinPrice.value = '';
            if (filterMaxPrice) filterMaxPrice.value = '';
            if (filterSortBy) filterSortBy.value = 'name_asc';
            
            document.querySelectorAll('.stock-status-group .filter-pill').forEach(p => p.classList.remove('active'));
            const defaultStockPill = document.querySelector('.stock-status-group .filter-pill[data-status="all"]');
            if (defaultStockPill) defaultStockPill.classList.add('active');
            
            triggerSearch();
        });
    }
    
    // ============================================
    // PRODUCT CLICK HANDLERS
    // ============================================
    attachProductClickHandlers();
    
    function attachProductClickHandlers() {
        document.querySelectorAll('.product-card').forEach(card => {
            const addBtn = card.querySelector('.product-add-btn');
            const favoriteBtn = card.querySelector('.product-favorite');
            
            // Add to cart on card click
            card.addEventListener('click', function(e) {
                if (e.target.closest('.product-favorite')) return;
                
                const productId = parseInt(this.dataset.id);
                const productName = this.dataset.name;
                const productPrice = parseFloat(this.dataset.price);
                const productQuantity = parseFloat(this.dataset.quantity);
                const productBarcode = this.dataset.barcode;
                const productImage = this.dataset.image;
                const productCategoryId = parseInt(this.dataset.categoryId) || 0;
                const productCategoryName = this.dataset.categoryName || '';
                const productCigarettePrice = this.dataset.cigarettePrice || null;
                const productCigarettesPerPack = this.dataset.cigarettesPerPack || null;
                const cpp = getTobaccoCigarettesPerPack(productCigarettesPerPack);
                const totalAvailableStock = productCategoryId === 6 && cpp > 0
                    ? productQuantity * cpp
                    : productQuantity;
                      
                if (productQuantity <= 0 || totalAvailableStock <= 0) {
                    showToast('Produit en rupture de stock', 'error');
                    return;
                }
                   
                addToCart(productId, productName, productPrice, productQuantity, productBarcode, productImage, productCategoryId, productCategoryName, productCigarettePrice, productCigarettesPerPack);
            });
            
            // Favorite button
            if (favoriteBtn) {
                favoriteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('active');
                    const icon = this.querySelector('i');
                    if (this.classList.contains('active')) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                        showToast('Ajouté aux favoris', 'success');
                    } else {
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                        showToast('Retiré des favoris', 'success');
                    }
                });
            }
        });
    }
    
    // ============================================
    // SEARCH BY BARCODE
    // ============================================
    function searchByBarcode(barcode) {
        fetch(`index.php?page=pos&action=getProductByBarcode&barcode=${encodeURIComponent(barcode)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.product;
                    const categoryId = parseInt(product.category_id, 10) || 0;
                    const cpp = getTobaccoCigarettesPerPack(product.cigarettes_per_pack);
                    const availableBarcodeStock = categoryId === 6 && cpp > 0
                        ? (parseFloat(product.quantity) * cpp)
                        : parseFloat(product.quantity);
                    if (availableBarcodeStock <= 0) {
                        showToast('Produit en rupture de stock', 'error');
                        return;
                    }
                    addToCart(product.id, product.name, product.price, parseFloat(product.quantity), product.barcode, product.image, product.category_id || 0, product.category_name || '', product.cigarette_price || null, product.cigarettes_per_pack || null);
                } else {
                    showToast('Produit introuvable', 'error');
                }
            })
            .catch(error => console.error('Error searching by barcode:', error));
    }
    
    // ============================================
    // ADD TO CART
    // ============================================
    function addToCart(id, name, price, availableQty, barcode, image, categoryId = 0, categoryName = '', cigarettePrice = null, cigarettesPerPack = null) {
        // If product belongs to Tabac category_id 6, ask the cashier which mode to use
        if (categoryId === 6) {
            showTobaccoModeDialog({id, name, price, availableQty, barcode, image, cigarettePrice, cigarettesPerPack}, function(selection) {
                // selection: { unit: 'pack'|'cigarette', quantity }
                addToCartWithUnit(id, name, price, availableQty, barcode, image, categoryId, selection.unit, selection.quantity, cigarettePrice, cigarettesPerPack);
            });
            return;
        }

        // Default behavior for non-tobac products
        addToCartWithUnit(id, name, price, availableQty, barcode, image, categoryId, 'pack', 1, cigarettePrice, cigarettesPerPack);
    }

    function isTobaccoProduct(categoryId) {
        return categoryId === 6;
    }

    function getTobaccoCigarettesPerPack(cigarettesPerPack) {
        const cpp = parseInt(cigarettesPerPack, 10);
        if (isNaN(cpp) || cpp <= 0) {
            return 0;
        }
        return cpp;
    }

    function getTobaccoAvailableCigarettes(packQuantity, cigarettesPerPack) {
        const cpp = getTobaccoCigarettesPerPack(cigarettesPerPack);
        if (cpp <= 0) {
            return 0;
        }
        return Math.round(packQuantity * cpp);
    }

    function getReservedCigarettes(productId, cigarettesPerPack, excludeKey = null) {
        return cart.reduce((sum, item) => {
            if (item.id !== productId) {
                return sum;
            }
            if (excludeKey && item.idKey === excludeKey) {
                return sum;
            }
            const cpp = getTobaccoCigarettesPerPack(item.cigarettes_per_pack || cigarettesPerPack);
            if (item.unit_type === 'pack') {
                return sum + item.quantity * cpp;
            }
            return sum + item.quantity;
        }, 0);
    }

    function getItemMaxQuantity(item) {
        if (!isTobaccoProduct(item.category_id)) {
            return item.stockQuantity || item.availableQty || 0;
        }

        const cpp = getTobaccoCigarettesPerPack(item.cigarettes_per_pack);
        const totalAvailable = getTobaccoAvailableCigarettes(item.stockQuantity, cpp);
        const reservedByOthers = getReservedCigarettes(item.id, cpp, item.idKey);
        const availableAfterOtherReservations = Math.max(0, totalAvailable - reservedByOthers);

        if (item.unit_type === 'pack') {
            return Math.floor(availableAfterOtherReservations / cpp);
        }

        return availableAfterOtherReservations;
    }

    function refreshCartItemAvailability() {
        cart.forEach(item => {
            item.availableQty = getItemMaxQuantity(item);
        });
    }

    function addToCartWithUnit(id, name, price, availableQty, barcode, image, categoryId, unit, qty, cigarettePrice = null, cigarettesPerPack = null) {
        const itemIdKey = unit === 'cigarette' ? id + '_cig' : id; // differentiate key for same product different unit in cart
        const existingItem = cart.find(item => item.idKey === itemIdKey);
        const cpp = getTobaccoCigarettesPerPack(cigarettesPerPack);
        const isTobacProduct = isTobaccoProduct(categoryId);

        // Compute current maximum quantity for this item
        const stockQuantity = availableQty;
        const itemForCalculation = {
            id,
            idKey: itemIdKey,
            category_id: categoryId,
            unit_type: unit,
            cigarettes_per_pack: cigarettesPerPack,
            stockQuantity
        };
        const maxQuantity = getItemMaxQuantity(itemForCalculation);

        if (qty > maxQuantity) {
            showToast('Stock insuffisant', 'error');
            return;
        }

        if (existingItem) {
            if (existingItem.quantity + qty > maxQuantity) {
                showToast('Stock maximum atteint', 'warning');
                return;
            }
            existingItem.quantity += qty;
        } else {
            const displayName = unit === 'cigarette' ? `${name} (Cigarette)` : `${name} (Paquet)`;
            const unitPrice = unit === 'cigarette' && cigarettePrice ? parseFloat(cigarettePrice) : price;

            cart.push({
                id: id,
                idKey: itemIdKey,
                name: displayName,
                baseName: name,
                price: parseFloat(unitPrice),
                quantity: qty,
                availableQty: maxQuantity,
                stockQuantity: stockQuantity,
                unit_type: unit,
                category_id: categoryId,
                cigarettes_per_pack: cigarettesPerPack || null,
                barcode: barcode,
                image: image
            });
        }

        updateCart();
        showToast('Produit ajouté au panier', 'success');
        playSound('add');
    }

    function showTobaccoModeDialog(product, cb) {
        // Create modal if not exists
        let existing = document.getElementById('tobaccoModeModal');
        if (existing) existing.remove();

        const modal = document.createElement('div');
        modal.id = 'tobaccoModeModal';
        modal.style.cssText = 'position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:99999;';
        modal.innerHTML = `
            <div style="background:var(--surface);padding:20px;border-radius:8px;min-width:320px;max-width:90%;">
                <h3 style="margin-top:0;">Mode de vente</h3>
                <p style="margin:6px 0 12px 0;font-size:14px;color:var(--text-tertiary);">${product.name}</p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button id="tobaccoPackBtn" style="flex:1; padding:10px;">Par paquet</button>
                    <button id="tobaccoCigBtn" style="flex:1; padding:10px;">Par cigarette</button>
                </div>
                <div id="tobaccoCigPanel" style="display:none;margin-top:12px;">
                    <label>Quantité (cigarettes)</label>
                    <input id="tobaccoCigQty" type="number" min="1" value="1" style="width:100%;padding:8px;margin-top:6px;">
                </div>
                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
                    <button id="tobaccoCancelBtn">Annuler</button>
                    <button id="tobaccoConfirmBtn" style="background:var(--primary);color:#fff;padding:8px 12px;border-radius:4px;">Confirmer</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const packBtn = modal.querySelector('#tobaccoPackBtn');
        const cigBtn = modal.querySelector('#tobaccoCigBtn');
        const cigPanel = modal.querySelector('#tobaccoCigPanel');
        const cigQty = modal.querySelector('#tobaccoCigQty');
        const cancelBtn = modal.querySelector('#tobaccoCancelBtn');
        const confirmBtn = modal.querySelector('#tobaccoConfirmBtn');

        let selected = 'pack';

        packBtn.addEventListener('click', function() {
            selected = 'pack';
            cigPanel.style.display = 'none';
            packBtn.style.opacity = '1';
            cigBtn.style.opacity = '0.6';
        });
        cigBtn.addEventListener('click', function() {
            selected = 'cigarette';
            cigPanel.style.display = 'block';
            packBtn.style.opacity = '0.6';
            cigBtn.style.opacity = '1';
        });

        cancelBtn.addEventListener('click', function() {
            modal.remove();
        });

        confirmBtn.addEventListener('click', function() {
            const cpp = getTobaccoCigarettesPerPack(product.cigarettesPerPack);
            if (selected === 'cigarette') {
                if (!product.cigarettePrice || parseFloat(product.cigarettePrice) <= 0) {
                    showToast('Le prix par cigarette n\'est pas configuré pour ce produit.', 'error');
                    return;
                }

                if (cpp <= 0) {
                    showToast('Le nombre de cigarettes par paquet est invalide pour ce produit.', 'error');
                    return;
                }

                let q = parseInt(cigQty.value, 10) || 1;
                if (q <= 0) {
                    showToast('Quantité de cigarettes invalide', 'error');
                    return;
                }

                const maxCigarettes = getTobaccoAvailableCigarettes(product.availableQty, cpp);
                if (q > maxCigarettes) {
                    showToast('Stock insuffisant', 'error');
                    return;
                }

                cb({unit: selected, quantity: q});
            } else {
                cb({unit: selected, quantity: 1});
            }

            modal.remove();
        });
    }
    
    // ============================================
    // UPDATE CART DISPLAY
    // ============================================
    function updateCart() {
        refreshCartItemAvailability();

        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div class="cart-empty-state">
                    <i class="bi bi-cart3 cart-empty-icon"></i>
                    <h4 class="cart-empty-title">Panier vide</h4>
                    <p class="cart-empty-text">Ajoutez des produits pour commencer</p>
                </div>
            `;
        } else {
            cartItems.innerHTML = cart.map((item, index) => `
                <div class="cart-item fade-in">
                    <div class="cart-item-image">
                        ${item.image ? `<img src="uploads/${item.image}" alt="">` : '<i class="bi bi-box-seam" style="font-size: 24px; color: var(--text-tertiary);"></i>'}
                    </div>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${currency}${item.price.toFixed(2)}</div>
                    </div>
                    <div class="cart-item-actions">
                        <div class="cart-item-quantity">
                            <button class="qty-btn" onclick="updateQuantity(${index}, -1)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input
                                type="number"
                                class="qty-value"
                                value="${item.quantity}"
                                min="1"
                                max="${item.availableQty}"
                                inputmode="numeric"
                                onchange="setQuantity(${index}, this.value)"
                                onclick="this.select()"
                            >
                            <button class="qty-btn" onclick="updateQuantity(${index}, 1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <div class="cart-item-total">${currency}${(item.price * item.quantity).toFixed(2)}</div>
                        <button class="cart-item-remove" onclick="removeFromCart(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        updateTotals();
        updateMobileCartCount();
    }
    
    // ============================================
    // UPDATE QUANTITY
    // ============================================
    window.updateQuantity = function(index, change) {
        const item = cart[index];
        if (!item) return;

        const maxQuantity = getItemMaxQuantity(item);
        const newQuantity = item.quantity + change;
        
        if (newQuantity <= 0) {
            removeFromCart(index);
            return;
        }
        
        if (newQuantity > maxQuantity) {
            showToast('Stock maximum atteint', 'warning');
            return;
        }
        
        item.quantity = newQuantity;
        updateCart();
    };
    
    // ============================================
    // SET QUANTITY (manual input)
    // ============================================
    window.setQuantity = function(index, value) {
        const item = cart[index];
        if (!item) return;

        let newQuantity = parseInt(value, 10);

        if (isNaN(newQuantity) || newQuantity <= 0) {
            removeFromCart(index);
            return;
        }

        const maxQuantity = getItemMaxQuantity(item);
        if (newQuantity > maxQuantity) {
            newQuantity = maxQuantity;
            showToast('Stock maximum atteint', 'warning');
        }

        item.quantity = newQuantity;
        updateCart();
    };
    
    // ============================================
    // REMOVE FROM CART
    // ============================================
    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCart();
        showToast('Produit retiré du panier', 'success');
    };
    
    // ============================================
    // UPDATE TOTALS
    // ============================================
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = safeParseFloat(discountInput?.value || 0);
        const taxableAmount = subtotal - discount;
        const tax = taxableAmount * (taxPercentage / 100);
        const total = taxableAmount + tax;
        
        if (cartSubtotal) cartSubtotal.textContent = `${currency}${subtotal.toFixed(2)}`;
        if (cartDiscount) cartDiscount.textContent = `-${currency}${discount.toFixed(2)}`;
        if (cartTax) cartTax.textContent = `${currency}${tax.toFixed(2)}`;
        if (cartTotal) cartTotal.textContent = `${currency}${total.toFixed(2)}`;
    }
    
    // ============================================
    // UPDATE MOBILE CART COUNT
    // ============================================
    function updateMobileCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (mobileCartCount) {
            mobileCartCount.textContent = totalItems;
        }
    }
    
    // ============================================
    // DISCOUNT INPUT
    // ============================================
    if (discountInput) {
        discountInput.addEventListener('input', updateTotals);
    }
    
    // ============================================
    // CLEAR CART
    // ============================================
    if (clearCart) {
        clearCart.addEventListener('click', function() {
            if (cart.length > 0 && confirm('Vider le panier ?')) {
                cart = [];
                updateCart();
                if (discountInput) discountInput.value = '';
                showToast('Panier vidé', 'success');
            }
        });
    }
    
    // ============================================
    // CANCEL BUTTON
    // ============================================
    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            if (cart.length > 0 && confirm('Annuler la vente ?')) {
                cart = [];
                updateCart();
                if (discountInput) discountInput.value = '';
                showToast('Vente annulée', 'success');
            }
        });
    }
    
    // ============================================
    // HOLD ORDER
    // ============================================
    if (btnHold) {
        btnHold.addEventListener('click', function() {
            if (cart.length === 0) {
                showToast('Le panier est vide', 'error');
                return;
            }
            showToast('Commande mise en attente', 'success');
            // Implement hold order logic
        });
    }
    
    // ============================================
    // PRINT RECEIPT
    // ============================================
    if (btnPrint) {
        btnPrint.addEventListener('click', function() {
            if (cart.length === 0) {
                showToast('Le panier est vide', 'error');
                return;
            }
            window.print();
        });
    }
    
    // ============================================
    // CHECKOUT BUTTON
    // ============================================
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function() {
            if (cart.length === 0) {
                showToast('Le panier est vide', 'error');
                return;
            }
            
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discount = safeParseFloat(discountInput?.value || 0);
            const taxableAmount = subtotal - discount;
            const tax = taxableAmount * (taxPercentage / 100);
            const total = taxableAmount + tax;
            
            // Submit the order
            // NOTE: index.php routes on $_GET['action'], and the controller method
            // is named completeSale() (not "checkout") — both must match, or the
            // request silently falls through to POSController::index(), which
            // returns the full HTML page instead of JSON (hence the
            // "Unexpected token '<'" / "Erreur de connexion au serveur" errors).
            const payload = {
                items: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price,
                    unit_type: item.unit_type || 'pack'
                })),
                subtotal: subtotal,
                discount: discount,
                tax: tax,
                total: total,
                payment_method: selectedPaymentMethod || 'cash'
            };

            fetch('index.php?page=pos&action=completeSale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Commande enregistrée avec succès!', 'success');
                    cart = [];
                    updateCart();
                    if (discountInput) discountInput.value = '';
                    playSound('success');
                } else {
                    showToast('Erreur: ' + (data.message || 'Impossible de traiter la commande'), 'error');
                }
            })
            .catch(error => {
                console.error('Checkout error:', error);
                showToast('Erreur de connexion au serveur', 'error');
            });
        });
    }
    
    // ============================================
    // MOBILE CART TOGGLE
    // ============================================
    if (mobileCartToggle) {
        mobileCartToggle.addEventListener('click', function() {
            cartPanel.classList.toggle('open');
        });
    }
    
    // ============================================
    // TOAST NOTIFICATIONS
    // ============================================
    function showToast(message, type = 'success') {
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        let icon = 'bi-check-circle';
        if (type === 'error') icon = 'bi-x-circle';
        if (type === 'warning') icon = 'bi-exclamation-circle';
        
        toast.innerHTML = `
            <i class="bi ${icon} toast-icon"></i>
            <span class="toast-message">${message}</span>
            <i class="bi bi-x toast-close"></i>
        `;
        
        toastContainer.appendChild(toast);
        
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.remove();
        });
        
        setTimeout(() => {
            toast.style.animation = 'slideIn 0.2s ease reverse';
            setTimeout(() => toast.remove(), 200);
        }, 3000);
    }
    
    // ============================================
    // SOUND EFFECTS
    // ============================================
    function playSound(type) {
        // Create audio context for sound effects
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        if (type === 'add') {
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }
    }
    
    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    function safeParseFloat(value) {
        const parsed = parseFloat(value);
        return isNaN(parsed) ? 0 : parsed;
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // ============================================
    // KEYBOARD SHORTCUTS
    // ============================================
    document.addEventListener('keydown', function(e) {
        // F2 - Focus search
        if (e.key === 'F2') {
            e.preventDefault();
            globalSearch?.focus();
        }
        
        // F4 - Checkout
        if (e.key === 'F4') {
            e.preventDefault();
            if (cart.length > 0) {
                btnCheckout?.click();
            }
        }
        
        // F8 - Clear cart
        if (e.key === 'F8') {
            e.preventDefault();
            clearCart?.click();
        }
        
        // Escape - Close modals/panels
        if (e.key === 'Escape') {
            cartPanel?.classList.remove('open');
            posSidebar?.classList.remove('open');
        }
        
        // Ctrl/Cmd + B - Toggle sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            sidebarToggle?.click();
        }
    });
    
    // ============================================
    // RESPONSIVE HANDLING
    // ============================================
    function handleResize() {
        if (window.innerWidth <= 992) {
            if (mobileCartToggle) {
                mobileCartToggle.style.display = 'flex';
                mobileCartToggle.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    z-index: 999;
                    box-shadow: var(--shadow-xl);
                `;
            }
        } else {
            if (mobileCartToggle) {
                mobileCartToggle.style.display = 'none';
            }
            cartPanel?.classList.remove('open');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize();
});
