# Product Store Architecture & Flow Diagrams

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        WEB BROWSER                          │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │         products_store.php (UI Layer)              │  │
│  │                                                    │  │
│  │  ┌───────────┬──────────────────┬──────────────┐  │  │
│  │  │ Products  │  Search/Filter   │ Cart Sidebar │  │  │
│  │  │   Grid    │                  │              │  │  │
│  │  └───────────┴──────────────────┴──────────────┘  │  │
│  │                                                    │  │
│  │  JavaScript: Toggle cart on mobile                │  │
│  └──────────────────────────────────────────────────┘  │
│                          ↓                              │
│          form.submit() → add_to_cart.php               │
│                                                         │
└─────────────────────────────────────────────────────────┘
                           ↓
        ┌──────────────────────────────────────┐
        │    PHP Backend (Logic Layer)         │
        │                                      │
        │  ┌────────────────────────────────┐ │
        │  │   ProductManager Class         │ │
        │  │  - get_products()              │ │
        │  │  - get_product()               │ │
        │  │  - get_categories()            │ │
        │  └────────────────────────────────┘ │
        │                                      │
        │  ┌────────────────────────────────┐ │
        │  │   Session Management           │ │
        │  │  - $_SESSION['cart']           │ │
        │  │  - $_SESSION['user_id']        │ │
        │  └────────────────────────────────┘ │
        │                                      │
        └──────────────────────────────────────┘
                           ↓
        ┌──────────────────────────────────────┐
        │     Database Layer (MySQL)           │
        │                                      │
        │  ┌────────┐  ┌────────┐  ┌────────┐ │
        │  │Products│  │Categor.│  │  Users │ │
        │  │Table   │  │ Table  │  │ Table  │ │
        │  └────────┘  └────────┘  └────────┘ │
        │                                      │
        └──────────────────────────────────────┘
```

---

## 🔄 User Flow - Add to Cart

```
START: User visits store
  ↓
[Load products_store.php]
  ├─ Check session ($_SESSION['cart'])
  ├─ Query products from DB
  ├─ Get search/filter params
  └─ Build cart sidebar HTML
  ↓
[Render page]
  ├─ Display product grid
  ├─ Display search form
  └─ Display cart sidebar
  ↓
User enters quantity & clicks "Add to Cart"
  ↓
[POST to add_to_cart.php]
  ├─ Validate product_id
  ├─ Validate quantity
  ├─ Initialize $_SESSION['cart'] if needed
  ├─ Add/update cart: $_SESSION['cart'][$pid] += $qty
  └─ Redirect to cart.php
  ↓
[Redirect received]
  └─ Browser reloads page
  ↓
[Reload products_store.php]
  ├─ Regenerate cart items from new session
  ├─ Recalculate totals
  └─ Display updated cart sidebar
  ↓
User sees item in cart ✓
  ↓
END
```

---

## 📊 Product Display Flow

```
┌─────────────────────────────────────────┐
│   products_store.php                    │
└─────────────────────────────────────────┘
              ↓
    [Initialize ProductManager]
              ↓
    [Get Filter Parameters]
    ├─ $search = $_GET['search']
    ├─ $category_id = $_GET['category']
    └─ $products = $pm->get_products(cat, search)
              ↓
    [Database Query]
    ├─ SELECT p.*, c.category_name
    ├─ FROM products p
    ├─ LEFT JOIN categories c
    ├─ WHERE conditions applied
    └─ RETURN array of products
              ↓
    [Process Products]
    ├─ Loop through $products
    ├─ Create HTML for each:
    │  ├─ Product card
    │  ├─ Image
    │  ├─ Details
    │  └─ Add to cart form
    └─ Render in grid
              ↓
┌─────────────────────────────────────────┐
│        Display to User                  │
└─────────────────────────────────────────┘
```

---

## 🛒 Cart Calculation Flow

```
[User clicks "Add to Cart"]
              ↓
[POST to add_to_cart.php]
  product_id = 5
  quantity = 2
              ↓
[Initialize cart if empty]
  $_SESSION['cart'] = []
              ↓
[Check if product exists in cart]
  if (isset($_SESSION['cart'][5]))
    ↓ YES: Add to existing quantity
    $_SESSION['cart'][5] += 2
  else
    ↓ NO: Create new entry
    $_SESSION['cart'][5] = 2
              ↓
[Redirect back to store]
  header('Location: products_store.php')
              ↓
[products_store.php - Cart Calculation]
  ├─ $cart = $_SESSION['cart']
  │  Example: [5 => 2, 8 => 1]
  ├─ Loop through cart
  │  ├─ Get product details
  │  ├─ Calculate item_total
  │  │  item_total = price * quantity
  │  │  Example: 299.99 * 2 = 599.98
  │  └─ Add to subtotal
  │     subtotal += item_total
  ├─ Calculate shipping
  │  if (has_physical_items)
  │    shipping = 10
  └─ Calculate grand total
     total = subtotal + shipping
              ↓
[Display cart with updated totals]
  ├─ Subtotal: $599.98
  ├─ Shipping: $10.00
  └─ Total: $609.98
```

---

## 📱 Responsive Layout Flow

```
SCREEN SIZE CHECK
       ↓
   ┌───┴───┬─────────────┬────────────┐
   ↓       ↓             ↓            ↓
 <480px  480-768px   768-1024px   >1024px
   ↓       ↓             ↓            ↓
Mobile   Mobile+      Tablet       Desktop


┌──────────────────────────────────────┐
│ MOBILE (< 480px)                     │
├──────────────────────────────────────┤
│ Products: Single column              │
│ Cart: Hidden (floating button)       │
│ Filter: Full width                   │
│ 🛒 Button: Fixed bottom-right        │
└──────────────────────────────────────┘
          ↓
    User taps 🛒
          ↓
┌──────────────────────────────────────┐
│ MODAL CART                           │
├──────────────────────────────────────┤
│ [X] Your Cart                        │
│ ─────────────────────────────────── │
│ [Item 1] [Remove]                   │
│ [Item 2] [Remove]                   │
│ ─────────────────────────────────── │
│ Subtotal: $X.XX                     │
│ Shipping: $X.XX                     │
│ Total: $X.XX                        │
│ [View Full Cart] [Checkout]         │
└──────────────────────────────────────┘


┌──────────────────────────────────────┐
│ TABLET (768px - 1024px)              │
├──────────────────────────────────────┤
│ Products: 2-3 columns                │
│ Cart: Below products (full width)    │
│ Filter: Responsive form              │
│ Layout: Single column                │
└──────────────────────────────────────┘


┌───────────────────────────────────────────────────┐
│ DESKTOP (> 1024px)                                │
├───────────┬─────────────────────────────────────┤
│           │                                     │
│  CART     │                                     │
│ SIDEBAR   │     PRODUCTS GRID (4 columns)       │
│           │                                     │
│ Sticky    │  ┌─────────────────────────────┐   │
│ (350px)   │  │ Product 1  │ Product 2      │   │
│           │  │ Product 3  │ Product 4      │   │
│           │  │ Product 5  │ Product 6      │   │
│           │  └─────────────────────────────┘   │
│           │                                     │
│           │  [More products...]                 │
│           │                                     │
└───────────┴─────────────────────────────────────┘
```

---

## 🔐 Session Management Flow

```
USER JOURNEY:

[1] User arrives at site
    ↓
    [Session starts]
    $_SESSION = []
    ↓

[2] User logs in
    ↓
    $_SESSION['user_id'] = 123
    $_SESSION['role'] = 'customer'
    ↓

[3] User browses products
    ↓
    $_SESSION['cart'] = []  (initialized)
    ↓

[4] User adds item to cart
    ↓
    $_SESSION['cart'][1] = 1
    ↓

[5] User adds another item
    ↓
    $_SESSION['cart'][5] = 2
    Session state: [1 => 1, 5 => 2]
    ↓

[6] User removes item
    ↓
    unset($_SESSION['cart'][1])
    Session state: [5 => 2]
    ↓

[7] User logs out
    ↓
    session_destroy()
    All session data cleared
    $_SESSION = []
    ↓

[8] User redirected to homepage
    (Cart emptied, must log in again)
```

---

## 🎯 Component Interaction Diagram

```
┌────────────────────────────────────────────────────────┐
│              products_store.php                        │
├────────────────────────────────────────────────────────┤
│                                                        │
│  ┌──────────────────────┐  ┌──────────────────────┐   │
│  │  SEARCH/FILTER       │  │   PRODUCT GRID       │   │
│  │  COMPONENT           │  │   COMPONENT          │   │
│  │                      │  │                      │   │
│  │  ├─ Search input     │  │  ├─ Loop products   │   │
│  │  ├─ Category select  │  │  ├─ Product cards   │   │
│  │  ├─ Submit button    │  │  ├─ Add to cart     │   │
│  │  │                   │  │  │   forms          │   │
│  │  └─ GET params       │  │  └─ Submit → PHP    │   │
│  └──────────────────────┘  └──────────────────────┘   │
│            ↓ onChange              ↑ onSubmit        │
│            └─── Reload Page ─────←─┘               │
│                                                        │
│  ┌────────────────────────────────────────────────┐   │
│  │  CART SIDEBAR COMPONENT                        │   │
│  │                                                 │   │
│  │  ├─ Header "Your Cart"                         │   │
│  │  │  ├─ [X] Close button                        │   │
│  │  │                                              │   │
│  │  ├─ IF empty:                                  │   │
│  │  │  └─ Empty state message                     │   │
│  │  │                                              │   │
│  │  ├─ ELSE:                                      │   │
│  │  │  ├─ Cart items list                         │   │
│  │  │  │  └─ Loop: item, remove button            │   │
│  │  │  ├─ Cart summary                            │   │
│  │  │  │  ├─ Subtotal calculation                 │   │
│  │  │  │  ├─ Shipping calculation                 │   │
│  │  │  │  └─ Grand total                          │   │
│  │  │  └─ Action buttons                          │   │
│  │  │     ├─ View Full Cart                       │   │
│  │  │     └─ Checkout                             │   │
│  │  │                                              │   │
│  │  └─ [Dynamic updates from PHP]                 │   │
│  │                                                 │   │
│  └────────────────────────────────────────────────┘   │
│                                                        │
│  ┌────────────────────────────────────────────────┐   │
│  │  FLOATING CART BUTTON (Mobile only)            │   │
│  │                                                 │   │
│  │        🛒 (2)   ← Click to toggle              │   │
│  │      ↓                                          │   │
│  │  Shows cart count badge                        │   │
│  │  Opens/closes cart modal on click              │   │
│  │                                                 │   │
│  └────────────────────────────────────────────────┘   │
│                                                        │
└────────────────────────────────────────────────────────┘
```

---

## 📂 File Dependencies

```
products_store.php
    ├─ requires: includes/db_connection.php
    │            └─ mysqli connection
    │
    ├─ requires: includes/session.php
    │            ├─ BASE_PATH constant
    │            ├─ is_logged_in()
    │            └─ Session functions
    │
    ├─ requires: includes/product_manager.php
    │            ├─ class ProductManager
    │            ├─ get_products()
    │            ├─ get_categories()
    │            └─ get_product()
    │
    ├─ includes: assets/css/style.css
    │            └─ Base styles
    │
    ├─ includes: assets/css/store.css
    │            └─ Store-specific styles
    │
    ├─ forms submit to: add_to_cart.php
    │                   ├─ Updates $_SESSION['cart']
    │                   └─ Redirects to cart.php
    │
    └─ buttons link to: cart.php, checkout.php
```

---

## 🚀 Data Flow - Complete Journey

```
┌─────────────────────────────────────────────────────────┐
│                  USER WORKFLOW                          │
└─────────────────────────────────────────────────────────┘

[1] BROWSE PRODUCTS
    ┌──────────────────────────────────────┐
    │ User visits /products_store.php       │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ PHP loads:                           │
    │ - Session data                       │
    │ - ProductManager                     │
    │ - All products from DB               │
    │ - All categories                     │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ HTML rendered with:                  │
    │ - Product grid                       │
    │ - Search/filter form                 │
    │ - Empty cart display                 │
    └──────────────────────────────────────┘
           ↓

[2] SEARCH/FILTER
    ┌──────────────────────────────────────┐
    │ User enters search or selects category
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ Form submits GET params:             │
    │ ?search=guitar&category=2            │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ PHP queries DB with filters:         │
    │ WHERE product_name LIKE '%guitar%'   │
    │   AND category_id = 2                │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ Filtered results displayed           │
    └──────────────────────────────────────┘
           ↓

[3] ADD TO CART
    ┌──────────────────────────────────────┐
    │ User selects quantity & clicks       │
    │ "Add to Cart"                        │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ POST /add_to_cart.php:               │
    │ - product_id: 5                      │
    │ - quantity: 2                        │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ add_to_cart.php:                     │
    │ - Validate inputs                    │
    │ - $_SESSION['cart'][5] = 2           │
    │ - Redirect to cart.php               │
    └──────────────────────────────────────┘
           ↓

[4] PAGE RELOAD WITH UPDATED CART
    ┌──────────────────────────────────────┐
    │ products_store.php reloaded          │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ PHP processes:                       │
    │ - Read $_SESSION['cart'][5] = 2      │
    │ - Query product details (id=5)       │
    │ - Calculate: 299.99 × 2 = 599.98    │
    │ - Subtotal: $599.98                  │
    │ - Shipping: $10.00                   │
    │ - Total: $609.98                     │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ Cart sidebar displays:               │
    │ - Item thumbnail                     │
    │ - "Guitar Pro" × 2                   │
    │ - Total: $609.98                     │
    │ - Buttons: "View Full Cart"          │
    │          "Checkout"                  │
    └──────────────────────────────────────┘
           ↓

[5] CHECKOUT
    ┌──────────────────────────────────────┐
    │ User clicks "Checkout"               │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ Redirect to /checkout.php            │
    │ (Future implementation)              │
    └──────────────────────────────────────┘
           ↓
    ┌──────────────────────────────────────┐
    │ Order processed & confirmed          │
    └──────────────────────────────────────┘
```

---

**Architecture Version:** 1.0  
**Last Updated:** January 21, 2026  
**For:** Melody Masters Music Store
