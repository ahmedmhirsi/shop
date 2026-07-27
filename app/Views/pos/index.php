<?php $page_title = 'Point of Sale'; ob_start(); ?>

<div id="posContainer" style="display: grid; grid-template-columns: 1fr 350px; gap: 24px; height: calc(100vh - 120px);">
    <!-- Product Catalog -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="card" style="flex: 0 0 auto;">
            <input 
                type="text" 
                id="productSearch" 
                class="form-control" 
                placeholder="Search by name or barcode (F2 to focus)..."
                autofocus
                style="padding: 12px;"
            >
            <div id="searchResults" style="max-height: 200px; overflow-y: auto;"></div>
        </div>

        <div class="card" style="flex: 1; overflow-y: auto;">
            <div class="card-header">
                <h3 class="card-title">Products</h3>
                <div>
                    <label class="form-label" style="margin: 0; font-size: 12px;">
                        <input type="checkbox" id="modeToggle"> 
                        <span id="modeLabel">Mode Paquet</span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <table class="table" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo SecurityHelper::escapeHtml($product['name']); ?></td>
                            <td><?php echo FormatterHelper::formatQuantity($product['quantity']); ?></td>
                            <td><?php echo FormatterHelper::formatCurrency($product['selling_price']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary add-to-cart-btn"
                                    data-id="<?php echo $product['id']; ?>"
                                    data-barcode="<?php echo SecurityHelper::escapeHtml($product['barcode'] ?? ''); ?>"
                                    data-cigarette-price="<?php echo $product['cigarette_price'] ?? 0; ?>"
                                    data-cigarettes-per-pack="<?php echo $product['cigarettes_per_pack'] ?? 20; ?>"
                                >Add</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cart & Checkout -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Cart -->
        <div class="card" style="flex: 1; overflow-y: auto;">
            <div class="card-header">
                <h3 class="card-title">Cart</h3>
            </div>
            <div id="cartItems" class="card-body" style="max-height: 400px; overflow-y: auto;"></div>
        </div>

        <!-- Checkout Form -->
        <div class="card">
            <div class="card-body">
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 12px;">Subtotal</label>
                    <div style="font-size: 18px; font-weight: 600;" id="subtotal">0.00 TND</div>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 12px;">Tax</label>
                    <div style="font-size: 14px; font-weight: 600;" id="tax">0.00 TND</div>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 12px;">Discount</label>
                    <input type="number" id="discount" class="form-control" value="0" step="0.01" style="font-size: 12px;">
                </div>

                <div style="background: #F0F4F8; padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                    <div style="font-size: 12px; color: #64748B; margin-bottom: 4px;">TOTAL</div>
                    <div id="cartTotal" style="font-size: 24px; font-weight: 700; color: #4F46E5;">0.00 TND</div>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 12px;">Payment Method</label>
                    <select id="paymentMethod" class="form-control" style="font-size: 12px;">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 12px;">Amount Received</label>
                    <input type="number" id="amountReceived" class="form-control" value="0" step="0.01" style="font-size: 12px;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" style="font-size: 12px;">Change</label>
                    <div id="change" style="font-size: 16px; font-weight: 600; color: #10B981;">0.00 TND</div>
                </div>

                <button id="checkoutBtn" class="btn btn-success btn-block" style="margin-bottom: 8px;">
                    Checkout (F4)
                </button>
                <button id="clearCartBtn" class="btn btn-secondary btn-block" style="font-size: 12px;">
                    Clear (F8)
                </button>
            </div>
        </div>

        <!-- Shortcuts Help -->
        <div style="background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 8px; padding: 12px; font-size: 11px;">
            <strong>Shortcuts:</strong>
            <div>F2 - Focus Search</div>
            <div>F4 - Checkout</div>
            <div>F8 - Clear Cart</div>
        </div>
    </div>
</div>

<style>
    #searchResults {
        background: white;
        border-top: 1px solid #E2E8F0;
    }

    .search-result {
        padding: 12px;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .search-result:hover {
        background: #F8FAFC;
    }

    .search-empty {
        padding: 20px;
        text-align: center;
        color: #94A3B8;
        font-size: 12px;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: 600;
        font-size: 13px;
    }

    .product-price, .product-price-unit, .product-stock {
        font-size: 11px;
        color: #64748B;
    }

    .add-to-cart-btn {
        padding: 4px 8px;
        font-size: 11px;
    }

    .cart-item {
        padding: 12px 0;
        border-bottom: 1px solid #E2E8F0;
        display: grid;
        grid-template-columns: 1fr 80px 60px;
        gap: 8px;
        align-items: center;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-weight: 600;
        font-size: 12px;
    }

    .cart-item-details {
        font-size: 11px;
        color: #64748B;
    }

    .cart-item-qty {
        display: flex;
        gap: 2px;
    }

    .qty-btn {
        flex: 1;
        padding: 4px;
        border: 1px solid #E2E8F0;
        background: white;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
    }

    .qty-input {
        flex: 1;
        padding: 4px;
        border: 1px solid #E2E8F0;
        border-radius: 4px;
        text-align: center;
        font-size: 11px;
    }

    .cart-item-total {
        font-weight: 600;
        font-size: 12px;
        text-align: right;
    }

    .remove-item {
        padding: 4px 8px;
        font-size: 10px;
    }
</style>

<script>
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = parseInt(this.dataset.id);
            
            // Find product from the full products list
            if (window.posSystem && window.posSystem.allProducts) {
                const product = window.posSystem.allProducts.find(p => p.id === productId);
                if (product) {
                    window.posSystem.addToCart(product);
                } else {
                    console.error('Product not found in list');
                    app.showNotification('Product not found', 'error');
                }
            }
        });
    });

    document.getElementById('discount').addEventListener('change', function() {
        if (window.posSystem) {
            window.posSystem.discount = parseFloat(this.value) || 0;
            window.posSystem.updateTotals();
        }
    });
</script>

<?php $content = ob_get_clean(); include APP_DIR . '/Views/layouts/main.php'; ?>
