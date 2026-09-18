# Shop — Stock & Sales Management System

Shop is a PHP/MySQL web application for managing retail inventory and point-of-sale operations. It provides separate administrator and employee workflows for products, stock, sales, customers, reporting, and store configuration.

## Features

### Point of sale

- Search products by name or barcode
- Cart management with quantities and discounts
- Cash and card payments with change calculation
- Customer association and invoice generation
- Receipt printing
- Pack and individual cigarette sales
- Keyboard shortcuts: `F2` search, `F4` checkout, `F8` clear cart

### Inventory and catalog

- Product, category, and supplier management
- Barcode, buying price, selling price, images, and descriptions
- Stock quantities and minimum-stock thresholds
- Tobacco-specific cigarette pricing
- Stock movement history
- Low-stock and out-of-stock alerts

### Dashboard and administration

- Revenue, sales, profit, and product KPIs
- Daily, weekly, monthly, yearly, and custom reports
- Sales history, cancellation, and invoice reprinting
- Role-based access for `boss` and `employee` users
- Store settings, tax rate, currency, invoice prefix, and logo
- Responsive dashboard UI with Chart.js and Inter typography

## Technology stack

- **Backend:** PHP 7.4+ with PDO
- **Database:** MySQL 5.7+ / MariaDB
- **Frontend:** HTML5, CSS3, and vanilla JavaScript
- **Libraries:** Chart.js, Font Awesome, Google Fonts (Inter)
- **Web server:** Apache (XAMPP recommended) or another PHP-compatible server

## Requirements

- PHP 7.4 or later
- MySQL 5.7 or later, or MariaDB
- Apache, Nginx, or PHP's development server
- PHP extensions: `pdo`, `pdo_mysql`, `mbstring`, and `fileinfo`

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/ahmedmhirsi/shop.git
cd shop
```

### 2. Create the database

The complete schema and sample data are in `database/database.sql`:

```bash
mysql -u root -p < database/database.sql
```

Alternatively, initialize the database with the included script:

```bash
php init-db.php
```

The default database name is `stock_management`.

### 3. Configure the connection

Edit `config/config.php` and set the MySQL connection values:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

For the MVC entry point, also verify the values in the root `config.php` if your deployment uses that configuration file.

### 4. Configure writable directories

Ensure the web server can write to the upload and log directories:

```bash
chmod 755 uploads
chmod 755 logs
```

On Windows/XAMPP, grant the Apache process write access through the directory security settings instead.

### 5. Start the application

With XAMPP, start Apache and MySQL, then open:

```text
http://localhost/shop/
```

For a local PHP development server:

```bash
php -S localhost:8000
```

Then visit <http://localhost:8000/>.

## Demo accounts

The database initialization creates these accounts:

| Role | Username | Password |
| --- | --- | --- |
| Administrator (`boss`) | `admin` | `password` |
| Employee | `employee` | `password` |

Change both passwords immediately in any non-development environment.

## Project structure

```text
shop/
├── app/                    # MVC application classes and templates
│   ├── Controllers/        # Authentication, dashboard, POS, and product controllers
│   ├── Models/             # Users, products, sales, and references
│   ├── Helpers/            # Security and formatting helpers
│   └── Views/              # Authentication, dashboard, POS, and product views
├── assets/                 # CSS and JavaScript assets
├── config/                 # Database and application configuration
├── controllers/            # Additional management/report controllers
├── database/
│   └── database.sql        # MySQL schema and sample data
├── includes/               # Shared application functions
├── models/                 # Additional domain models
├── public/                 # Public CSS and JavaScript assets
├── views/                  # Management and reporting views
├── api/                    # Small API endpoints
├── uploads/                # Product image uploads
├── index.php               # Application entry point and router
├── init-db.php             # Database initialization script
├── login.php               # Login entry point
└── health-check.php        # Environment/database health check
```

The repository currently contains both the newer `app/` MVC implementation and an additional controller/model/view tree kept for compatibility with existing screens. Use the routes exposed by `index.php` for the active deployment.

## Main routes

The MVC router uses the `url` query parameter:

```text
index.php?url=login
index.php?url=dashboard
index.php?url=pos
index.php?url=products
index.php?url=pos/sales
index.php?url=dashboard/analytics
index.php?url=dashboard/users
index.php?url=dashboard/settings
```

Unauthenticated users are redirected to the login page. Administrative screens require the `boss` role.

## Security

- Passwords are hashed with bcrypt
- PDO prepared statements protect database queries
- CSRF tokens are used for state-changing requests
- Output is escaped with `htmlspecialchars`
- Session cookies are configured as HTTP-only
- Role-based authorization protects administrative actions

Before production deployment, disable PHP error display, use HTTPS, replace demo credentials, restrict upload permissions, and keep regular database backups.

## Troubleshooting

Check the environment and database connection with:

```bash
php health-check.php
```

If the application cannot connect to MySQL, verify that the server is running and that the credentials in the active configuration file match the database user. If product images cannot be uploaded, verify the `uploads/` directory permissions and PHP upload limits.

## License

This project is proprietary software. All rights reserved.
