# Stock & Sales Management System v1.0.0

A comprehensive, production-ready web application for managing inventory and point-of-sale operations with a modern SaaS dashboard design.

## 🎯 Features

### Dashboard
- Real-time sales statistics and KPIs
- Top-selling products (30-day trending)
- Low stock alerts
- Daily sales overview
- Profit tracking and analytics
- Revenue breakdown by payment method

### Point of Sale (POS)
- Fast product search and barcode scanning
- Dynamic shopping cart with real-time calculations
- Dual-mode support: Pack/Unit (tobacco products)
- Cash/Card payment tracking
- Change calculation
- Transaction history and receipt printing
- F2/F4/F8 keyboard shortcuts for efficiency

### Product Management
- Complete inventory control
- Barcode support
- Category organization
- Supplier tracking
- Price management (buying/selling)
- Cigarette unit pricing
- Stock level alerts
- Product status (active/inactive)

### Sales & Analytics
- Detailed sales records with filters
- Revenue and profit analytics
- Sales by payment method
- Top-performing products
- Category-wise sales breakdown
- Profit margin calculations
- Exportable reports

### User Management (Admin)
- Role-based access control (Boss/Employee)
- User account management
- Activity tracking
- Status controls

## 🛠️ Technical Stack

- **Backend:** PHP 7.4+ (OOP, MVC)
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3 (Variables & Grid), Vanilla JavaScript (ES6)
- **Security:** Bcrypt password hashing, CSRF tokens, SQL prepared statements, XSS protection
- **Design:** Modern SaaS aesthetic (Stripe/Linear inspired)

## 📋 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP or similar local server

### Setup Steps

1. **Navigate to the project directory:**
   ```bash
   cd C:\xampp\htdocs\shop_v2
   ```

2. **Initialize the database:**
   ```bash
   php init-db.php
   ```

3. **Verify directory permissions:**
   ```bash
   chmod -R 755 app/
   chmod -R 755 public/
   chmod -R 777 uploads/
   chmod -R 777 logs/
   ```

4. **Access the application:**
   ```
   http://localhost/shop_v2/
   ```

## 🔐 Demo Credentials

| Role     | Username | Password |
|----------|----------|----------|
| Admin    | admin    | password |
| Employee | employee | password |

**Password Hash:** `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.X2gA4iMS2`

Both credentials hash to: `password`

## 🗂️ Project Structure

```
shop_v2/
├── app/
│   ├── Controllers/          # Business logic
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── POSController.php
│   │   └── ProductController.php
│   ├── Models/              # Database entities
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   └── Reference.php (Category, Supplier, Customer)
│   ├── Helpers/             # Utilities
│   │   ├── SecurityHelper.php
│   │   └── FormatterHelper.php
│   ├── Views/               # Template files
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── pos/
│   │   ├── products/
│   │   └── layouts/
│   └── Database.php         # PDO connection (Singleton)
├── public/
│   ├── css/
│   │   └── style.css        # Modern dashboard styles
│   ├── js/
│   │   └── app.js           # Core JS + POS system
│   └── images/
├── uploads/                 # Product images
├── logs/                    # Application logs
├── config.php              # Configuration
├── index.php               # Front controller/router
├── .htaccess               # URL rewriting
└── init-db.php             # Database initialization
```

## 🔄 Database Schema

### Core Tables
- **users** - Team members with role-based access
- **products** - Inventory with pricing and stock
- **categories** - Product categorization
- **suppliers** - Vendor management
- **customers** - Customer records
- **sales** - Transaction records
- **sale_items** - Line items per transaction
- **settings** - System configuration
- **stock_history** - Stock movement audit log

## 🛡️ Security Features

1. **Authentication:** Session-based with HTTPOnly cookies
2. **Authorization:** Role-based access control (RBAC)
3. **Data Protection:**
   - Prepared statements (PDO) for SQL injection prevention
   - Output escaping with `htmlspecialchars()` (XSS prevention)
   - CSRF token verification on all POST requests
4. **Password Security:** Bcrypt hashing with cost factor 10
5. **HTTP Headers:** X-Content-Type-Options, X-Frame-Options, X-XSS-Protection

## 🎨 UI/UX Design System

### Color Palette
| Color       | Hex       | Usage |
|------------|-----------|-------|
| Primary    | #4F46E5   | Indigo - Main actions |
| Success    | #10B981   | Emerald - Positive feedback |
| Warning    | #F59E0B   | Amber - Alerts |
| Danger     | #EF4444   | Rose - Destructive actions |
| Dark BG    | #0F172A   | Slate 900 - Sidebar |
| App BG     | #F8FAFC   | Slate 50 - Main background |

### Typography
- System font stack: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif
- Font sizes: 12px (small), 14px (body), 16px (titles), 20px+ (headings)
- Line height: 1.5 (body), 1.2 (headings)

### Spacing & Layout
- Base unit: 4px
- Padding: 12px (compact), 16px (standard), 20px (generous), 24px (spacious)
- Border radius: 8px (cards), 12px (dialogs)
- Transitions: 150ms cubic-bezier(0.4, 0, 0.2, 1)

## ⌨️ POS Keyboard Shortcuts

| Key | Function |
|-----|----------|
| F2  | Focus product search |
| F4  | Proceed to checkout |
| F8  | Clear cart |
| ESC | Close modals |

## 📊 API Endpoints

### Authentication
- `POST /index.php?url=login` - User login
- `GET /index.php?url=logout` - User logout
- `POST /index.php?url=register` - Create user (admin only)

### Dashboard
- `GET /index.php?url=dashboard` - Main dashboard
- `GET /index.php?url=dashboard/analytics` - Analytics & reports
- `GET /index.php?url=dashboard/users` - User management

### POS
- `GET /index.php?url=pos` - POS interface
- `POST /index.php?url=pos/searchProducts` - Product search (AJAX)
- `POST /index.php?url=pos/getProductByBarcode` - Barcode lookup
- `POST /index.php?url=pos/checkout` - Process sale
- `GET /index.php?url=pos/printReceipt` - Print receipt
- `GET /index.php?url=pos/sales` - Sales history

### Products
- `GET /index.php?url=products` - Product inventory
- `GET /index.php?url=products/create` - Add product form
- `POST /index.php?url=products/create` - Save new product
- `GET /index.php?url=products/edit&id=X` - Edit product form
- `POST /index.php?url=products/edit&id=X` - Update product
- `POST /index.php?url=products/delete` - Delete product
- `GET /index.php?url=products/categories` - Category management
- `POST /index.php?url=products/addCategory` - Add category

## 🔧 Configuration

Edit `config.php` to customize:

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_USER', 'root');           // DB username
define('DB_PASS', '');               // DB password
define('DB_NAME', 'stock_management'); // Database name
define('CURRENCY', 'TND');           // Currency code
define('TAX_RATE', 0.00);            // Default tax percentage
```

## 📱 Responsive Design

- Desktop-first approach (optimized for 1200px+)
- Mobile breakpoint at 768px
- Touch-friendly buttons (min 44px)
- Collapsible sidebar on mobile

## 🚀 Performance Optimizations

1. **Database:** Indexed primary/foreign keys, prepared statements
2. **Frontend:** Lazy JavaScript loading, CSS variables for theming
3. **Caching:** LocalStorage for POS cart persistence
4. **Minification:** Production-ready CSS/JS

## 📝 Logging

Application logs are stored in `/logs/` directory. Monitor for errors:

```bash
tail -f C:\xampp\htdocs\shop_v2\logs\*.log
```

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running: `mysql -u root`
- Check credentials in `config.php`
- Run initialization: `php init-db.php`

### Permission Denied
- Ensure web server user has access: `chmod 755 app/ public/`
- Verify uploads folder is writable: `chmod 777 uploads/`

### Session Issues
- Clear browser cookies
- Verify session save path: `php -r "echo ini_get('session.save_path');"`

## 📄 License

This system is provided as-is for commercial and personal use.

## 🤝 Support

For issues or questions, refer to the code documentation within each file.

---

**Version:** 1.0.0  
**Last Updated:** 2026  
**Status:** Production Ready
