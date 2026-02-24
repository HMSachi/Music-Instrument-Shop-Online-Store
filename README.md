# Melody Masters - Music Instrument Shop

A complete e-commerce platform for selling musical instruments built with PHP and MySQL.

## Features

✅ **User Management**
- Customer registration and login
- Role-based access (Admin, Staff, Customer)
- Profile management

✅ **Product Catalog**
- Browse products by categories
- Search functionality
- Product details with reviews and ratings
- Digital and physical products

✅ **Shopping Cart**
- Add/remove items
- Update quantities
- Real-time cart management

✅ **Order Management**
- Checkout process
- Order tracking
- Order history

✅ **Admin Panel**
- Dashboard with statistics
- Product management (CRUD)
- User management
- Order management with status updates

✅ **Responsive Design**
- Mobile-friendly interface
- Clean and modern UI
- Font Awesome icons

## Installation

### Prerequisites
- XAMPP (or any PHP/MySQL environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Instructions

1. **Start XAMPP**
   - Start Apache and MySQL servers

2. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `music_shop`
   - Import the database schema from `database/database.sql`

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'music_shop');
     ```

4. **Update Site URL**
   - Open `config/config.php`
   - Update the SITE_URL constant:
     ```php
     define('SITE_URL', 'http://localhost/Music-Instrument-Shop-Online-Store');
     ```

5. **Access the Application**
   - Open your browser and go to: http://localhost/Music-Instrument-Shop-Online-Store

## Default Admin Account

After importing the database, you can create an admin account by:

1. Register a new account through the registration page
2. Go to phpMyAdmin
3. Find your user in the `users` table
4. Change the `role` field to `admin`

OR run this SQL query (replace email with your registered email):
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

## Adding Sample Data

### Add Categories
```sql
INSERT INTO categories (category_name) VALUES 
('Guitars'),
('Keyboards'),
('Drums'),
('Accessories'),
('Digital Sheet Music');
```

### Add Sample Products
```sql
INSERT INTO products (category_id, product_name, brand, description, price, stock, product_type, image) VALUES
(1, 'Acoustic Guitar', 'Yamaha', 'Professional acoustic guitar with rich sound quality', 15000.00, 10, 'physical', 'assets/images/guitar.jpg'),
(2, 'Digital Piano', 'Casio', '88-key digital piano with weighted keys', 35000.00, 5, 'physical', 'assets/images/piano.jpg'),
(3, 'Drum Set', 'Pearl', 'Complete 5-piece drum set for beginners', 25000.00, 3, 'physical', 'assets/images/drums.jpg'),
(4, 'Guitar Strings', 'Ernie Ball', 'Premium guitar strings set', 500.00, 50, 'physical', 'assets/images/strings.jpg');
```

## Project Structure

```
Music-Instrument-Shop-Online-Store/
├── admin/                      # Admin panel pages
│   ├── dashboard.php
│   ├── manage_products.php
│   ├── manage_users.php
│   └── manage_orders.php
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # JavaScript functionality
│   └── images/                # Product images
├── config/
│   ├── config.php             # General configuration
│   └── database.php           # Database connection
├── customer/
│   └── dashboard.php          # Customer dashboard
├── database/
│   └── database.sql           # Database schema
├── includes/
│   ├── header.php             # Header template
│   └── footer.php             # Footer template
├── staff/
│   └── dashboard.php          # Staff dashboard
├── cart.php                   # Shopping cart
├── checkout.php               # Checkout process
├── index.php                  # Homepage
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── product.php                # Product details
├── register.php               # Registration page
└── shop.php                   # Products listing
```

## Usage

### For Customers
1. Register an account or login
2. Browse products by categories or search
3. View product details and reviews
4. Add items to cart
5. Proceed to checkout
6. Track orders in dashboard

### For Admin
1. Login with admin credentials
2. Access admin dashboard
3. Manage products (add, edit, delete)
4. Manage users and their roles
5. Process and update order status
6. View sales statistics

### For Staff
1. Login with staff credentials
2. View and manage orders
3. Update order status

## Technologies Used

- **Backend:** PHP (Procedural)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3 (Flexbox & Grid)
- **JavaScript:** Vanilla JS
- **Icons:** Font Awesome 6
- **Session Management:** PHP Sessions

## Security Features

- Password hashing using PHP `password_hash()`
- SQL injection prevention with `real_escape_string()`
- Session-based authentication
- Role-based access control
- Input validation and sanitization

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Future Enhancements

- Payment gateway integration
- Email notifications
- Product reviews submission
- Wishlist functionality
- Advanced search filters
- Product recommendations
- Sales reports and analytics
- Multi-language support
- Social media integration

## Support

For issues or questions, please contact the administrator.

## License

This project is for educational purposes.

---

**Developed by:** Your Name  
**Date:** February 2026  
**Version:** 1.0.0
