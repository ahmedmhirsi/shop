/* Stock & Sales Management System v1.0.0 - Core JS */

class App {
    constructor() {
        this.baseUrl = '/shop_v2';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupShortcuts();
    }

    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initMenuActive();
        });
    }

    setupShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (e.altKey || e.ctrlKey || e.metaKey) {
                switch (e.key.toLowerCase()) {
                    case 'p':
                        if (document.querySelector('[data-page="pos"]')) {
                            window.location.href = this.baseUrl + '/index.php?url=pos';
                        }
                        break;
                }
            }
        });
    }

    initMenuActive() {
        const currentUrl = window.location.search.split('url=')[1]?.split('/')[0];
        if (currentUrl) {
            document.querySelectorAll('.sidebar-menu-link').forEach(link => {
                link.classList.remove('active');
                if (link.href.includes('url=' + currentUrl)) {
                    link.classList.add('active');
                }
            });
        }
    }

    async fetch(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    ...options.headers
                }
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Request failed');
            }
            return data;
        } catch (error) {
            console.error('Fetch error:', error);
            this.showNotification('Error: ' + error.message, 'error');
            throw error;
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <p>${this.escapeHtml(message)}</p>
                <button class="notification-close">&times;</button>
            </div>
        `;

        document.body.appendChild(notification);

        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount) + ' TND';
    }
}

const app = new App();

/* POS System */

class POSSystem {
    constructor() {
        this.cart = [];
        this.currentMode = 'pack';
        this.discount = 0;
        this.paymentMethod = 'cash';
        this.amountReceived = 0;
        this.csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
        this.allProducts = [];
        this.init();
    }

    init() {
        this.bindElements();
        this.loadProducts();
        this.attachEventListeners();
        this.loadCart();
    }

    loadProducts() {
        const productRows = document.querySelectorAll('table tbody tr');
        productRows.forEach(row => {
            const button = row.querySelector('button[data-id]');
            if (!button) return;

            const id = parseInt(button.dataset.id || 0);
            if (!id) return;

            const cells = row.querySelectorAll('td');
            this.allProducts.push({
                id: id,
                name: cells[0]?.textContent.trim() || '',
                quantity: parseFloat(cells[1]?.textContent.replace(/[^\\d.-]/g, '') ) || 0,
                selling_price: parseFloat(cells[2]?.textContent.replace(/[^\\d.-]/g, '') ) || 0,
                buying_price: parseFloat(cells[2]?.textContent.replace(/[^\\d.-]/g, '') ) * 0.7 || 0,
                barcode: button.dataset.barcode || '',
                cigarette_price: parseFloat(button.dataset.cigarettePrice || 0),
                cigarettes_per_pack: parseInt(button.dataset.cigarettesPerPack || 20, 10)
            });
        });
    }

    bindElements() {
        this.searchInput = document.getElementById('productSearch');
        this.resultsContainer = document.getElementById('searchResults');
        this.cartItems = document.getElementById('cartItems');
        this.cartTotal = document.getElementById('cartTotal');
        this.subtotalEl = document.getElementById('subtotal');
        this.taxEl = document.getElementById('tax');
        this.discountEl = document.getElementById('discount');
        this.changeEl = document.getElementById('change');
        this.modeToggle = document.getElementById('modeToggle');
        this.modeLabel = document.getElementById('modeLabel');
        this.amountReceivedInput = document.getElementById('amountReceived');
        this.paymentMethodSelect = document.getElementById('paymentMethod');
        this.checkoutBtn = document.getElementById('checkoutBtn');
        this.clearCartBtn = document.getElementById('clearCartBtn');
        this.customerSelect = document.getElementById('customer');
        this.notesInput = document.getElementById('notes');
    }

    attachEventListeners() {
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => this.handleSearch(e));
        }
        if (this.modeToggle) {
            this.modeToggle.addEventListener('change', () => this.toggleMode());
        }
        if (this.amountReceivedInput) {
            this.amountReceivedInput.addEventListener('input', () => this.updateChange());
        }
        if (this.checkoutBtn) {
            this.checkoutBtn.addEventListener('click', () => this.checkout());
        }
        if (this.clearCartBtn) {
            this.clearCartBtn.addEventListener('click', () => this.clearCart());
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') {
                e.preventDefault();
                if (this.searchInput) this.searchInput.focus();
            }
            if (e.key === 'F4') {
                e.preventDefault();
                if (this.checkoutBtn) this.checkout();
            }
            if (e.key === 'F8') {
                e.preventDefault();
                this.clearCart();
            }
        });
    }

    async handleSearch(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            this.resultsContainer.innerHTML = '';
            return;
        }

        try {
            const formData = new FormData();
            formData.append('query', query);
            formData.append('csrf_token', this.csrfToken);

            const response = await fetch(app.baseUrl + '/index.php?url=pos/searchProducts', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            this.displaySearchResults(data.products || []);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displaySearchResults(products) {
        if (products.length === 0) {
            this.resultsContainer.innerHTML = '<div class="search-empty">No products found</div>';
            return;
        }

        const html = products.map(product => `
            <div class="search-result" data-id="${product.id}">
                <div class="product-info">
                    <div class="product-name">${this.escapeHtml(product.name)}</div>
                    <div class="product-price">Pack: ${app.formatCurrency(product.selling_price)}</div>
                    ${product.cigarette_price > 0 ? `<div class="product-price-unit">Unit: ${app.formatCurrency(product.cigarette_price)}</div>` : ''}
                    <div class="product-stock">Stock: ${product.quantity}</div>
                </div>
                <button class="btn btn-sm btn-primary add-to-cart-btn" data-id="${product.id}">Add</button>
            </div>
        `).join('');

        this.resultsContainer.innerHTML = html;

        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = parseInt(btn.dataset.id);
                const product = products.find(p => p.id === productId);
                this.addToCart(product);
                this.searchInput.value = '';
                this.resultsContainer.innerHTML = '';
                this.searchInput.focus();
            });
        });
    }

    addToCart(product) {
        const quantity = 1;
        const unitType = this.currentMode === 'cigarette' ? 'cigarette' : 'pack';
        const sellingPrice = unitType === 'cigarette' ? product.cigarette_price : product.selling_price;
        const buyingPrice = product.buying_price;

        const existingItem = this.cart.find(item => 
            item.product_id === product.id && item.unit_type === unitType
        );

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                product_id: product.id,
                name: product.name,
                barcode: product.barcode,
                quantity: quantity,
                unit_type: unitType,
                selling_price: sellingPrice,
                buying_price: buyingPrice,
                stock: product.quantity,
                cigarettes_per_pack: product.cigarettes_per_pack || 20
            });
        }

        this.saveCart();
        this.updateCartUI();
        app.showNotification(`${product.name} added to cart`, 'success');
    }

    updateCartUI() {
        if (!this.cartItems) return;

        this.cartItems.innerHTML = this.cart.map((item, index) => {
            const qtyValue = item.unit_type === 'cigarette'
                ? this.formatTobaccoDisplayQuantity(item.quantity, item.cigarettes_per_pack || 20)
                : item.quantity;

            return `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${this.escapeHtml(item.name)}</div>
                    <div class="cart-item-details">
                        <span>${item.unit_type === 'cigarette' ? 'Cig.' : 'Pack'}</span>
                        <span class="cart-item-price">${app.formatCurrency(item.selling_price)}</span>
                    </div>
                </div>
                <div class="cart-item-qty">
                    <button class="qty-btn" data-index="${index}" data-action="minus">−</button>
                    <input type="number" value="${qtyValue}" data-index="${index}" class="qty-input" min="${item.unit_type === 'cigarette' ? '0.01' : '1'}" step="${item.unit_type === 'cigarette' ? '0.01' : '1'}">
                    <button class="qty-btn" data-index="${index}" data-action="plus">+</button>
                </div>
                <div class="cart-item-total">${app.formatCurrency(item.selling_price * item.quantity)}</div>
                <button class="btn btn-sm btn-danger remove-item" data-index="${index}">✕</button>
            </div>
        `;
        }).join('');

        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(btn.dataset.index);
                const action = btn.dataset.action;
                if (action === 'plus') {
                    this.cart[index].quantity += 1;
                } else if (action === 'minus' && this.cart[index].quantity > 1) {
                    this.cart[index].quantity -= 1;
                }
                this.saveCart();
                this.updateCartUI();
                this.updateTotals();
            });
        });

        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const index = parseInt(input.dataset.index);
                const item = this.cart[index];
                const qty = item.unit_type === 'cigarette'
                    ? Math.max(1, this.parseTobaccoQuantityInput(input.value, item.cigarettes_per_pack || 20))
                    : Math.max(1, parseInt(input.value) || 1);

                this.cart[index].quantity = qty;
                this.saveCart();
                this.updateCartUI();
                this.updateTotals();
            });
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const index = parseInt(btn.dataset.index);
                this.cart.splice(index, 1);
                this.saveCart();
                this.updateCartUI();
                this.updateTotals();
            });
        });

        this.updateTotals();
    }

    updateTotals() {
        let subtotal = 0;
        this.cart.forEach(item => {
            subtotal += item.selling_price * item.quantity;
        });

        const tax = subtotal * (0 / 100);
        const total = subtotal + tax - this.discount;

        if (this.subtotalEl) this.subtotalEl.textContent = app.formatCurrency(subtotal);
        if (this.taxEl) this.taxEl.textContent = app.formatCurrency(tax);
        if (this.cartTotal) this.cartTotal.textContent = app.formatCurrency(total);

        this.updateChange();
    }

    formatTobaccoDisplayQuantity(quantity, perPack = 20) {
        const packs = Math.floor(quantity / perPack);
        const cigarettes = Math.round(quantity % perPack);
        const decimalPart = cigarettes.toString().padStart(2, '0');
        return `${packs}.${decimalPart}`;
    }

    parseTobaccoQuantityInput(value, perPack = 20) {
        if (!value) {
            return 1;
        }

        const cleaned = value.toString().trim().replace(',', '.');
        const parts = cleaned.split('.');
        const packCount = parseInt(parts[0], 10) || 0;

        if (parts.length === 2) {
            let remainder = parseInt(parts[1].slice(0, 2), 10);
            remainder = isNaN(remainder) ? 0 : remainder;
            if (remainder > perPack) {
                remainder = perPack;
            }
            return packCount * perPack + remainder;
        }

        const numericValue = parseInt(cleaned, 10) || 0;
        return numericValue;
    }

    updateChange() {
        if (!this.amountReceivedInput || !this.changeEl) return;

        let subtotal = 0;
        this.cart.forEach(item => {
            subtotal += item.selling_price * item.quantity;
        });

        const tax = subtotal * (0 / 100);
        const total = subtotal + tax - this.discount;
        const amountReceived = parseFloat(this.amountReceivedInput.value) || 0;
        const change = Math.max(0, amountReceived - total);

        this.changeEl.textContent = app.formatCurrency(change);
    }

    toggleMode() {
        this.currentMode = this.currentMode === 'pack' ? 'cigarette' : 'pack';
        if (this.modeLabel) {
            this.modeLabel.textContent = this.currentMode === 'pack' ? 'Mode Paquet' : 'Mode Cigarette';
        }
    }

    async checkout() {
        if (this.cart.length === 0) {
            app.showNotification('Cart is empty', 'warning');
            return;
        }

        const subtotal = this.cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0);
        const total = subtotal - this.discount;
        const amountReceived = parseFloat(this.amountReceivedInput?.value) || 0;

        if (amountReceived < total && this.paymentMethodSelect?.value === 'cash') {
            app.showNotification('Insufficient payment', 'error');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('csrf_token', this.csrfToken);
            formData.append('items', JSON.stringify(this.cart));
            formData.append('customer_id', this.customerSelect?.value || '');
            formData.append('discount', this.discount);
            formData.append('payment_method', this.paymentMethodSelect?.value || 'cash');
            formData.append('amount_received', amountReceived);
            formData.append('notes', this.notesInput?.value || '');

            const response = await fetch(app.baseUrl + '/index.php?url=pos/checkout', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            
            // Check if response contains error
            if (data.error) {
                app.showNotification('Error: ' + data.error, 'error');
                console.error('Checkout error:', data.error);
                return;
            }
            
            if (data.success) {
                app.showNotification('Sale completed successfully', 'success');
                this.clearCart();
                // Corrected URL with & instead of ?
                setTimeout(() => {
                    window.location.href = app.baseUrl + '/index.php?url=pos/printReceipt&id=' + data.sale_id;
                }, 1500);
            } else {
                app.showNotification('Checkout failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Checkout error:', error);
            app.showNotification('Error: ' + error.message, 'error');
        }
    }

    clearCart() {
        if (confirm('Clear cart?')) {
            this.cart = [];
            this.discount = 0;
            this.saveCart();
            this.updateCartUI();
            if (this.searchInput) this.searchInput.focus();
        }
    }

    saveCart() {
        localStorage.setItem('pos_cart', JSON.stringify(this.cart));
    }

    loadCart() {
        const saved = localStorage.getItem('pos_cart');
        if (saved) {
            try {
                this.cart = JSON.parse(saved).map(item => ({
                    ...item,
                    cigarettes_per_pack: item.cigarettes_per_pack || 20
                }));
            } catch (error) {
                this.cart = [];
            }
            this.updateCartUI();
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

/* Initialize on page load */
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('posContainer')) {
        window.posSystem = new POSSystem();
    }
});

/* Notification Styles (injected) */
const style = document.createElement('style');
style.innerHTML = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        z-index: 2000;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .notification-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        gap: 12px;
    }

    .notification-success {
        border-left: 4px solid #10B981;
    }

    .notification-success .notification-content {
        color: #10B981;
    }

    .notification-error {
        border-left: 4px solid #EF4444;
    }

    .notification-error .notification-content {
        color: #EF4444;
    }

    .notification-warning {
        border-left: 4px solid #F59E0B;
    }

    .notification-warning .notification-content {
        color: #F59E0B;
    }

    .notification-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: inherit;
        opacity: 0.5;
    }

    .notification-close:hover {
        opacity: 1;
    }

    /* Search Results */
    .search-result {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #E2E8F0;
        hover-background: #F8FAFC;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-weight: 600;
        color: #0F172A;
    }

    .product-price, .product-price-unit, .product-stock {
        font-size: 12px;
        color: #64748B;
    }

    /* Cart Item */
    .cart-item {
        display: grid;
        grid-template-columns: 1fr 120px 100px 40px;
        gap: 12px;
        padding: 12px;
        border-bottom: 1px solid #E2E8F0;
        align-items: center;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-weight: 600;
        color: #0F172A;
    }

    .cart-item-details {
        font-size: 12px;
        color: #64748B;
    }

    .cart-item-qty {
        display: flex;
        gap: 4px;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        padding: 0;
        border: 1px solid #E2E8F0;
        border-radius: 4px;
        background: white;
        cursor: pointer;
    }

    .qty-input {
        width: 40px;
        padding: 4px;
        border: 1px solid #E2E8F0;
        border-radius: 4px;
        text-align: center;
    }

    .cart-item-total {
        font-weight: 600;
        text-align: right;
    }
`;
document.head.appendChild(style);

