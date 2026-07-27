#!/usr/bin/env php
<?php
/**
 * Database initialization script
 * Run: php init-db.php
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'stock_management');

try {
    $pdo = new PDO('mysql:host=' . DB_HOST, DB_USER, DB_PASS);
    
    echo "[*] Creating database...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    
    echo "[*] Creating users table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('boss', 'employee') NOT NULL DEFAULT 'employee',
        status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    
    echo "[*] Creating categories table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    
    echo "[*] Creating suppliers table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS suppliers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        contact_person VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    
    echo "[*] Creating products table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        barcode VARCHAR(50) UNIQUE,
        name VARCHAR(200) NOT NULL,
        category_id INT NULL,
        supplier_id INT NULL,
        buying_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        selling_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        cigarette_price DECIMAL(10,3) DEFAULT 0.000,
        cigarettes_per_pack INT DEFAULT 20,
        quantity DECIMAL(10,3) NOT NULL DEFAULT 0.000,
        minimum_stock INT DEFAULT 5,
        image VARCHAR(255) DEFAULT 'default_product.png',
        description TEXT,
        status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
    ) ENGINE=InnoDB");
    
    echo "[*] Creating customers table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    
    echo "[*] Creating sales table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(50) NOT NULL UNIQUE,
        customer_id INT NULL,
        user_id INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        discount DECIMAL(10,2) DEFAULT 0.00,
        tax DECIMAL(10,2) DEFAULT 0.00,
        total DECIMAL(10,2) NOT NULL,
        payment_method ENUM('cash', 'card') NOT NULL DEFAULT 'cash',
        amount_received DECIMAL(10,2) NOT NULL,
        change_amount DECIMAL(10,2) DEFAULT 0.00,
        notes TEXT,
        status ENUM('completed', 'cancelled') NOT NULL DEFAULT 'completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=InnoDB");
    
    echo "[*] Creating sale_items table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS sale_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sale_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        buying_price DECIMAL(10,2) NOT NULL,
        selling_price DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        profit DECIMAL(10,2) NOT NULL,
        unit_type ENUM('pack', 'cigarette') NOT NULL DEFAULT 'pack',
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB");
    
    echo "[*] Creating settings table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_name VARCHAR(100) NOT NULL DEFAULT 'My Store',
        logo VARCHAR(255) DEFAULT 'logo.png',
        address TEXT,
        phone VARCHAR(20),
        email VARCHAR(100),
        currency VARCHAR(10) DEFAULT 'TND',
        tax_percentage DECIMAL(5,2) DEFAULT 0.00,
        invoice_prefix VARCHAR(20) DEFAULT 'INV-',
        low_stock_alert INT DEFAULT 5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    
    echo "[*] Creating stock_history table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS stock_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        quantity DECIMAL(10,3) NOT NULL,
        type ENUM('in', 'out') NOT NULL,
        reference_id INT,
        reference_type VARCHAR(50),
        notes TEXT,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=InnoDB");
    
    echo "[*] Inserting initial data...\n";
    $pdo->exec("INSERT INTO users (username, password, full_name, role, status) VALUES 
        ('admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.X2gA4iMS2', 'Magasin Boss', 'boss', 'active'),
        ('employee', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.X2gA4iMS2', 'Employé POS', 'employee', 'active')
    ON DUPLICATE KEY UPDATE id=id");
    
    $pdo->exec("INSERT INTO categories (name, description) VALUES 
        ('Electronics', 'Gadgets and electronics'),
        ('Food & Beverages', 'General food and soft drinks'),
        ('Clothing', 'Apparel and accessories'),
        ('Tobacco', 'Cigarettes and tobacco products'),
        ('Health & Beauty', 'Personal care products')
    ON DUPLICATE KEY UPDATE id=id");
    
    $pdo->exec("INSERT INTO customers (name, phone) VALUES 
        ('Passager / Comptoir', '00000000')
    ON DUPLICATE KEY UPDATE id=id");
    
    $pdo->exec("INSERT INTO settings (store_name, currency, tax_percentage) VALUES 
        ('Mon Magasin', 'TND', 0.00)
    ON DUPLICATE KEY UPDATE id=id");
    
    echo "\n✅ Database initialization completed successfully!\n";
    echo "\n📝 Demo Credentials:\n";
    echo "   Admin: admin / password\n";
    echo "   Employee: employee / password\n";
    echo "\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
