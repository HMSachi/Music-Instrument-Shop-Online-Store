# Quick Start Guide - Melody Masters

## 🚀 Getting Started in 5 Minutes

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Click **Start** for Apache
3. Click **Start** for MySQL

### Step 2: Import Database
1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click **New** to create database
3. Name it: `melody_masters`
4. Click on `melody_masters` database
5. Click **Import** tab
6. Choose file: `database/music_shop.sql`
7. Click **Go**

### Step 3: Test the Application
1. Open: `http://localhost/Music-Instrument-Shop-Online-Store`
2. You should see the homepage!

## 🔐 Default Login Credentials

### Admin Account
- **Email:** admin@melodymaster.com
- **Password:** admin123
- **Access:** Full admin panel

### Staff Account
- **Email:** staff@melodymaster.com
- **Password:** staff123
- **Access:** Order management

### Customer Account
- **Email:** customer@example.com
- **Password:** customer123
- **Access:** Shopping and orders

## 📝 Quick Tasks

### Test Shopping Flow
1. Browse products on homepage
2. Click "Shop" to see all products
3. Click on a product to view details
4. Add to cart
5. View cart and proceed to checkout
6. Login if not logged in
7. Complete order

### Test Admin Panel
1. Login as admin
2. Go to Admin Dashboard
3. Try adding a new product
4. View and manage users
5. Update order status

## 🎨 Adding Your Own Products

### Via Admin Panel (Easy Way)
1. Login as admin
2. Go to **Manage Products**
3. Fill the form:
   - Product Name
   - Brand
   - Category
   - Price
   - Stock
   - Type (Physical/Digital)
   - Description
   - Image URL
4. Click **Add Product**

### Via Database (Direct Way)
```sql
INSERT INTO products (category_id, product_name, brand, description, price, stock, product_type) 
VALUES (1, 'Your Product', 'Your Brand', 'Description', 10000.00, 10, 'physical');
```

## 📷 Adding Product Images

### Option 1: Use Image URLs
- In the image field, you can use full URLs:
  - Example: `https://example.com/image.jpg`

### Option 2: Upload to assets/images
1. Copy your image to: `assets/images/`
2. Use path: `assets/images/yourimage.jpg`

### Option 3: Use Placeholder
- Leave image field empty
- System will use placeholder automatically

## 🛠️ Common Issues & Solutions

### Issue: Page Not Loading
**Solution:** Check if XAMPP Apache is running

### Issue: Database Error
**Solution:** 
1. Check if MySQL is running
2. Verify database name is `melody_masters`
3. Check credentials in `config/database.php`

### Issue: CSS Not Loading
**Solution:** 
1. Check SITE_URL in `config/config.php`
2. Should be: `http://localhost/Music-Instrument-Shop-Online-Store`

### Issue: Can't Login
**Solution:**
1. Clear browser cache
2. Check if session is enabled (should be by default)
3. Try using the default credentials above

## 🎯 Testing Checklist

- [ ] Homepage loads correctly
- [ ] Can browse products
- [ ] Search works
- [ ] Can add items to cart
- [ ] Cart displays correctly
- [ ] Can login/register
- [ ] Customer dashboard shows orders
- [ ] Admin can add products
- [ ] Admin can manage users
- [ ] Admin can update order status

## 📊 Sample Data Included

The database includes:
- ✅ 5 Categories
- ✅ 12 Sample Products
- ✅ 3 User Accounts (Admin, Staff, Customer)
- ✅ 1 Sample Order
- ✅ 4 Product Reviews

## 🔄 Reset Database

If you want to start fresh:
1. Go to phpMyAdmin
2. Select `melody_masters` database
3. Click **Drop** to delete
4. Re-import `database/music_shop.sql`

## 💡 Next Steps

1. **Customize Styling:** Edit `assets/css/style.css`
2. **Add More Products:** Use admin panel
3. **Test All Features:** Use different user roles
4. **Add Real Images:** Replace placeholder images
5. **Customize Content:** Update text in PHP files

## 📞 Need Help?

Common Pages:
- Homepage: `index.php`
- Shop: `shop.php`
- Product Details: `product.php`
- Cart: `cart.php`
- Admin: `admin/dashboard.php`

Configuration Files:
- Database: `config/database.php`
- General: `config/config.php`

## ✅ You're All Set!

Your complete music instrument shop is ready to use!

Happy coding! 🎵🎸🎹
