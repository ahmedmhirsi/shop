/**
 * Modern UI Utilities
 * Reusable JavaScript components for modern design
 */

class ModernUI {
    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - 'success', 'error', 'warning', 'info'
     * @param {number} duration - Duration in milliseconds (0 = persistent)
     */
    static showToast(message, type = 'info', duration = 3000) {
        const container = this._getToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        toast.innerHTML = `
            <div class="toast-icon">${icons[type]}</div>
            <div class="toast-content">
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close">✕</button>
        `;
        
        container.appendChild(toast);
        
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.remove();
        });
        
        if (duration > 0) {
            setTimeout(() => {
                toast.remove();
            }, duration);
        }
        
        return toast;
    }
    
    /**
     * Show a modal dialog
     * @param {string} title - Modal title
     * @param {string} content - Modal content (HTML)
     * @param {array} buttons - Array of button objects {label, class, callback}
     */
    static showModal(title, content, buttons = []) {
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay active';
        
        const dialog = document.createElement('div');
        dialog.className = 'modal-dialog';
        
        let buttonsHTML = '';
        buttons.forEach(btn => {
            buttonsHTML += `<button class="btn ${btn.class || 'btn-secondary'}">${btn.label}</button>`;
        });
        
        dialog.innerHTML = `
            <div class="modal-header">
                <h2 class="modal-title">${title}</h2>
                <button class="modal-close-btn">✕</button>
            </div>
            <div class="modal-content">${content}</div>
            ${buttonsHTML ? `<div class="modal-footer">${buttonsHTML}</div>` : ''}
        `;
        
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);
        
        // Close handlers
        const closeBtn = dialog.querySelector('.modal-close-btn');
        closeBtn.addEventListener('click', () => overlay.remove());
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.remove();
        });
        
        // Button handlers
        const buttonElements = dialog.querySelectorAll('button:not(.modal-close-btn)');
        buttonElements.forEach((btn, index) => {
            btn.addEventListener('click', () => {
                if (buttons[index]?.callback) {
                    buttons[index].callback();
                }
                overlay.remove();
            });
        });
        
        return overlay;
    }
    
    /**
     * Show a confirmation dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {function} onConfirm - Callback when confirmed
     * @param {function} onCancel - Callback when cancelled
     */
    static showConfirm(title, message, onConfirm, onCancel) {
        return this.showModal(title, 
            `
            <div class="alert-dialog-message">${message}</div>
            `,
            [
                { label: 'Annuler', class: 'btn-secondary', callback: onCancel },
                { label: 'Confirmer', class: 'btn-primary', callback: onConfirm }
            ]
        );
    }
    
    /**
     * Show an alert dialog
     * @param {string} title - Dialog title
     * @param {string} message - Dialog message
     * @param {string} type - 'success', 'error', 'warning', 'info'
     */
    static showAlert(title, message, type = 'info') {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        return this.showModal(title,
            `
            <div class="alert-dialog">
                <div class="alert-dialog-icon ${type}">${icons[type]}</div>
                <div class="alert-dialog-message">${message}</div>
            </div>
            `,
            [
                { label: 'OK', class: 'btn-primary' }
            ]
        );
    }
    
    /**
     * Initialize form validation
     * @param {HTMLFormElement} form - The form to validate
     * @param {object} rules - Validation rules
     */
    static initFormValidation(form, rules = {}) {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const formGroups = form.querySelectorAll('.form-group');
            
            formGroups.forEach(group => {
                const input = group.querySelector('input, textarea, select');
                if (!input) return;
                
                const fieldName = input.name;
                const value = input.value.trim();
                const rule = rules[fieldName];
                
                if (rule?.required && !value) {
                    this._setFieldError(group, 'Ce champ est requis');
                    isValid = false;
                } else if (rule?.type === 'email' && value && !this._isValidEmail(value)) {
                    this._setFieldError(group, 'Email invalide');
                    isValid = false;
                } else if (rule?.minLength && value.length < rule.minLength) {
                    this._setFieldError(group, `Minimum ${rule.minLength} caractères`);
                    isValid = false;
                } else if (rule?.maxLength && value.length > rule.maxLength) {
                    this._setFieldError(group, `Maximum ${rule.maxLength} caractères`);
                    isValid = false;
                } else if (rule?.pattern && value && !rule.pattern.test(value)) {
                    this._setFieldError(group, 'Format invalide');
                    isValid = false;
                } else {
                    this._setFieldSuccess(group);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
            
            return isValid;
        });
    }
    
    /**
     * Initialize table sorting
     * @param {HTMLTableElement} table - The table to make sortable
     */
    static initTableSorting(table) {
        const headers = table.querySelectorAll('thead th.sortable');
        
        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const isAsc = header.classList.contains('sort-asc');
                
                // Remove sort classes from all headers
                headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
                
                // Add sort class to clicked header
                header.classList.toggle('sort-asc', !isAsc);
                header.classList.toggle('sort-desc', isAsc);
                
                // Sort rows
                rows.sort((a, b) => {
                    const aVal = a.cells[index].textContent.trim();
                    const bVal = b.cells[index].textContent.trim();
                    
                    if (isAsc) {
                        return bVal.localeCompare(aVal);
                    } else {
                        return aVal.localeCompare(bVal);
                    }
                });
                
                // Re-append sorted rows
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    }
    
    /**
     * Initialize pagination
     * @param {object} options - Configuration object
     */
    static initPagination(options) {
        const {
            table,
            itemsPerPage = 10,
            onPageChange = null
        } = options;
        
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const totalPages = Math.ceil(rows.length / itemsPerPage);
        let currentPage = 1;
        
        const showPage = (page) => {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            
            rows.forEach((row, index) => {
                row.style.display = index >= start && index < end ? '' : 'none';
            });
            
            if (onPageChange) onPageChange(page, totalPages);
        };
        
        return {
            goToPage: (page) => {
                currentPage = Math.max(1, Math.min(page, totalPages));
                showPage(currentPage);
            },
            nextPage: () => {
                this.goToPage(currentPage + 1);
            },
            prevPage: () => {
                this.goToPage(currentPage - 1);
            },
            getTotalPages: () => totalPages,
            getCurrentPage: () => currentPage
        };
    }
    
    /**
     * Initialize dropdown menu
     * @param {string} triggerId - ID of trigger element
     * @param {string} menuId - ID of menu element
     */
    static initDropdown(triggerId, menuId) {
        const trigger = document.getElementById(triggerId);
        const menu = document.getElementById(menuId);
        
        if (!trigger || !menu) return;
        
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('active');
        });
        
        document.addEventListener('click', () => {
            menu.classList.remove('active');
        });
        
        menu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', () => {
                menu.classList.remove('active');
            });
        });
    }
    
    /**
     * Format currency
     * @param {number} amount - Amount to format
     * @param {string} currency - Currency code (default: EUR)
     */
    static formatCurrency(amount, currency = 'EUR') {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }
    
    /**
     * Format date
     * @param {Date} date - Date to format
     * @param {string} format - Format string
     */
    static formatDate(date, format = 'DD/MM/YYYY') {
        const options = {
            'DD/MM/YYYY': { day: '2-digit', month: '2-digit', year: 'numeric' },
            'DD MMM YYYY': { day: '2-digit', month: 'short', year: 'numeric' },
            'YYYY-MM-DD': { year: 'numeric', month: '2-digit', day: '2-digit' }
        };
        
        return new Intl.DateTimeFormat('fr-FR', options[format] || options['DD/MM/YYYY']).format(date);
    }
    
    // ========== PRIVATE METHODS ==========
    
    static _getToastContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }
    
    static _setFieldError(group, message) {
        group.classList.remove('success');
        group.classList.add('error');
        
        let errorDiv = group.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            group.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    static _setFieldSuccess(group) {
        group.classList.remove('error');
        group.classList.add('success');
        
        const errorDiv = group.querySelector('.invalid-feedback');
        if (errorDiv) errorDiv.remove();
    }
    
    static _isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModernUI;
}
