/**
 * POS JavaScript
 * Point of Sale functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cart array
    let cart = [];
    let selectedPaymentMethod = 'cash';
    
    // DOM Elements
    const posSearch = document.getElementById('posSearch');
    const posProducts = document.getElementById('posProducts');
    const posCategories = document.getElementById('posCategories');
    const cartItems = document.getElementById('cartItems');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const cartDiscount = document.getElementById('cartDiscount');
    const cartTax = document.getElementById('cartTax');
    const cartTotal = document.getElementById('cartTotal');
    const discountInput = document.getElementById('discountInput');
    const btnCheckout = document.getElementById('btnCheckout');
    const btnCancel = document.getElementById('btnCancel');
    const clearCart = document.getElementById('clearCart');
    const paymentModal = document.getElementById('paymentModal');
    const invoiceModal = document.getElementById('invoiceModal');
    
    // Search functionality
    if (posSearch) {
        posSearch.addEventListener('input', debounce(function() {
            const search = this.value.trim();
            const category = document.querySelector('.pill.active')?.dataset.category || '';
            
                fetch(`index.php?page=pos&action=search&search=${encodeURIComponent(search)}&category_id=${category}`)
                .then(response => response.text())
                .then(html => {
                    console.log('POS search response length:', html.length);
                    posProducts.innerHTML = html;
                    attachProductClickHandlers();
                    console.log('Injected product cards count:', document.querySelectorAll('#posProducts .product-card').length);
                })
                .catch(error => console.error('Error searching products:', error));
        }, 300));
        
        // Barcode scanner support (Enter key) and autocomplete suggestions
        posSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value.trim();
                if (!val) return;
                // If value is numeric, treat as barcode
                if (/^\d+$/.test(val)) {
                    searchByBarcode(val);
                    this.value = '';
                    return;
                }
                // Otherwise fetch JSON suggestions and show dropdown
                const category = document.querySelector('.pill.active')?.dataset.category || '';
                fetch(`index.php?page=pos&action=search&search=${encodeURIComponent(val)}&category_id=${category}&json=1`)
                    .then(res => res.json())
                    .then(list => {
                        showSearchSuggestions(list, this);
                    })
                    .catch(err => console.error('Error fetching suggestions:', err));
            }
        });

        function showSearchSuggestions(list, input) {
            let existing = document.getElementById('posSuggestions');
            if (existing) existing.remove();
            const container = document.createElement('div');
            container.id = 'posSuggestions';
            container.style.position = 'absolute';
            container.style.zIndex = '9999';
            container.style.background = '#fff';
            container.style.border = '1px solid #ddd';
            container.style.maxHeight = '240px';
            container.style.overflow = 'auto';
            container.style.width = input.offsetWidth + 'px';
            const rect = input.getBoundingClientRect();
            container.style.left = (rect.left + window.scrollX) + 'px';
            container.style.top = (rect.bottom + window.scrollY) + 'px';

            list.forEach(p => {
                const item = document.createElement('div');
                item.className = 'pos-suggestion-item';
                item.style.padding = '8px';
                item.style.cursor = 'pointer';
                item.textContent = `${p.name} (${p.barcode})`;
                item.addEventListener('click', function() {
                    addToCart(p.id, p.name, parseFloat(p.price), p.quantity, p.barcode, p.image);
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
    }
    
    // Category filter
    if (posCategories) {
        posCategories.addEventListener('click', function(e) {
            if (e.target.classList.contains('pill')) {
                // Remove active class from all pills
                document.querySelectorAll('.pill').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked pill
                e.target.classList.add('active');
                
                const category = e.target.dataset.category;
                const search = posSearch?.value.trim() || '';
                
                fetch(`index.php?page=pos&action=search&search=${encodeURIComponent(search)}&category_id=${category}`)
                    .then(response => response.text())
                    .then(html => {
                        posProducts.innerHTML = html;
                        attachProductClickHandlers();
                    })
                    .catch(error => console.error('Error filtering products:', error));
            }
        });
    }
    
    // Product click handlers
    attachProductClickHandlers();
    
    function attachProductClickHandlers() {
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                const productId = parseInt(this.dataset.id);
                const productName = this.dataset.name;
                const productPrice = parseFloat(this.dataset.price);
                const productQuantity = parseInt(this.dataset.quantity);
                const productBarcode = this.dataset.barcode;
                const productImage = this.dataset.image;
                
                if (productQuantity <= 0) {
                    showNotification('Produit en rupture de stock', 'error');
                    return;
                }
                
                addToCart(productId, productName, productPrice, productQuantity, productBarcode, productImage);
            });
        });
    }
    
    // Search by barcode
    function searchByBarcode(barcode) {
        fetch(`index.php?page=pos&action=getProductByBarcode&barcode=${encodeURIComponent(barcode)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.product;
                    if (product.quantity <= 0) {
                        showNotification('Produit en rupture de stock', 'error');
                        return;
                    }
                    addToCart(product.id, product.name, product.price, product.quantity, product.barcode, product.image);
                } else {
                    showNotification('Produit introuvable', 'error');
                }
            })
            .catch(error => console.error('Error searching by barcode:', error));
    }
    
    // Add to cart
    function addToCart(id, name, price, availableQty, barcode, image) {
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            if (existingItem.quantity >= availableQty) {
                showNotification('Stock maximum atteint', 'warning');
                return;
            }
            existingItem.quantity++;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                quantity: 1,
                availableQty: availableQty,
                barcode: barcode,
                image: image
            });
        }
        
        updateCart();
        showNotification('Produit ajouté au panier', 'success');
    }
    
    // Update cart display
    function updateCart() {
        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    <i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: 10px;"></i>
                    <p>Le panier est vide</p>
                </div>
            `;
        } else {
            cartItems.innerHTML = cart.map((item, index) => `
                <div class="cart-item">
                    <div class="cart-item-image">
                        ${item.image ? `<img src="uploads/${item.image}" alt="">` : '<i class="fas fa-box"></i>'}
                    </div>
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${currency}${item.price.toFixed(2)}</div>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                        <span class="qty-value">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <div class="cart-item-total">${currency}${(item.price * item.quantity).toFixed(2)}</div>
                    <button class="cart-item-remove" onclick="removeFromCart(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
        }
        
        updateTotals();
    }
    
    // Update quantity
    window.updateQuantity = function(index, change) {
        const item = cart[index];
        const newQuantity = item.quantity + change;
        
        if (newQuantity <= 0) {
            removeFromCart(index);
            return;
        }
        
        if (newQuantity > item.availableQty) {
                showNotification('Stock maximum atteint', 'warning');
        }
        
        item.quantity = newQuantity;
        updateCart();
    };
    
    // Remove from cart
    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCart();
    };
    
    // Update totals
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
    
    // Discount input
    if (discountInput) {
        discountInput.addEventListener('input', updateTotals);
    }
    
    // Clear cart
    if (clearCart) {
        clearCart.addEventListener('click', function() {
            if (cart.length > 0 && confirm('Vider le panier ?')) {
                cart = [];
                updateCart();
                if (discountInput) discountInput.value = '';
            }
        });
    }
    
    // Cancel button
    if (btnCancel) {
        btnCancel.addEventListener('click', function() {
            if (cart.length > 0 && confirm('Annuler la vente ?')) {
                cart = [];
                updateCart();
                if (discountInput) discountInput.value = '';
            }
        });
    }
    
    // Checkout button
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function() {
            if (cart.length === 0) {
                showNotification('Le panier est vide', 'error');
                return;
            }
            
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discount = safeParseFloat(discountInput?.value || 0);
            const taxableAmount = subtotal - discount;
            const tax = taxableAmount * (taxPercentage / 100);
            const total = taxableAmount + tax;
            
            document.getElementById('amountToPay').textContent = `${currency}${total.toFixed(2)}`;
            document.getElementById('amountReceived').value = total.toFixed(2);
            document.getElementById('changeAmount').textContent = `${currency}0.00`;
            
            openModal('paymentModal');
        });
    }
    
    // Payment method selection
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
            this.classList.add('selected');
            selectedPaymentMethod = this.dataset.method;
        });
    });
    
    // Amount received calculation
    const amountReceived = document.getElementById('amountReceived');
    if (amountReceived) {
        amountReceived.addEventListener('input', function() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const discount = safeParseFloat(discountInput?.value || 0);
            const taxableAmount = subtotal - discount;
            const tax = taxableAmount * (taxPercentage / 100);
            const total = taxableAmount + tax;
            const received = safeParseFloat(this.value);
            const change = received - total;
            
            document.getElementById('changeAmount').textContent = `${currency}${change.toFixed(2)}`;
        });
    }
    
    // Close payment modal
    document.getElementById('closePaymentModal')?.addEventListener('click', () => closeModal('paymentModal'));
    document.getElementById('cancelPayment')?.addEventListener('click', () => closeModal('paymentModal'));
    
    // Confirm payment
    document.getElementById('confirmPayment')?.addEventListener('click', function() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = safeParseFloat(discountInput?.value || 0);
        const taxableAmount = subtotal - discount;
        const tax = taxableAmount * (taxPercentage / 100);
        const total = taxableAmount + tax;
        const amountReceived = safeParseFloat(document.getElementById('amountReceived').value);
        
        if (amountReceived < total) {
            showNotification('Montant insuffisant', 'error');
            return;
        }
        
        const data = {
            items: cart,
            discount: discount,
            amount_received: amountReceived,
            payment_method: selectedPaymentMethod,
            customer_name: document.getElementById('customerName').value,
            notes: document.getElementById('saleNotes').value
        };
        
        this.disabled = true;
        this.innerHTML = '<div class="loading"></div>';
        
        fetch('index.php?page=pos&action=completeSale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(Object.assign({
                csrf_token: getCSRFToken()
            }, data))
        })
        .then(response => response.text().then(text => ({ status: response.status, text })))
        .then(({ status, text }) => {
            console.log('POS raw response:', status, text);
            try {
                const result = JSON.parse(text);
                return result;
            } catch (error) {
                showNotification('Réponse serveur invalide : ' + text, 'error');
                throw new Error('Invalid JSON response');
            }
        })
        .then(result => {
            if (result.success) {
                closeModal('paymentModal');
                cart = [];
                updateCart();
                if (discountInput) discountInput.value = '';
                document.getElementById('customerName').value = '';
                document.getElementById('saleNotes').value = '';
                
                showNotification('Vente finalisée avec succès !', 'success');
                
                // Load and show invoice
                loadInvoice(result.sale_id);
            } else {
                showNotification(result.message || 'Échec de la finalisation de la vente', 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur lors de la finalisation de la vente', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-check"></i> Finaliser la vente';
        });
    });
    
    // Load invoice
    function loadInvoice(saleId) {
        fetch(`index.php?page=pos&action=printInvoice&id=${saleId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('invoiceContent').innerHTML = html;
                openModal('invoiceModal');
            })
            .catch(error => console.error('Error loading invoice:', error));
    }
    
    // Close invoice modal
    document.getElementById('closeInvoiceModal')?.addEventListener('click', () => closeModal('invoiceModal'));
    document.getElementById('closeInvoice')?.addEventListener('click', () => closeModal('invoiceModal'));
    
    // Print invoice
    document.getElementById('printInvoice')?.addEventListener('click', function() {
        window.print();
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // F2 - Focus search
        if (e.key === 'F2') {
            e.preventDefault();
            posSearch?.focus();
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
        
        // Escape - Close modals
        if (e.key === 'Escape') {
            closeModal('paymentModal');
            closeModal('invoiceModal');
        }
    });
});
