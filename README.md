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

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
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
