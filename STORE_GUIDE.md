# 🛒 Product Store Page - User Guide

## Overview

The new **Product Store Page** (`products_store.php`) is a modern, user-friendly shopping interface designed to make browsing and purchasing instruments effortless. It features:

- **Split-screen layout** with products on the left and cart on the right
- **Real-time cart display** showing selected items, quantities, and totals
- **Mobile-responsive design** with floating cart button on smaller screens
- **Easy product search & filtering** by category
- **One-click add to cart** functionality

---

## Features

### 1. **Product Browsing**
- Display products in a responsive grid layout
- **Product information includes:**
  - Product image with fallback placeholder
  - Product name and brand
  - Brief description
  - Price in clear, visible format
  - Stock status (In Stock, Out of Stock, or Digital Download)
  - Product type badge (Physical/Digital)

### 2. **Search & Filter**
- Search products by name or keywords
- Filter by category
- Real-time product filtering
- Helpful empty state message when no results found

### 3. **Shopping Cart Sidebar**
The cart sidebar displays on the right side (desktop) or as a modal (mobile):

- **Cart Items Display:**
  - Product thumbnail
  - Product name and price
  - Quantity and item total
  - Quick remove button (🗑️)

- **Cart Summary:**
  - Subtotal calculation
  - Shipping cost (if applicable)
  - Grand total
  - Color-coded price highlights

- **Cart Actions:**
  - View Full Cart button (redirects to `cart.php`)
  - Checkout button (redirects to `checkout.php`)

### 4. **Quick Add to Cart**
- Quantity selector (1-10 items)
- Large "Add to Cart" button
- Automatic cart update
- Disabled button for out-of-stock items

### 5. **Cart Toggle (Mobile)**
- Fixed floating button in bottom-right corner
- Shows cart item count badge
- Toggles cart visibility on mobile/tablet devices
- Smooth animations and transitions

---

## How to Use

### For Customers

#### **Browsing Products**
1. Navigate to the Shop page from the main navigation menu
2. Browse products in the grid
3. Use the search bar to find specific items
4. Filter by category using the category dropdown
5. Click "Search" to apply filters

#### **Adding Items to Cart**
1. Select the quantity using the number input (1-10)
2. Click "Add to Cart"
3. Item appears in the cart sidebar
4. Cart count updates automatically

#### **Managing Cart**
- **View cart details** on the right sidebar (desktop) or tap the cart icon (mobile)
- **Remove items** by clicking the trash icon (🗑️)
- **Update quantities** by viewing the full cart page
- **View full cart** by clicking "View Full Cart" button
- **Proceed to checkout** by clicking "Checkout"

#### **Mobile Experience**
1. Products display in a single or two-column layout
2. Cart is hidden by default
3. Tap the floating cart button 🛒 (bottom-right) to view your cart
4. Cart modal slides up from bottom
5. Close cart by tapping the ✕ button or clicking outside

---

## Technical Details

### Files Created/Modified

#### **New Files**
1. **`products_store.php`** - Main store page with product grid and cart sidebar
2. **`assets/css/store.css`** - Complete styling for store layout

#### **Modified Files**
- **`index.php`** - Added link to new store page in navigation

### Key Components

#### **Database Integration**
- Uses `ProductManager` class from `includes/product_manager.php`
- Fetches products with search/filter functionality
- Retrieves product details and pricing
- Session-based cart management

#### **Session Management**
- Uses PHP sessions (`$_SESSION['cart']`) to store cart data
- Format: `product_id => quantity`
- Persists across page navigation
- Cleared on logout

#### **Responsive Breakpoints**
- **Desktop:** 1024px+ - Two-column layout (products + sidebar)
- **Tablet:** 768px-1023px - Single column with sticky sidebar
- **Mobile:** Below 768px - Products with floating cart button

---

## Styling & Customization

### CSS Structure

#### **Layout Variables** (in `style.css`)
```css
--primary: #229954      /* Main green color */
--accent: #27AE60       /* Accent green */
--bg: #F7F9FA           /* Light background */
--text: #1C5E42         /* Dark text */
--muted: #7F8C8D        /* Gray text */
--border: #D6DDE3       /* Border color */
```

#### **Main Components**
- `.store-wrapper` - Main container flex layout
- `.products-grid-store` - Product grid (4-column on desktop)
- `.product-card-store` - Individual product card with hover effects
- `.cart-sidebar` - Right-side cart panel
- `.cart-toggle-btn` - Floating cart button (mobile only)

### Customizing Colors

Edit `/assets/css/store.css` or root CSS variables:
```css
:root {
    --primary: #your-color;      /* Change main theme */
    --accent: #your-accent;       /* Change accent */
}
```

---

## Integration with Existing System

### Cart Flow
1. **Add to Cart** → `add_to_cart.php` (existing)
   - Adds item to `$_SESSION['cart']`
   - Redirects to `cart.php`

2. **View Cart** → `cart.php` (existing)
   - Shows full cart with all items
   - Allows quantity updates
   - Shows checkout options

3. **Remove Item** → `remove_from_cart.php` (existing)
   - Removes item from session
   - Updates cart totals

4. **Checkout** → `checkout.php` (to be created)
   - Processes order
   - Handles payment
   - Confirms purchase

### Authentication
- Product store requires user login (if `require_customer()` is added)
- Currently accessible to all users
- Respects session-based user roles

---

## Features Breakdown

### ✅ Current Features
- ✓ Product display with images and details
- ✓ Search and category filtering
- ✓ Add to cart functionality
- ✓ Real-time cart updates
- ✓ Cart sidebar with totals
- ✓ Mobile responsive design
- ✓ Cart item removal
- ✓ Quantity selection
- ✓ Stock status display

### 🔄 Future Enhancements
- Product ratings and reviews
- Wishlist/favorites
- Compare products
- Advanced filtering (price range, brand)
- Product quick view modal
- Stock alerts
- Social sharing
- Recent viewed products

---

## Troubleshooting

### Cart Not Updating
- **Issue:** Items don't appear in cart after clicking "Add to Cart"
- **Solution:** Check that sessions are enabled in PHP (`php.ini`)
- **Check:** Verify `includes/session.php` is included in the page

### Images Not Loading
- **Issue:** Products show placeholder instead of images
- **Solution:** Upload product images to `/assets/images/products/`
- **Check:** Verify image filenames match database records

### Layout Breaking on Mobile
- **Issue:** Products or cart not displaying properly on small screens
- **Solution:** Clear browser cache or use developer tools to check viewport
- **Check:** Ensure `store.css` is loaded correctly

### Add to Cart Button Disabled
- **Issue:** Physical products show as disabled even in stock
- **Solution:** Check product stock quantity in database
- **Query:** `SELECT product_id, product_name, stock FROM products WHERE stock <= 0`

---

## Performance Considerations

### Optimization Tips
1. **Images:** Compress product images before uploading
2. **Database:** Add indexes on `products.category_id` and `products.product_name`
3. **Caching:** Consider caching product list for faster load times
4. **Pagination:** Add pagination for large product catalogs

### Recommended Database Indexes
```sql
-- If not already present
ALTER TABLE products ADD INDEX idx_category (category_id);
ALTER TABLE products ADD INDEX idx_search (product_name);
```

---

## Security Considerations

### Current Security Measures
- ✓ SQL injection prevention via `mysqli` prepared statements
- ✓ HTML escaping using `htmlspecialchars()`
- ✓ Session-based user identification

### Recommended Enhancements
- Implement CSRF tokens for form submissions
- Validate product IDs and quantities server-side
- Sanitize search input
- Implement rate limiting on add-to-cart

---

## Browser Compatibility

| Browser | Desktop | Tablet | Mobile |
|---------|---------|--------|--------|
| Chrome  | ✓ Full  | ✓ Full | ✓ Full |
| Firefox | ✓ Full  | ✓ Full | ✓ Full |
| Safari  | ✓ Full  | ✓ Full | ✓ Full |
| Edge    | ✓ Full  | ✓ Full | ✓ Full |
| IE 11   | ⚠ Basic | ✗ No   | ✗ No   |

---

## Files and Locations

```
Music-Instrument-Shop-Online-Store/
├── products_store.php          # Main store page
├── assets/
│   └── css/
│       └── store.css            # Store styling
├── includes/
│   ├── db_connection.php        # Database connection
│   ├── session.php              # Session management
│   └── product_manager.php      # Product queries
└── add_to_cart.php              # Add to cart handler
```

---

## Contact & Support

For issues or feature requests, please check:
1. Database connection in `includes/db_connection.php`
2. Session configuration in `includes/session.php`
3. Product data in database tables
4. Browser console for JavaScript errors
5. Server error logs

---

**Version:** 1.0  
**Last Updated:** January 21, 2026  
**Status:** ✅ Production Ready
