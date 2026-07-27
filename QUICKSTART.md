# 🚀 Quick Start Guide - Stock & Sales Management System

## System Ready for Production ✅

Your complete Stock & Sales Management System v1.0.0 is now installed and ready to use!

## First-Time Setup (2 Minutes)

### 1. Start XAMPP
```bash
# Windows
C:\xampp\xampp-control.exe
```

Click **Start** next to Apache and MySQL.

### 2. Access the Application
```
http://localhost/shop_v2/
```

### 3. Login with Demo Credentials
| Field    | Value    |
|----------|----------|
| Username | admin    |
| Password | password |

## 🎯 Getting Started

### For Admins (Boss Role)

1. **Dashboard** - View today's sales, top products, and system health
2. **POS** - Process sales and manage checkout
3. **Products** - Add/edit inventory items
4. **Categories** - Organize products
5. **Users** - Manage team members
6. **Analytics** - Detailed reports and insights

### For Employees

1. **POS** - Process sales
2. **Products** - View inventory
3. **Sales** - Check transaction history

## ⌨️ POS Power User Tips

### Shortcuts
- **F2** - Focus search bar
- **F4** - Complete checkout
- **F8** - Clear cart
- **ESC** - Close dialogs

### Workflow
1. Type product name or barcode in search
2. Click "Add" or press Enter
3. Adjust quantity with +/- buttons
4. Enter payment amount
5. Press F4 to checkout
6. Receipt prints automatically

### Tobacco Products
- Toggle "Mode Cigarette" for unit pricing
- Perfect for bulk vs. individual sales

## 📊 Key Features at a Glance

### Dashboard
- **Real-time KPIs:** Today's sales count, revenue, profit
- **Trend Analysis:** Top 5 best-selling products
- **Stock Alerts:** Low inventory warnings
- **Quick Access:** Recent transactions

### Inventory Management
- Barcode support (UPC/EAN)
- Multi-category organization
- Supplier tracking
- Automatic stock history
- Low stock thresholds
- Tobacco unit pricing

### Sales Operations
- Fast product lookup
- Dynamic pricing
- Multiple payment methods
- Customer tracking
- Receipt printing
- Change calculation

### Analytics & Reporting
- Daily revenue trends
- Profit margin analysis
- Sales by payment method
- Category performance
- Top products ranking
- Staff performance

## 🔧 Common Tasks

### Add a Product
```
1. Go to Products > Add Product
2. Fill in name, prices, quantity
3. Set minimum stock level
4. Assign category
5. Click Save
```

### Process a Sale
```
1. Go to POS
2. Search for products
3. Add to cart (F2)
4. Enter payment amount
5. Checkout (F4)
6. Receipt prints
```

### Create User Account
```
1. Go to Dashboard > Users (admin only)
2. Click Add User
3. Set username, password, role
4. Click Create Account
```

### View Reports
```
1. Go to Dashboard > Analytics
2. Select date range
3. View sales breakdown by:
   - Payment method
   - Product performance
   - Category sales
   - Profit analysis
```

## 📁 File Organization

```
shop_v2/
├── Controllers/     - Business logic
├── Models/          - Database operations
├── Views/           - User interface
├── public/
│   ├── css/         - Styling
│   └── js/          - Interactivity
├── uploads/         - Product images
└── logs/            - Application logs
```

## 🔒 Security

Your system includes:
- ✅ Bcrypt password hashing
- ✅ CSRF token protection
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Role-based access control
- ✅ HTTPOnly cookies
- ✅ Prepared statements

**Never share the database password with customers!**

## 🎨 Customization

### Colors
Edit `/public/css/style.css` CSS variables:
```css
:root {
    --primary: #4F46E5;        /* Main color */
    --success: #10B981;        /* Success color */
    --warning: #F59E0B;        /* Alert color */
    --danger: #EF4444;         /* Error color */
}
```

### Store Name
Edit `config.php`:
```php
define('APP_NAME', 'Your Store Name');
```

### Tax Rate
Edit `config.php`:
```php
define('TAX_RATE', 7.00);  // 7% tax
```

## 🆘 Troubleshooting

### Application won't load
1. Verify Apache and MySQL are running
2. Check database connection: `php health-check.php`
3. Clear browser cache: Ctrl+Shift+Delete

### Login not working
1. Ensure MySQL is running
2. Verify user exists: Check database
3. Try demo credentials first

### Product search not working
1. Ensure products are active (not inactive)
2. Check inventory quantity > 0
3. Verify category is assigned

### Receipt not printing
1. Configure browser print settings
2. Check popup blocker isn't blocking
3. Ensure printer is connected

### Database connection error
```bash
# Run setup again
php init-db.php

# Check status
php health-check.php
```

## 📞 Support Resources

### Documentation
- See `/README.md` for full documentation
- Check code comments for specific features

### Database Reset
```bash
# Back up your data first!
php init-db.php

# This recreates all tables with demo data
```

## 🎯 Pro Tips

1. **Backup Regularly:** Export sales data weekly
2. **Monitor Stock:** Check low-stock alerts daily
3. **Review Reports:** Analyze sales trends weekly
4. **Update Prices:** Keep pricing current seasonally
5. **Manage Users:** Deactivate old staff accounts

## 🚀 Performance Tips

- Search is optimized with indexed database queries
- Product images stored in `/uploads/` folder
- Browser localStorage caches POS cart
- CSS variables enable instant theme changes

## 📈 Growth Features

As your business grows, you can easily:
- Add more product categories
- Create unlimited user accounts
- Process unlimited transactions
- Export historical data
- Generate custom reports

## ✨ Quality Assurance

This system has been tested for:
- ✅ SQL injection vulnerability
- ✅ XSS attacks
- ✅ CSRF attacks
- ✅ Session hijacking
- ✅ Concurrent transactions
- ✅ Data consistency

---

## Ready to Go! 🎉

You have a production-ready system. Start using it now:

```
http://localhost/shop_v2/
```

**Admin:** admin / password  
**Employee:** employee / password

---

**System Version:** 1.0.0  
**Status:** ✅ Production Ready  
**Last Updated:** 2026
