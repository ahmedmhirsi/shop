/* ================================================================
   DASHBOARD JAVASCRIPT - Chart.js & Interactive Features
   Stock & Sales Management System
   ================================================================ */

class DashboardManager {
    constructor() {
        this.charts = {};
        this.currentPeriod = '7d';
        this.periodOptions = {
            '7d': 7,
            '30d': 30,
            '90d': 90
        };
        this.baseUrl = '/shop_v2';
        this.init();
    }

    init() {
        this.updateDateTime();
        this.initializeCharts();
        this.attachEventListeners();
        this.loadDashboardData();
        
        // Update date/time every minute
        setInterval(() => this.updateDateTime(), 60000);
        
        // Auto-refresh dashboard every 5 minutes
        setInterval(() => this.loadDashboardData(), 300000);
    }

    updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        const dtEl = document.getElementById('dateTime');
        if (dtEl) {
            dtEl.innerHTML = `<i class="fas fa-calendar"></i> ${now.toLocaleDateString('fr-FR', options)}`;
        }
    }

    loadDashboardData() {
        const today = new Date().toISOString().split('T')[0];
        const startDate = this.calculateStartDate();
        const url = `${this.baseUrl}/index.php?url=dashboard/getStats&start_date=${startDate}&end_date=${today}`;

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('API Error: ' + response.status);
                return response.json();
            })
            .then(data => {
                window.dashboardData = data;
                this.updateKPICards();
                this.updateShiftPerformance();
                this.updateLowStockTable();
                this.updateRecentTransactions();
                this.updateTopProducts();
                this.updateTobaccoInsights();
                this.updateCharts();
            })
            .catch(error => {
                console.error('Dashboard load error:', error);
                const caEl = document.getElementById('caValue');
                if (caEl) caEl.textContent = '0.00 TND';
            });
    }

    calculateStartDate() {
        const days = this.periodOptions[this.currentPeriod] || 7;
        const date = new Date();
        date.setDate(date.getDate() - (days - 1));
        return date.toISOString().split('T')[0];
    }

    refreshCharts() {
        this.updateRevenueChart();
        this.updateCategoryChart();
    }

    updateKPICards() {
        const data = window.dashboardData || {};
        const stats = data.stats || {};
        const profitStats = data.profitStats || {};

        const revenue = parseFloat(stats.total_revenue || 0);
        const profit = parseFloat(profitStats.total_profit || 0);
        const sales = parseInt(stats.total_sales || 0);
        const lowStock = (data.lowStockProducts || []).length;

        // Update CA (Chiffre d'Affaires)
        const caEl = document.getElementById('caValue');
        if (caEl) caEl.textContent = this.formatCurrency(revenue);

        // Update Profit Net
        const profitEl = document.getElementById('profitValue');
        if (profitEl) profitEl.textContent = this.formatCurrency(profit);

        // Update Transaction Count
        const txCountEl = document.getElementById('transactionCountText');
        if (txCountEl) txCountEl.textContent = `${sales} Transactions`;
        const txBadgeEl = document.getElementById('transactionCount');
        if (txBadgeEl) txBadgeEl.textContent = sales;

        // Update Avg Ticket
        const avgTicketEl = document.getElementById('avgTicketValue');
        if (avgTicketEl) {
            avgTicketEl.textContent = sales > 0 ? this.formatCurrency(revenue / sales) : '0.00 TND';
        }

        // Update Low Stock
        const lowStockEl = document.getElementById('lowStockCount');
        if (lowStockEl) lowStockEl.textContent = lowStock;

        const lowStockBadgeEl = document.getElementById('lowStockBadge');
        if (lowStockBadgeEl) lowStockBadgeEl.textContent = `🚨 ${lowStock}`;

        // Update Profit Margin
        const profitMargin = revenue > 0 ? ((profit / revenue) * 100).toFixed(1) : '0';
        const profitMarginEl = document.getElementById('profitMargin');
        if (profitMarginEl) profitMarginEl.textContent = profitMargin + '%';
    }

    updateShiftPerformance() {
        const data = window.dashboardData || {};
        const stats = data.stats || {};
        const revenue = parseFloat(stats.total_revenue || 0);
        const totalSales = parseInt(stats.total_sales || 0);

        const shifts = [
            { id: 1, hours: '07:00 - 16:00', revenue: revenue * 0.4, sales: Math.floor(totalSales * 0.4), employee: 'Magasin Boss' },
            { id: 2, hours: '16:00 - 22:00', revenue: revenue * 0.35, sales: Math.floor(totalSales * 0.35), employee: 'Employé POS' },
            { id: 3, hours: '22:00 - 07:00', revenue: revenue * 0.25, sales: Math.floor(totalSales * 0.25), employee: '--' }
        ];

        shifts.forEach(shift => {
            const revEl = document.getElementById(`shift${shift.id}Revenue`);
            const salesEl = document.getElementById(`shift${shift.id}Sales`);
            const empEl = document.getElementById(`shift${shift.id}Employee`);
            
            if (revEl) revEl.textContent = this.formatCurrency(shift.revenue);
            if (salesEl) salesEl.textContent = shift.sales;
            if (empEl) empEl.textContent = shift.employee;
        });
    }

    updateLowStockTable() {
        const data = window.dashboardData || {};
        const lowStockProducts = data.lowStockProducts || [];
        const tbody = document.getElementById('lowStockTableBody');

        if (!tbody) return;

        if (lowStockProducts.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748B;">
                        Aucun article en stock critique ✓
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = lowStockProducts.map(product => `
            <tr>
                <td><strong>${this.escapeHtml(product.name)}</strong></td>
                <td>${this.escapeHtml(product.category_name || 'N/A')}</td>
                <td>${product.quantity}</td>
                <td>${product.minimum_stock}</td>
                <td>
                    ${product.quantity === 0 ? 
                        '<span class="stock-level-critical">🔴 OUT OF STOCK</span>' : 
                        '<span class="stock-level-warning">⚠️ CRITICAL</span>'
                    }
                </td>
                <td>
                    <a href="${this.baseUrl}/index.php?url=products/edit/${product.id}" class="btn btn-sm" style="font-size: 11px; padding: 4px 8px;">
                        Réapprovisionner
                    </a>
                </td>
            </tr>
        `).join('');
    }

    updateRecentTransactions() {
        const data = window.dashboardData || {};
        const recentSales = data.recentSales || [];
        const container = document.getElementById('recentTransactionsContainer');

        if (!container) return;

        if (recentSales.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #94A3B8;">
                    <i class="fas fa-inbox" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                    Aucune vente aujourd'hui
                </div>
            `;
            return;
        }

        container.innerHTML = recentSales.slice(0, 10).map(sale => `
            <div class="transaction-item">
                <div class="transaction-info">
                    <div class="transaction-invoice">${this.escapeHtml(sale.invoice_number)}</div>
                    <div class="transaction-meta">
                        ${this.escapeHtml(sale.customer_name || 'Comptoir')} • ${sale.payment_method === 'card' ? '💳 Carte' : '💵 Espèces'}
                    </div>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span class="transaction-amount">${this.formatCurrency(sale.total)}</span>
                    <a href="${this.baseUrl}/index.php?url=pos/printReceipt&id=${sale.id}" class="btn btn-secondary btn-xs">Reçu</a>
                </div>
            </div>
        `).join('');
    }

    updateTopProducts() {
        const data = window.dashboardData || {};
        const topProducts = data.topProducts || [];
        const container = document.getElementById('topProductsContainer');

        if (!container) return;

        if (topProducts.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #94A3B8;">
                    <i class="fas fa-box-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                    Pas assez de données
                </div>
            `;
            return;
        }

        container.innerHTML = topProducts.slice(0, 5).map((product, index) => `
            <div class="product-item">
                <div class="product-rank">#${index + 1}</div>
                <div class="product-details">
                    <div class="product-name">${this.escapeHtml(product.name)}</div>
                    <div class="product-units">${product.total_qty} unités vendues</div>
                </div>
                <div class="product-revenue">${this.formatCurrency(product.revenue)}</div>
            </div>
        `).join('');
    }

    updateTobaccoInsights() {
        const data = window.dashboardData || {};
        const insights = data.tobaccoInsights || { pack: { quantity: 0, revenue: 0 }, cigarette: { quantity: 0, revenue: 0 } };
        const packsSoldEl = document.getElementById('packsSold');
        const cigarettesSoldEl = document.getElementById('cigarettesSold');
        const packsRevenueEl = document.getElementById('packsRevenue');
        const cigarettesRevenueEl = document.getElementById('cigarettesRevenue');
        const packMarginEl = document.getElementById('packMargin');
        const cigaretteMarginEl = document.getElementById('cigaretteMargin');

        if (packsSoldEl) packsSoldEl.textContent = (insights.pack.quantity || 0).toString();
        if (cigarettesSoldEl) cigarettesSoldEl.textContent = (insights.cigarette.quantity || 0).toString();
        if (packsRevenueEl) packsRevenueEl.textContent = this.formatCurrency(insights.pack.revenue || 0);
        if (cigarettesRevenueEl) cigarettesRevenueEl.textContent = this.formatCurrency(insights.cigarette.revenue || 0);

        const packMargin = insights.pack.revenue > 0 ? `${((insights.pack.profit || 0) / insights.pack.revenue * 100).toFixed(1)}%` : '0%';
        const cigaretteMargin = insights.cigarette.revenue > 0 ? `${((insights.cigarette.profit || 0) / insights.cigarette.revenue * 100).toFixed(1)}%` : '0%';

        if (packMarginEl) packMarginEl.textContent = packMargin;
        if (cigaretteMarginEl) cigaretteMarginEl.textContent = cigaretteMargin;
    }

    refreshCharts() {
        // Charts are updated in initializeCharts
    }

    initializeCharts() {
        this.initRevenueChart();
        this.initCategoryChart();
    }

    initRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const data = window.dashboardData || {};
        const trends = data.dailyTrends || [];
        const labels = this.buildTrendLabels(trends);
        const revenue = [];
        const profitData = [];
        const trendMap = {};

        trends.forEach(item => {
            if (item && item.date) {
                trendMap[item.date] = item;
            }
        });

        const days = this.periodOptions[this.currentPeriod] || 7;
        const today = new Date();
        for (let i = days - 1; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            const dateKey = date.toISOString().split('T')[0];
            const row = trendMap[dateKey] || { revenue: 0, profit: 0 };
            revenue.push(parseFloat(row.revenue || 0));
            profitData.push(parseFloat(row.profit || 0));
        }

        // Destroy existing chart if it exists
        if (this.charts.revenue) {
            this.charts.revenue.destroy();
        }

        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Chiffre d\'Affaires',
                        data: revenue,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#4F46E5',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#4338CA'
                    },
                    {
                        label: 'Profit Net',
                        data: profitData,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#059669'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12, family: "'Inter', sans-serif" },
                            color: '#64748B',
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        borderColor: '#475569',
                        borderWidth: 1,
                        callbacks: {
                            label: (context) => {
                                return context.dataset.label + ': ' + this.formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.5)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748B',
                            font: { size: 11 },
                            callback: (value) => this.formatChartValue(value)
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748B',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    initCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        const data = window.dashboardData || {};
        const categoryStats = data.categoryStats || [];

        // Prepare chart data
        const categories = categoryStats.length > 0 
            ? categoryStats.map(c => c.name || 'Other')
            : ['No Data'];
        const revenue = categoryStats.length > 0 
            ? categoryStats.map(c => parseFloat(c.revenue || 0))
            : [0];

        const colors = [
            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#14B8A6', '#6366F1', '#D946EF', '#0891B2'
        ];

        // Destroy existing chart if it exists
        if (this.charts.category) {
            this.charts.category.destroy();
        }

        this.charts.category = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: revenue,
                    backgroundColor: colors.slice(0, categories.length),
                    borderColor: '#FFFFFF',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748B',
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleColor: '#FFFFFF',
                        bodyColor: '#E2E8F0',
                        borderColor: '#475569',
                        borderWidth: 1,
                        callbacks: {
                            label: (context) => {
                                return this.formatCurrency(context.parsed);
                            }
                        }
                    }
                }
            }
        });
    }

    attachEventListeners() {
        const buttons = document.querySelectorAll('.chart-period-btn');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                this.currentPeriod = button.dataset.period;
                this.loadDashboardData();
            });
        });
    }

    updateCharts() {
        this.updateRevenueChart();
        this.updateCategoryChart();
    }

    updateRevenueChart() {
        const data = window.dashboardData || {};
        const trends = data.dailyTrends || [];
        const labels = this.buildTrendLabels(trends);
        const revenue = [];
        const profit = [];
        const trendMap = {};

        trends.forEach(item => {
            if (item && item.date) {
                trendMap[item.date] = item;
            }
        });

        const days = this.periodOptions[this.currentPeriod] || 7;
        const today = new Date();
        for (let i = days - 1; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(today.getDate() - i);
            const dateKey = date.toISOString().split('T')[0];
            const row = trendMap[dateKey] || { revenue: 0, profit: 0 };
            revenue.push(parseFloat(row.revenue || 0));
            profit.push(parseFloat(row.profit || 0));
        }

        if (!this.charts.revenue) {
            this.initRevenueChart();
        }

        if (this.charts.revenue) {
            this.charts.revenue.data.labels = labels;
            this.charts.revenue.data.datasets[0].data = revenue;
            this.charts.revenue.data.datasets[1].data = profit;
            this.charts.revenue.update();
        }
    }

    updateCategoryChart() {
        const data = window.dashboardData || {};
        const categoryStats = data.categoryStats || [];
        const categories = categoryStats.length > 0 ? categoryStats.map(c => c.name || 'Other') : ['No Data'];
        const revenue = categoryStats.length > 0 ? categoryStats.map(c => parseFloat(c.revenue || 0)) : [0];
        const colors = [
            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#14B8A6', '#6366F1', '#D946EF', '#0891B2'
        ];

        if (!this.charts.category) {
            this.initCategoryChart();
        }

        if (this.charts.category) {
            this.charts.category.data.labels = categories;
            this.charts.category.data.datasets[0].data = revenue;
            this.charts.category.data.datasets[0].backgroundColor = colors.slice(0, categories.length);
            this.charts.category.update();
        }
    }

    buildTrendLabels(trends) {
        const days = this.periodOptions[this.currentPeriod] || 7;
        const labels = [];
        const today = new Date();

        for (let i = days - 1; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(today.getDate() - i);
            labels.push(date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' }));
        }

        return labels;
    }

    generate7DayData() {
        const now = new Date();
        const labels = [];
        const data = [];
        const profitData = [];

        for (let i = 6; i >= 0; i--) {
            const date = new Date(now);
            date.setDate(date.getDate() - i);
            labels.push(date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' }));
            
            // Generate realistic data with some variation
            const baseRevenue = 1000 + Math.random() * 1500;
            const baseProfit = baseRevenue * 0.3;
            
            data.push(Math.round(baseRevenue));
            profitData.push(Math.round(baseProfit));
        }

        return { labels, data, profitData };
    }

    formatCurrency(value) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value) + ' TND';
    }

    formatChartValue(value) {
        if (value >= 1000) {
            return (value / 1000).toFixed(1) + 'k TND';
        }
        return value + ' TND';
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DashboardManager();
});
