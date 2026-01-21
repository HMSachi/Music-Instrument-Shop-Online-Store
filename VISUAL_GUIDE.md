# 🎵 Product Store - Visual Guide & Screenshots

## 📸 Layout Overview

### Desktop View (1024px+)

```
┌─────────────────────────────────────────────────────────────────┐
│  HEADER: Melody Masters    Shop  Browse  Admin  Logout           │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  🎵 Shop Instruments                                              │
│  Browse our collection and add items to your cart                │
│                                                                   │
│  ┌─────────────────────────┐  ┌──────────────────────────────┐  │
│  │ Search: [_________]     │  │ Your Cart          🛒 (0)    │  │
│  │ Category: [Guitars▼]    │  ├──────────────────────────────┤  │
│  │ [SEARCH]                │  │ Your cart is empty           │  │
│  └─────────────────────────┘  │ 🛍️                           │  │
│                                │ Your cart is empty           │  │
│  ┌─────────┐  ┌─────────┐    │ Add items to get started!    │  │
│  │         │  │         │    │                              │  │
│  │Product 1│  │Product 2│    │                              │  │
│  │         │  │         │    │                              │  │
│  │$299.99  │  │$149.99  │    │                              │  │
│  │✓ In Stock│ │✓ In Stock│   │                              │  │
│  │[ADD 1]  │  │[ADD 1]  │    │                              │  │
│  └─────────┘  ┌─────────┐    │                              │  │
│               │         │    │                              │  │
│  ┌─────────┐  │Product 3│    │ [VIEW FULL CART]             │  │
│  │         │  │         │    │ [CHECKOUT]                   │  │
│  │Product 4│  │$89.99   │    │                              │  │
│  │         │  │📱 Digital│   └──────────────────────────────┘  │
│  │$199.99  │  │[ADD 1]  │                                      │
│  │✓ In Stock│ └─────────┘                                      │
│  │[ADD 1]  │                                                    │
│  └─────────┘                                                    │
│                                                                   │
│  [More products...]                                              │
│                                                                   │
├─────────────────────────────────────────────────────────────────┤
│ © 2026 Melody Masters. All rights reserved.                     │
└─────────────────────────────────────────────────────────────────┘
```

---

### Tablet View (768px)

```
┌────────────────────────────────────────┐
│ HEADER: Melody Masters    Shop  Browse │
├────────────────────────────────────────┤
│                                        │
│ 🎵 Shop Instruments                    │
│                                        │
│ ┌──────────────────────────────────┐  │
│ │ Search: [___________]            │  │
│ │ Category: [Guitars▼]  [SEARCH]  │  │
│ └──────────────────────────────────┘  │
│                                        │
│ ┌─────────────┐ ┌─────────────┐      │
│ │  Product 1  │ │  Product 2  │      │
│ │             │ │             │      │
│ │   $299.99   │ │   $149.99   │      │
│ │ ✓ In Stock  │ │ ✓ In Stock  │      │
│ │ [ADD 1]     │ │ [ADD 1]     │      │
│ └─────────────┘ └─────────────┘      │
│                                        │
│ ┌─────────────┐ ┌─────────────┐      │
│ │  Product 3  │ │  Product 4  │      │
│ │             │ │             │      │
│ │    $89.99   │ │   $199.99   │      │
│ │ 📱 Digital  │ │ ✓ In Stock  │      │
│ │ [ADD 1]     │ │ [ADD 1]     │      │
│ └─────────────┘ └─────────────┘      │
│                                        │
├────────────────────────────────────────┤
│           YOUR CART (2 items)          │
├────────────────────────────────────────┤
│ [Thumb] Product 1                     │
│         $299.99 × 1                   │
│         Total: $299.99        [🗑️]    │
│ [Thumb] Product 2                     │
│         $149.99 × 1                   │
│         Total: $149.99        [🗑️]    │
│                                        │
│ Subtotal:  $449.98                    │
│ Shipping:  $10.00                     │
│ Total:     $459.98                    │
│                                        │
│ [VIEW FULL CART]                      │
│ [CHECKOUT]                             │
│                                        │
└────────────────────────────────────────┘
```

---

### Mobile View (< 480px)

```
┌─────────────────────────┐
│ Melody Masters   Menu   │
├─────────────────────────┤
│                         │
│ 🎵 Shop Instruments     │
│                         │
│ [Search ________] [Go]  │
│ [All Categories  ▼]     │
│                         │
│ ┌─────────────────────┐ │
│ │  Product 1          │ │
│ │  [Image]            │ │
│ │  Guitar Pro         │ │
│ │  by Yamaha          │ │
│ │  High-quality...    │ │
│ │  $299.99            │ │
│ │  [1] [ADD CART]     │ │
│ └─────────────────────┘ │
│                         │
│ ┌─────────────────────┐ │
│ │  Product 2          │ │
│ │  [Image]            │ │
│ │  Drum Kit           │ │
│ │  by Ludwig          │ │
│ │  Professional...    │ │
│ │  $899.99            │ │
│ │  [1] [ADD CART]     │ │
│ └─────────────────────┘ │
│                         │
│ [More products...]      │
│                         │
│                    🛒 2 │ ← Floating Cart
│                         │   Button
└─────────────────────────┘
```

#### Mobile Cart Modal (When tapped 🛒)

```
┌─────────────────────────┐
│ Your Cart          [✕]  │
├─────────────────────────┤
│                         │
│ [Thumb] Guitar Pro      │
│ $299.99 × 1             │
│ Total: $299.99  [🗑️]    │
│                         │
│ [Thumb] Drum Kit        │
│ $899.99 × 1             │
│ Total: $899.99  [🗑️]    │
│                         │
├─────────────────────────┤
│ Subtotal: $1,199.98     │
│ Shipping: $10.00        │
│ Total:    $1,209.98     │
│                         │
│ [VIEW FULL CART]        │
│ [CHECKOUT]              │
│                         │
└─────────────────────────┘
```

---

## 🎨 Design System

### Colors
```
Primary Green:     #229954  (Main brand color)
Accent Green:      #27AE60  (Highlights)
Light Background:  #F7F9FA  (Card backgrounds)
Dark Text:         #1C5E42  (Headlines)
Gray Text:         #7F8C8D  (Secondary text)
Border Color:      #D6DDE3  (Dividers)
Success (Green):   #1E8449  (Stock available)
Error (Red):       #C0392B  (Out of stock)
```

### Typography
```
Headlines (h1-h3):   Font-weight: 700
Body Text:          Font-weight: 400
Labels:             Font-weight: 600
```

### Spacing
```
Page padding:       2rem
Card padding:       1.5rem
Gap between items:  1rem - 1.5rem
Button padding:     0.75rem 1.4rem
```

### Border Radius
```
Cards:              8px
Buttons:            4px
Modal:              16px (on mobile)
```

### Shadows
```
Light:     0 2px 6px rgba(0,0,0,0.06)
Medium:    0 4px 12px rgba(0,0,0,0.08)
Heavy:     0 8px 16px rgba(0,0,0,0.12)
Focus:     0 0 0 3px rgba(34,153,84,0.2)
```

---

## 🎯 Component Specifications

### Product Card
```
Width (Desktop):    240-260px
Height:             Auto (3 sections)
Image Height:       200px
Padding:            1.2rem
Border:             1px solid #D6DDE3
Border Radius:      10px
Hover Effect:       +2px transform, shadow increase
```

### Cart Item
```
Layout:             Grid (70px image, content, 30px remove)
Image Size:         70×70px
Gap:                0.75rem
Background:         #f9f9f9
Border:             1px solid #f0f0f0
Border Radius:      6px
Padding:            0.75rem
```

### Button Styles
```
Primary Button:     Green background, white text
Secondary Button:   White background, green text
Disabled Button:    Gray background, gray text
Size: Normal:       12px × 1.4rem padding
Size: Small:        0.5rem × 1rem padding
```

### Input Fields
```
Padding:            0.7rem
Border:             1px solid #D6DDE3
Border Radius:      4px
Font Size:          1rem
Focus Border:       Green (#229954)
Background:         #fff
```

---

## 📊 Responsive Breakpoints

### Mobile First Approach
```
Mobile (0-480px)
│
├─ Stack single column
├─ Floating cart button
├─ Modal cart from bottom
└─ Touch-friendly (44px+ buttons)
    ↓
Mobile+ (480-768px)
│
├─ Single column or 2 columns
├─ Cart button still visible
├─ Better spacing
└─ More comfortable touch
    ↓
Tablet (768-1024px)
│
├─ 2-3 column products
├─ Cart below (full width)
├─ No floating button
└─ Desktop-like features
    ↓
Desktop (1024px+)
│
├─ 4 column product grid
├─ Sidebar cart (right, sticky)
├─ Full feature set
└─ Optimal for keyboard/mouse
```

---

## 🖱️ Interaction Patterns

### Desktop - Add to Cart
```
User hovers over product card
    ↓
Card shadow increases
    ↓
User sees quantity input + Add button
    ↓
User adjusts quantity (1-10)
    ↓
User clicks "Add to Cart"
    ↓
Form submits via POST
    ↓
Page redirects and reloads
    ↓
Cart sidebar updates with new item
    ↓
Cart count badge increments
```

### Mobile - Add to Cart
```
User taps on quantity field
    ↓
Mobile keyboard appears
    ↓
User adjusts quantity
    ↓
User taps "Add to Cart" button
    ↓
Form submits
    ↓
Page reloads
    ↓
Cart button badge updates with count
    ↓
User can tap 🛒 to view cart
```

### Remove Item
```
User sees trash icon (🗑️) on cart item
    ↓
User taps/clicks trash icon
    ↓
Form submits to remove_from_cart.php
    ↓
Item removed from session
    ↓
Page reloads
    ↓
Cart updated, totals recalculated
```

---

## 🎬 Animation Guide

### Hover Effects
- Card hover: `transform: translateY(-2px); box-shadow increase`
- Button hover: `transform: translateY(-1px); box-shadow increase`
- Link hover: `opacity: 0.85`

### Transitions
- Duration: 0.15s - 0.3s
- Easing: ease or ease-in-out
- Properties: transform, box-shadow, opacity

### Mobile Modal
- Slide up from bottom
- Duration: 0.3s
- Easing: ease-out
- On close: Slide down

---

## 📐 Grid Layouts

### Desktop Product Grid
```
Grid Template Columns:  repeat(auto-fill, minmax(260px, 1fr))
Gap:                   1.5rem
Max Columns:           4-5
Result:                Flexible responsive grid
```

### Tablet Product Grid
```
Grid Template Columns:  repeat(auto-fill, minmax(200px, 1fr))
Gap:                   1rem
Max Columns:           3-4
Result:                Responsive to content
```

### Mobile Product Grid
```
Grid Template Columns:  1fr (single column)
Gap:                   1rem
Max Columns:           1
Result:                Full-width single column
```

---

## 🎯 Focus States

### Keyboard Navigation
```
Tab → Focus on search input (blue outline)
Tab → Focus on category dropdown
Tab → Focus on search button
Tab → Focus on first "Add to Cart" button
Tab → Focus on quantity input
Tab → Cycle through all buttons
```

### Visual Indicators
- Focus outline: 3px solid rgba(34,153,84,0.5)
- Outline offset: 2px
- Color: Green theme color

---

## ✨ Loading States

### Skeleton States
```
Product Card Loading:
┌──────────────┐
│ [████████]   │  ← Image placeholder
├──────────────┤
│ [██████]     │  ← Title
│ [███]        │  ← Brand
│ [████████]   │  ← Price
│ [████]       │  ← Stock
│ [████████]   │  ← Button
└──────────────┘
```

### Empty States
```
No Products Found:
┌─────────────────────────────┐
│        📦                   │
│   No Products Found        │
│                            │
│  Try adjusting your        │
│  search or filters to      │
│  find what you're looking  │
│  for.                      │
└─────────────────────────────┘

Empty Cart:
┌─────────────────────────────┐
│        🛍️                   │
│   Your cart is empty       │
│                            │
│ Add items to get started!  │
└─────────────────────────────┘
```

---

## 📱 Touch Targets

### Recommended Sizes
```
Touch Button:       44x44px minimum
Touch Input:        32x44px minimum
Touch Link:         44x44px minimum
Touch Icon:         24x24px (with padding)
```

### Mobile Optimization
```
Button padding:     12px 16px (mobile)
Card padding:       12px (mobile)
Gap between items:  12px (mobile)
Header height:      56px (mobile)
Footer height:      48px (mobile)
```

---

## 🎨 Visual Hierarchy

### Emphasis Order
```
1st Priority:  Product Price (largest, brightest green)
2nd Priority:  Product Name (large, bold)
3rd Priority:  Stock Status (medium, colored)
4th Priority:  Brand Name (small, gray)
5th Priority:  Description (small, gray)
```

### Cart Emphasis
```
1st Priority:  Grand Total (largest, bold, green)
2nd Priority:  Subtotal & Shipping (medium size)
3rd Priority:  Item Details (normal size)
4th Priority:  Action Buttons (standard)
```

---

**Design System Version:** 1.0  
**Last Updated:** January 21, 2026  
**Design Tool:** CSS3 + Responsive Grid  
**Framework:** Mobile-First, Component-Based
