<<<<<<< HEAD
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
=======
# Stock & Sales Management System

A complete Point of Sale (POS) and Stock Management web application built with PHP, MySQL, HTML5, CSS3, and Vanilla JavaScript.

## Features

### User Roles
- **Boss (Administrator)**: Full access to all features including dashboard, stock management, reports, and settings
- **Employee**: Limited to POS functionality - can sell products, search, view stock, and print invoices

### Boss Features
- **Dashboard**: Real-time statistics with beautiful cards showing:
  - Today's Revenue, Sales, and Profit
  - Week, Month, and Year Revenue
  - Total Revenue, Sales, and Products
  - Low Stock and Out of Stock alerts
  - Recent Sales and Top Selling Products
  - Interactive charts for Revenue and Sales per month

- **Stock Management**:
  - Add, Edit, Delete products
  - Manage Categories and Suppliers
  - Barcode support with auto-generator
  - Product images
  - Stock quantity tracking with alerts
  - Minimum stock level configuration

- **Point of Sale**:
  - Modern POS interface
  - Product search by barcode or name
  - Category filtering
  - Shopping cart with quantity management
  - Discount support
  - Tax calculation
  - Multiple payment methods (Cash, Card)
  - Invoice generation and printing
  - Keyboard shortcuts (F2: Search, F4: Checkout, F8: Clear Cart)

- **Reports**:
  - Daily, Weekly, Monthly, Yearly reports
  - Custom date range reports
  - Revenue, Profit, Products Sold, Transactions
  - Print functionality

- **Sales History**:
  - View all sales with filters
  - Sale details with items
  - Cancel sales with stock restoration
  - Invoice reprint

- **Stock History**:
  - Track all stock movements
  - Filter by date range
  - View user who made changes

- **Settings**:
  - Store name and logo
  - Address, phone, email
  - Currency symbol
  - Tax percentage
  - Invoice prefix
  - Low stock alert threshold

### Employee Features
- **Point of Sale**: Full POS functionality
- Product search and view stock
- Print invoices
- Cancel invoices before validation
- Add customer name (optional)
- Select payment method (Cash/Card)

### Security Features
- PDO Prepared Statements (SQL Injection prevention)
- Password Hashing (bcrypt)
- CSRF Protection
- XSS Prevention (input sanitization and output escaping)
- Session-based authentication
- Role-based access control

### Extra Features
- Dark Mode toggle
- Responsive design (Desktop, Tablet, Mobile)
- Loading animations
- Popup notifications
- Confirmation dialogs
- Pagination
- Search and filters
- Sorting

## Installation
>>>>>>> b47ee5eba4e058640b479010b7719ba3976e48d5

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
<<<<<<< HEAD
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
=======
- Web server (Apache/Nginx)
- Web browser (Chrome, Firefox, Safari, Edge)

### Step 1: Clone/Download the Project
```bash
git clone <repository-url>
cd shop
```

Or download and extract the ZIP file.

### Step 2: Configure Database
1. Create a MySQL database named `stock_management`
2. Import the SQL file:
```bash
mysql -u root -p stock_management < database/database.sql
```

Or use phpMyAdmin to import `database/database.sql`

### Step 3: Configure Database Connection
Edit `config/config.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### Step 4: Set File Permissions
Make the following directories writable:
```bash
chmod 755 uploads
```

### Step 5: Configure Web Server
Point your web server to the project directory.

For Apache, ensure mod_rewrite is enabled.

### Step 6: Access the Application
Open your browser and navigate to:
```
http://localhost/shop
```

## Default Login Credentials

### Boss (Administrator)
- **Username**: admin
- **Password**: password

### Employee
- **Username**: employee
- **Password**: password

**Important**: Change these passwords after first login!

## Project Structure

```
shop/
├── assets/
│   ├── css/
│   │   ├── style.css          # Main stylesheet
│   │   └── pos.css            # POS specific styles
│   ├── js/
│   │   ├── main.js            # Common JavaScript
│   │   ├── charts.js          # Chart.js integration
│   │   └── pos.js             # POS functionality
│   └── images/               # Static images
├── config/
│   ├── config.php            # Application configuration
│   └── database.php          # Database connection class
├── controllers/
│   ├── DashboardController.php
│   ├── ProductController.php
│   ├── CategoryController.php
│   ├── SupplierController.php
│   ├── POSController.php
│   ├── ReportController.php
│   ├── SettingsController.php
│   ├── SaleController.php
│   └── StockController.php
├── models/
│   ├── Model.php             # Base model class
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Supplier.php
│   ├── Customer.php
│   ├── Sale.php
│   ├── SaleItem.php
│   ├── Settings.php
│   └── StockHistory.php
├── views/
│   ├── layout.php            # Main layout
│   ├── dashboard.php
│   ├── products/
│   ├── categories/
│   ├── suppliers/
│   ├── pos/
│   ├── reports/
│   ├── settings/
│   ├── sales/
│   └── stock/
├── includes/
│   └── functions.php         # Helper functions
├── database/
│   └── database.sql          # SQL schema
├── uploads/                  # File uploads directory
├── api/
│   └── notifications.php     # API endpoints
├── index.php                # Main entry point
├── login.php                # Login page
├── logout.php               # Logout page
└── README.md                # This file
```

## Usage

### For Boss (Administrator)

1. **Login** with admin credentials
2. **Dashboard** - View statistics and charts
3. **Products** - Manage inventory
   - Click "Add Product" to add new items
   - Use "Edit" to modify existing products
   - Use "Delete" to remove products
4. **Categories** - Organize products into categories
5. **Suppliers** - Manage supplier information
6. **Point of Sale** - Process sales
   - Search products by barcode or name
   - Click products to add to cart
   - Adjust quantities in cart
   - Apply discounts
   - Select payment method
   - Complete sale and print invoice
7. **Reports** - View sales reports
   - Choose report type (Daily, Weekly, Monthly, Yearly, Custom)
   - Filter by date range
   - Print reports
8. **Sales History** - View and manage past sales
   - View sale details
   - Cancel sales if needed
9. **Stock History** - Track stock movements
10. **Settings** - Configure store settings
    - Update store information
    - Set currency and tax
    - Configure alerts

### For Employee

1. **Login** with employee credentials
2. **Point of Sale** - Process sales
   - Search products
   - Add to cart
   - Complete sale
   - Print invoice

## Keyboard Shortcuts (POS)

- **F2** - Focus search box
- **F4** - Checkout
- **F8** - Clear cart
- **Escape** - Close modals

## Security Notes

1. **Change Default Passwords**: Immediately change the default passwords after installation
2. **HTTPS**: Use HTTPS in production for secure data transmission
3. **File Permissions**: Ensure proper file permissions on the server
4. **Regular Backups**: Backup the database regularly
5. **Update Dependencies**: Keep PHP and MySQL updated

## Troubleshooting

### Database Connection Error
- Check database credentials in `config/config.php`
- Ensure MySQL server is running
- Verify database name and user permissions

### File Upload Issues
- Check `uploads` directory permissions
- Verify PHP upload limits in `php.ini`
- Ensure `upload_max_filesize` and `post_max_size` are sufficient

### Session Issues
- Check session save path permissions
- Ensure cookies are enabled in browser
- Verify session configuration in `php.ini`

### Charts Not Displaying
- Check internet connection (Chart.js loads from CDN)
- Verify JavaScript console for errors
- Ensure Chart.js CDN is accessible

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Opera

## Support

For issues, questions, or contributions, please contact the development team.

## License

This project is proprietary software. All rights reserved.

## Credits

Developed as a complete Stock & Sales Management System for retail businesses.
>>>>>>> b47ee5eba4e058640b479010b7719ba3976e48d5
