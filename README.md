# Melody Masters - Online Music Instrument Shop
## Complete Web Application System

A full-stack web application for managing an online music instrument shop with admin, staff, and customer roles.

---

## 📋 Table of Contents
- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation & Setup](#installation--setup)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [User Roles & Access](#user-roles--access)
- [Database Schema](#database-schema)
- [Configuration](#configuration)
- [Testing Credentials](#testing-credentials)

---

## ✨ Features

### Admin Features
- Complete product management (Add, Edit, Delete products)
- View and manage all registered users
- Assign user roles (Admin, Staff, Customer)
- Deactivate user accounts
- Access to all system functions

### Staff Features
- View all products
- Update inventory/stock quantities
- View and process customer orders
- Update order status (Pending, Processing, Shipped, Delivered, Cancelled)
- Access order details and customer information

### Customer Features
- Browse products by category
- Search products
- View detailed product information with customer reviews
- Add products to shopping cart
- Manage shopping cart (update quantities, remove items)
- Checkout and place orders
- View order history and order details
- Free shipping on orders over £100
- Digital product instant download access
- View product ratings and reviews

### General Features
- Secure user authentication (password hashing with bcrypt)
- Session-based login system
- Role-based access control (RBAC)
- Responsive design (works on desktop, tablet, mobile)
- Product categorisation
- Physical and digital product support
- Order management system
- Real-time inventory tracking
- User-friendly interface with Flexbox/Grid layouts

---

## 💻 System Requirements

- Web Server: Apache with PHP support
- PHP Version: 7.4 or higher
- Database: MySQL 5.7 or higher
- Browser: Modern browser (Chrome, Firefox, Safari, Edge)

Recommended: XAMPP (Apache, PHP, MySQL)

---

## 🚀 Installation & Setup

### 1) Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin).
2. Create database `melody_masters` with collation `utf8mb4_unicode_ci`.
3. Import `database/database.sql` into `melody_masters`.

### 2) File Structure
Place the project in:
```
c:/xampp/htdocs/Music-Instrument-Shop-Online-Store/
```

Ensure the structure:
```
admin/
assets/css/style.css
assets/images/products/
customer/
database/database.sql
includes/auth.php
includes/db_connection.php
includes/order_manager.php
includes/product_manager.php
includes/session.php
public/logout.php
staff/
cart.php
checkout.php
index.php
login.php
order_confirmation.php
product.php
products.php
signup.php
README.md
```

### 3) Configure Database Connection
Edit `includes/db_connection.php` if needed:
```php
$host = 'localhost';
$dbname = 'melody_masters';
$username = 'root';
$password = '';
```

### 4) Product Images
Create `assets/images/products/` and place product images matching filenames in the database seeds.

### 5) Run the App
1. Start Apache and MySQL via XAMPP.
2. Visit: http://localhost/Music-Instrument-Shop-Online-Store/

---

## 🛠 Technology Stack

- Frontend: HTML5, CSS3 (Flexbox/Grid), minimal JavaScript
- Backend: PHP 7.4+
- Database: MySQL
- Auth: Session-based login with bcrypt password hashing
- Access Control: Role-Based Access Control (Admin, Staff, Customer)

---

## 📂 Project Structure (Key Files)

- index.php — Homepage with featured products
- login.php / signup.php — Auth flows with role selection
- products.php — Product listing with search/filter
- product.php — Product detail with reviews
- cart.php — Shopping cart
- checkout.php — Order placement
- order_confirmation.php — Confirmation page
- admin/dashboard.php — Product CRUD
- admin/edit_product.php — Edit product
- admin/users.php — User management and roles
- staff/dashboard.php — Orders + stock updates
- staff/order_details.php — Order detail
- customer/dashboard.php — Account + order history
- customer/order_details.php — Customer order detail
- includes/*.php — DB connection, session, auth, products, orders handlers
- assets/css/style.css — Responsive styling

---

## 👥 User Roles & Access

### Admin
- Full access: product CRUD, user management, inventory oversight
- Redirect: /admin/dashboard.php

### Staff
- Manage inventory, process orders, view products
- No user management
- Redirect: /staff/dashboard.php

### Customer
- Browse, cart, checkout, order history, digital downloads
- Redirect: /customer/dashboard.php

### Guest
- Browse products and details only; must log in to purchase.

---

## 🗄️ Database Schema

### users
- id (PK), name, email (unique), password (hashed), role (Admin/Staff/Customer), is_active, created_at

### categories
- id (PK), name (unique), description, created_at

### products
- id (PK), name, category_id (FK), price, stock, description, image, type (Physical/Digital), is_active, timestamps

### orders
- id (PK), user_id (FK), total_price, shipping_cost, order_date, status, delivery_address, phone

### order_items
- id (PK), order_id (FK), product_id (FK), quantity, price

### digital_products
- id (PK), product_id (FK, unique), download_url, file_size, license_terms

### reviews
- id (PK), product_id (FK), user_id (FK), rating (1-5), review_text, is_verified_purchase, created_at, UNIQUE(user_id, product_id)

---

## ⚙️ Configuration

- DB credentials: `includes/db_connection.php`
- Sessions: `includes/session.php`
- Shipping: free over £100; physical items only; otherwise £15
- Password policy: ≥8 chars, includes uppercase and number

---

## 🧪 Testing Credentials

Seed users (from SQL):
- admin@melodymaster.com — role Admin — set your own password via signup/reset
- staff@melodymaster.com — role Staff — set your own password via signup/reset

Create additional accounts via signup and choose role.

---

## 🔐 Security Features

- Bcrypt password hashing
- Session-based auth with role checks
- Input sanitisation helpers
- Foreign keys for referential integrity
- Role-based access control on protected pages

---

## 📝 Usage Examples

- Add product (Admin): Admin Dashboard → Add product form
- Update stock (Staff): Staff Dashboard → Inventory table → Update stock
- Place order (Customer): Browse → Add to cart → Checkout → Confirm
- Order tracking: Customer Dashboard → Order history → Details

---

## 🐛 Troubleshooting

- DB connection errors: verify MySQL running and credentials correct.
- Images missing: ensure `assets/images/products/` has referenced files.
- Auth issues: confirm user exists and is_active=TRUE.
- Cart/session issues: ensure cookies and PHP sessions are enabled.

---

## 📊 Business Rules Implemented

- Free shipping on orders > £100 (physical items) else £15
- Shipping applies only to physical products
- Digital products: available for download after payment (placeholder link support)
- Role-based access enforced for Admin/Staff/Customer
- Stock decremented on confirmed orders

---

## 🚀 Future Enhancements

- Payment gateway integration (Stripe/PayPal)
- Email notifications
- Discount codes
- Enhanced reviews with moderation
- Wishlists and recommendations
- Multi-language support

---

## ✅ Project Completion Checklist

- Database schema and seed data
- Auth with role-based redirects
- Admin product CRUD
- Staff order + inventory tools
- Customer shopping, cart, checkout, history
- Responsive UI (Flexbox/Grid)
- Security basics (hashing, sanitisation, sessions)

---

**Version:** 1.0  
**Last Updated:** January 20, 2026
