# Quick Reference: Product Store Page

## 📍 Access Points

| Page | URL | Description |
|------|-----|-------------|
| Store | `/products_store.php` | Main shopping interface with cart |
| Browse | `/products.php` | Category-based product browser |
| Cart | `/cart.php` | Full cart view and management |
| Product Details | `/product.php?id=X` | Single product page |

---

## 🔑 Key Variables & Functions

### PHP Session Variables
```php
$_SESSION['user_id']        // Logged-in user ID
$_SESSION['role']           // User role (customer, staff, admin)
$_SESSION['cart']           // Cart items array
$_SESSION['cart'][$pid]     // Quantity for product $pid
```

### ProductManager Methods
```php
$pm = new ProductManager($db);
$pm->get_products($category_id, $search)  // Get filtered products
$pm->get_product($product_id)             // Get single product
$pm->get_categories()                     // Get all categories
```

### Session Functions
```php
is_logged_in()              // Check if user logged in
has_role($role)             // Check user role
require_customer()          // Redirect if not customer
BASE_PATH                   // Global base path constant
```

---

## 🎯 CSS Classes

### Layout
- `.store-wrapper` - Main flex container
- `.store-main` - Products section
- `.cart-sidebar` - Cart panel

### Products
- `.products-grid-store` - Product grid container
- `.product-card-store` - Individual product card
- `.product-image-store` - Product image container
- `.product-info-store` - Product details area
- `.product-price-store` - Price display

### Cart
- `.cart-items-list` - List of cart items
- `.cart-item` - Single cart item
- `.cart-summary` - Totals section
- `.cart-toggle-btn` - Mobile floating button

---

## 📊 Product Data Structure

```php
[
    'product_id' => 1,
    'product_name' => 'Acoustic Guitar',
    'brand' => 'Yamaha',
    'description' => 'High-quality...',
    'price' => 299.99,
    'stock' => 5,
    'product_type' => 'physical',
    'image' => 'guitar.jpg',
    'category_id' => 2,
    'category_name' => 'Guitars'
]
```

## 🛒 Cart Item Structure

```php
[
    'product_id' => 1,
    'product_name' => 'Acoustic Guitar',
    'price' => 299.99,
    'quantity' => 2,
    'item_total' => 599.98,
    'image' => 'guitar.jpg',
    'type' => 'physical'
]
```

---

## 🔄 Form Submission Flows

### Add to Cart
```
POST /add_to_cart.php
├── product_id (int)
└── quantity (int)
↓
Redirect to /cart.php
```

### Remove from Cart
```
POST /remove_from_cart.php
└── product_id (int)
↓
Redirect to /cart.php (or referrer)
```

---

## 🎨 Responsive Breakpoints

```css
/* Mobile */
< 480px     → Single column, floating cart

/* Tablet */
480-768px   → 2 columns, full-width cart

/* Small Desktop */
768-1024px  → 3 columns, sidebar cart

/* Desktop */
> 1024px    → 4 columns, sticky sidebar
```

---

## 🚨 Error Handling

### Database Errors
```php
if (!$result) {
    die('Database error: ' . $db->error);
}
```

### Session Errors
```php
if (!is_logged_in()) {
    header('Location: ' . BASE_PATH . '/login.php');
}
```

### Invalid Product
```php
if (!$product) {
    echo "Product not found";
}
```

---

## 🔐 Input Validation

### Product ID
```php
$product_id = (int) $_POST['product_id'];
```

### Quantity
```php
$quantity = (int) ($_POST['quantity'] ?? 1);
if ($quantity <= 0) $quantity = 1;
```

### Search
```php
$search = htmlspecialchars($_GET['search'] ?? '');
```

---

## 💾 Database Queries

### Get Products with Search
```sql
SELECT p.*, c.category_name FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
WHERE (p.product_name LIKE '%search%' 
   OR p.description LIKE '%search%')
ORDER BY p.created_at DESC
```

### Calculate Cart Totals
```php
foreach ($cart as $pid => $qty) {
    $item_total = $price * $qty;
    $subtotal += $item_total;
}
```

---

## 🎯 Common Tasks

### Add New Feature
1. Edit `products_store.php`
2. Add PHP logic in main section
3. Add HTML in template section
4. Add CSS in `store.css`
5. Test on all screen sizes

### Change Colors
1. Edit `:root` in `style.css`
2. Update primary, accent colors
3. Test for contrast accessibility

### Customize Grid
1. Edit `.products-grid-store` in `store.css`
2. Change `grid-template-columns` value
3. Adjust `minmax()` values

### Add New Product Type
1. Add to database `product_type` ENUM
2. Update `.product-type-badge` display
3. Add conditional stock logic
4. Test add-to-cart flow

---

## 🧪 Testing Checklist

- [ ] Load store page (unlogged, logged-in)
- [ ] Search products
- [ ] Filter by category
- [ ] Add to cart (update count)
- [ ] View cart sidebar
- [ ] Remove from cart
- [ ] Check cart totals
- [ ] Test on desktop (1920px)
- [ ] Test on tablet (768px)
- [ ] Test on mobile (375px)
- [ ] Test on very small mobile (320px)
- [ ] Test out-of-stock products
- [ ] Test digital products
- [ ] Test with no search results
- [ ] Test quantity limits

---

## 🐛 Debug Tips

### Check Session
```php
echo '<pre>'; print_r($_SESSION); echo '</pre>';
```

### Check Products Array
```php
echo '<pre>'; print_r($products); echo '</pre>';
```

### Check Cart Calculation
```php
echo "Subtotal: " . $subtotal;
echo "Shipping: " . $shipping;
echo "Total: " . $total;
```

### Browser Console (F12)
```javascript
// Check JavaScript errors
console.log('Cart sidebar open');
console.log(document.getElementById('cartSidebar'));
```

---

## 📱 Mobile Testing Tools

- **Chrome DevTools:** F12 → Toggle device toolbar
- **Firefox DevTools:** F12 → Responsive Design Mode
- **BrowserStack:** Cross-browser testing
- **Ghost Inspector:** Automated testing

---

## 🔗 Related Files Reference

```
Includes/
├── db_connection.php        DB config & connection
├── session.php             User auth & BASE_PATH
└── product_manager.php     Product queries

Root/
├── products_store.php      NEW - Main store page
├── products.php            Browse page
├── cart.php                Full cart view
├── add_to_cart.php         Cart handler
└── remove_from_cart.php    Remove handler

Assets/
└── css/
    ├── style.css           Base styles
    └── store.css           NEW - Store layout
```

---

## 🚀 Performance Metrics

### Target Load Times
- Store page: < 2 seconds
- Search results: < 1 second
- Add to cart: < 500ms

### Optimization Tips
- Compress images < 200KB
- Minify CSS < 50KB
- Use database indexes
- Enable gzip compression

---

## 📋 File Checklist

Required Files:
- [x] `products_store.php` - Main store
- [x] `assets/css/store.css` - Styling
- [x] `includes/db_connection.php` - DB
- [x] `includes/session.php` - Sessions
- [x] `includes/product_manager.php` - Queries
- [x] `add_to_cart.php` - Cart handler
- [x] `remove_from_cart.php` - Remove handler
- [x] `cart.php` - Full cart view

Optional Files:
- [ ] `checkout.php` - Checkout (future)
- [ ] `/assets/images/products/` - Product images

---

## 💡 Pro Tips

1. **Use DevTools:** Inspect elements to understand layout
2. **Test Empty States:** Check behavior with no products
3. **Mobile First:** Test mobile version first
4. **Accessibility:** Use keyboard navigation
5. **Performance:** Monitor in Network tab
6. **Console:** Check for JavaScript errors
7. **Database:** Use phpMyAdmin to verify data
8. **Sessions:** Check with `<?php var_dump($_SESSION); ?>`

---

**Last Updated:** January 21, 2026  
**For:** Melody Masters Music Store  
**Version:** 1.0
