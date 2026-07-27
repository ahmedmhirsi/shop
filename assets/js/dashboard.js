/* ============================================================================
   DASHBOARD JavaScript - Stock & Sales Management System
   Chart.js Initialization, Interactivity, and Data Management
   ============================================================================ */

/* ============================================================================
   CHART CONFIGURATION & SETUP
   ============================================================================ */

// Color palette matching CSS design system
const colors = {
  primary: '#4F46E5',
  primaryHover: '#4338CA',
  success: '#10B981',
  warning: '#F59E0B',
  danger: '#EF4444',
  slate900: '#0F172A',
  slate600: '#475569',
  slate400: '#94A3B8',
  slate200: '#E2E8F0',
  slate50: '#F8FAFC'
};

// Chart.js global configuration
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
Chart.defaults.color = colors.slate600;
Chart.defaults.borderColor = colors.slate200;

// Utility: Format currency values (TND)
function formatCurrency(value) {
  return new Intl.NumberFormat('fr-TN', {
    style: 'currency',
    currency: 'TND',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
}

// Utility: Format numbers with thousands separator
function formatNumber(value) {
  return new Intl.NumberFormat('fr-TN', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value);
}

// Utility: Create gradient for charts
function createGradient(ctx, color1, color2) {
  const gradient = ctx.createLinearGradient(0, 0, 0, 400);
  gradient.addColorStop(0, color1 + '40');
  gradient.addColorStop(1, color1 + '05');
  return gradient;
}

/* ============================================================================
   7-DAY REVENUE & PROFIT TREND CHART
   ============================================================================ */

function initRevenueChart() {
  const ctx = document.getElementById('revenueChart');
  if (!ctx) return;

  // Sample data - Replace with real API data
  const chartData = {
    labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
    revenue: [1200, 1900, 1700, 2100, 2400, 3100, 2800],
    profit: [600, 900, 850, 1050, 1200, 1550, 1400]
  };

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartData.labels,
      datasets: [
        {
          label: 'Chiffre d\'Affaires (CA)',
          data: chartData.revenue,
          backgroundColor: colors.primary,
          borderRadius: 6,
          borderSkipped: false,
          barPercentage: 0.7,
          categoryPercentage: 0.8,
          fill: false
        },
        {
          label: 'Profit Net',
          data: chartData.profit,
          borderColor: colors.success,
          backgroundColor: 'transparent',
          borderWidth: 2,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: colors.success,
          pointBorderColor: 'white',
          pointBorderWidth: 2,
          fill: false,
          type: 'line',
          yAxisID: 'y1'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false
      },
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            padding: 16,
            font: {
              size: 12,
              weight: 500
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.9)',
          padding: 12,
          titleFont: {
            size: 13,
            weight: 600
          },
          bodyFont: {
            size: 12
          },
          borderColor: colors.slate200,
          borderWidth: 1,
          displayColors: true,
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              label += formatCurrency(context.parsed.y);
              return label;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          position: 'left',
          title: {
            display: true,
            text: 'Chiffre d\'Affaires (TND)',
            font: {
              size: 11,
              weight: 600
            }
          },
          ticks: {
            callback: function(value) {
              return formatCurrency(value);
            }
          },
          grid: {
            color: colors.slate50
          }
        },
        y1: {
          beginAtZero: true,
          position: 'right',
          title: {
            display: true,
            text: 'Profit Net (TND)',
            font: {
              size: 11,
              weight: 600
            }
          },
          ticks: {
            callback: function(value) {
              return formatCurrency(value);
            }
          },
          grid: {
            display: false
          }
        },
        x: {
          grid: {
            display: false
          }
        }
      }
    }
  });
}

/* ============================================================================
   SALES BY CATEGORY DOUGHNUT CHART
   ============================================================================ */

function initCategoryChart() {
  const ctx = document.getElementById('categoryChart');
  if (!ctx) return;

  // Sample data
  const chartData = {
    labels: ['Tabac', 'Alimentation', 'Électronique', 'Boissons', 'Autres'],
    values: [2800, 1900, 1200, 1800, 700],
    colors: [colors.warning, colors.success, colors.primary, colors.slate400, colors.slate200]
  };

  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: chartData.labels,
      datasets: [
        {
          data: chartData.values,
          backgroundColor: chartData.colors,
          borderColor: 'white',
          borderWidth: 3
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            padding: 12,
            font: {
              size: 11,
              weight: 500
            },
            usePointStyle: true,
            pointStyle: 'circle'
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.9)',
          padding: 12,
          titleFont: {
            size: 13,
            weight: 600
          },
          bodyFont: {
            size: 12
          },
          borderColor: colors.slate200,
          borderWidth: 1,
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((context.parsed / total) * 100).toFixed(1);
              return context.label + ': ' + formatCurrency(context.parsed) + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });
}

/* ============================================================================
   DATE & TIME DISPLAY
   ============================================================================ */

function updateDateTime() {
  const now = new Date();
  const options = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  };
  
  const dateTimeStr = new Intl.DateTimeFormat('fr-FR', options).format(now);
  const dateElement = document.getElementById('currentDateTime');
  if (dateElement) {
    dateElement.textContent = dateTimeStr.charAt(0).toUpperCase() + dateTimeStr.slice(1);
  }
}

// Update date/time every second
setInterval(updateDateTime, 1000);
updateDateTime(); // Initial call

/* ============================================================================
   PERIOD SELECTOR FOR REVENUE CHART
   ============================================================================ */

document.addEventListener('DOMContentLoaded', function() {
  const periodButtons = document.querySelectorAll('.period-btn');
  
  periodButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Remove active class from all buttons
      periodButtons.forEach(b => b.classList.remove('active'));
      
      // Add active class to clicked button
      this.classList.add('active');
      
      // Get selected period
      const period = this.getAttribute('data-period');
      console.log('Period selected:', period);
      
      // TODO: Reload chart with new period data
      // This would typically make an AJAX call to fetch data for the selected period
    });
  });
  
  // Initialize charts
  setTimeout(() => {
    initRevenueChart();
    initCategoryChart();
  }, 100);
});

/* ============================================================================
   KPI ANIMATIONS ON SCROLL
   ============================================================================ */

const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

document.addEventListener('DOMContentLoaded', function() {
  const kpiCards = document.querySelectorAll('.kpi-card');
  kpiCards.forEach(card => {
    observer.observe(card);
  });
});

/* ============================================================================
   SHIFT STATUS & ACTIVE SHIFT INDICATOR
   ============================================================================ */

function updateShiftStatus() {
  const now = new Date();
  const hour = now.getHours();
  
  let activeShift = null;
  if (hour >= 7 && hour < 16) {
    activeShift = 1;
  } else if (hour >= 16 && hour < 22) {
    activeShift = 2;
  } else {
    activeShift = 3;
  }
  
  // Update shift indicators
  const shiftItems = document.querySelectorAll('.shift-item');
  shiftItems.forEach((item, index) => {
    item.classList.remove('active');
    if (index + 1 === activeShift) {
      item.classList.add('active');
    }
  });
}

// Run on page load
document.addEventListener('DOMContentLoaded', updateShiftStatus);
setInterval(updateShiftStatus, 60000); // Update every minute

/* ============================================================================
   QUICK RESTOCK BUTTON HANDLER
   ============================================================================ */

document.addEventListener('DOMContentLoaded', function() {
  const restockButtons = document.querySelectorAll('.btn-restock');
  
  restockButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const productName = this.getAttribute('data-product');
      console.log('Restock requested for:', productName);
      // TODO: Show modal/form to enter restock quantity
      alert('Réapprovisionner: ' + productName);
    });
  });
});

/* ============================================================================
   PRINT INVOICE HANDLER
   ============================================================================ */

document.addEventListener('DOMContentLoaded', function() {
  const printButtons = document.querySelectorAll('.btn-print');
  
  printButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const invoiceNo = this.getAttribute('data-invoice');
      console.log('Printing invoice:', invoiceNo);
      // TODO: Trigger print dialog or generate PDF
      window.print();
    });
  });
});

/* ============================================================================
   RESPONSIVE CHART RESIZING
   ============================================================================ */

window.addEventListener('resize', function() {
  // Charts automatically resize with Chart.js responsive option
  // This event can trigger additional responsive adjustments if needed
  console.log('Window resized');
});

/* ============================================================================
   ANIMATIONS (CSS-driven, JS support)
   ============================================================================ */

// Add animation styles dynamically
const style = document.createElement('style');
style.textContent = \
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @keyframes pulse {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }
  
  .pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
\;
document.head.appendChild(style);

/* ============================================================================
   EXPORT UTILITIES (Future Enhancement)
   ============================================================================ */

function exportDataAsCSV() {
  console.log('Export to CSV requested');
  // TODO: Implement CSV export of dashboard data
}

function exportDataAsPDF() {
  console.log('Export to PDF requested');
  // TODO: Implement PDF export of dashboard data
}

/* ============================================================================
   ERROR HANDLING & DEBUGGING
   ============================================================================ */

window.addEventListener('error', function(e) {
  console.error('Dashboard Error:', e.error);
  // TODO: Send error to logging service
});

// Log initialization
console.log('Dashboard JavaScript loaded successfully');
console.log('Charts will initialize on page load');
