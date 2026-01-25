# 🎨 MELODY MASTERS - VISUAL DESIGN GUIDE

## Color Palette

### Primary Colors
```
Primary Green:     #229954  (Main brand color)
Accent Green:      #27AE60  (Lighter highlight)
Dark Green:        #1a7a42  (Hover state)
```

### Neutral Colors
```
Light Background:  #F7F9FA  (Page background)
Very Light:        #FAFBFC  (Secondary background)
Text Color:        #1C5E42  (Primary text)
Muted Gray:        #7F8C8D  (Secondary text)
Border Color:      #D6DDE3  (Subtle dividers)
Light Border:      #ECF0F1  (Very subtle)
```

### Status Colors
```
Success Green:     #1E8449  (In stock)
Error Red:         #C0392B  (Out of stock)
Warning Orange:    #F39C12  (Ratings)
```

---

## Typography Scale

```
H1 (Product Title):      2.2rem, weight 800
H2 (Section Title):      1.8rem, weight 800
H3 (Subsection):         1.4rem, weight 700
H4 (Reviews):            1.05rem, weight 700
Label/Small Head:        0.95rem, weight 600
Body Text:               1rem, weight 400
Small Text:              0.9rem, weight 400
Tiny Text:               0.85rem, weight 600
```

### Font Family
```
Primary: 'Segoe UI', 'Helvetica Neue', Tahoma, Geneva, sans-serif
```

---

## Layout Specifications

### Container
```
Max Width:      1200px
Padding:        2rem (desktop), 1rem (mobile)
Horizontal:     2rem margin on sides
```

### Product Detail Grid
```
Desktop:        1fr 1fr (50% - 50%)
Gap:            4rem spacing
Tablet:         1fr (single column)
Mobile:         1fr (single column)
```

---

## Component Styling

### Premium Header
```
Height:         ~60px
Background:     Gradient (primary → accent)
Shadow:         0 4px 20px rgba(34, 153, 84, 0.15)
Position:       Sticky (top: 0)
Z-index:        100
```

### Product Card
```
Border Radius:  10px
Border:         1px solid border color
Box Shadow:     0 2px 6px (normal)
Hover Shadow:   0 8px 20px (elevated)
Top Border:     3px gradient (animated on hover)
Hover Transform: translateY(-6px)
```

### Buttons
```
Padding:        0.8rem 1.6rem (standard)
Padding Large:  1rem 2rem (CTA)
Border Radius:  8px (standard), 10px (large)
Font Weight:    600 (standard), 700 (large)
Transition:     0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)
Shadow:         0 4px 12px (normal), 0 8px 20px (hover)
Hover Effect:   translateY(-2px)
```

### Rating Stars
```
Normal:         1.3rem, #D6DDE3 (empty)
Filled:         1.3rem, #F39C12 (orange)
Review:         1rem, similar styling
```

### Inputs & Forms
```
Padding:        0.8rem
Border:         2px solid border color
Border Radius:  8px
Focus Border:   var(--primary)
Focus Shadow:   0 0 0 3px rgba(34, 153, 84, 0.1)
Transition:     0.3s ease
```

---

## Spacing System

```
Base Unit:      0.25rem (4px)
Used Increments:
  0.5rem   = 8px
  0.75rem  = 12px
  1rem     = 16px
  1.25rem  = 20px
  1.5rem   = 24px
  1.75rem  = 28px
  2rem     = 32px
  2.5rem   = 40px
  3rem     = 48px
  4rem     = 64px
```

---

## Shadow Depths

### Small Shadow (--shadow-sm)
```css
0 2px 6px rgba(0, 0, 0, 0.06)
```
Used for: Subtle elevation, base cards

### Medium Shadow (--shadow-md)
```css
0 4px 12px rgba(0, 0, 0, 0.08)
```
Used for: Card hover, buttons, modals

### Large Shadow (--shadow-lg)
```css
0 8px 20px rgba(0, 0, 0, 0.1)
```
Used for: Header, prominent elements, hero

---

## Animations

### Fade In Scale
```
Duration:    0.6s
Start:       opacity 0, scale 0.95
End:         opacity 1, scale 1
Used on:     Product gallery image
```

### Slide Down
```
Duration:    0.3s
Used on:     Alert messages
Transform:   translateY(-10px) → translateY(0)
```

### Hover Lift
```
Duration:    0.3s
Transform:   translateY(-2px) on hover
Used on:     Buttons, cards
```

### Pulsing
```
Duration:    2s, infinite
Scale:       1 → 1.1 → 1
Used on:     Cart badge
```

### Blinking
```
Duration:    2s, infinite
Opacity:     1 → 0.4 → 1
Used on:     Stock indicator
```

---

## Responsive Breakpoints

### Desktop (1024px+)
- 2-column product layout
- Sticky gallery
- Full navigation
- Full-width content

### Tablet (768px - 1024px)
- Single-column product layout
- Adjusted spacing
- Responsive grid
- Simplified navigation

### Mobile (480px - 768px)
- Single-column everything
- Stacked layout
- Reduced font sizes
- Touch-friendly spacing
- Full-width buttons

### Small Mobile (< 480px)
- Minimal spacing
- Compact buttons
- Smaller fonts
- Single-column grid

---

## Interactive States

### Button States
```
Default:    Full color, normal shadow
Hover:      Slightly darker, lifted, expanded shadow
Active:     Pressed down, reduced shadow
Focus:      Border highlight for accessibility
Disabled:   Gray, no interaction
```

### Link States
```
Default:    White color (in header)
Hover:      Underline animation from left
Active:     Underline visible
```

### Form States
```
Default:    Border gray
Hover:      Border slightly darker
Focus:      Border primary color, colored shadow
Error:      Border red, error message
Success:    Border green, success message
```

---

## Card Components

### Product Card
- Border: 1px border-light
- Radius: 10px
- Padding: 1.2rem
- Shadow: shadow-sm, hover shadow-lg
- Top decoration: Animated gradient bar

### Review Card
- Background: Light gray (var(--bg))
- Border-left: 4px primary
- Padding: 1.5rem
- Radius: 10px
- Animation: Slide in from left

### Quick Info Card
- Background: Subtle gradient on hover
- Padding: 1rem
- Radius: 8px
- Animation: Lift on hover

---

## Accessibility Features

✓ High contrast text (WCAG AA)
✓ Focus states visible on all interactive elements
✓ Semantic HTML structure
✓ Proper heading hierarchy
✓ Form labels linked to inputs
✓ Alt text on images
✓ Skip navigation links (recommended)
✓ Color not sole indicator of status

---

## Performance Optimizations

✓ CSS transitions for smooth animations
✓ Hardware-accelerated transforms
✓ Minimal repaints and reflows
✓ Efficient media queries
✓ Optimized shadows (not excessive)
✓ CSS variables for easy theming
✓ Mobile-first responsive design

---

## Future Enhancement Ideas

1. **Dark Mode**: Complete dark theme support
2. **Image Gallery**: Multiple product images with thumbnails
3. **Product Comparison**: Side-by-side comparison tool
4. **Customer Reviews Form**: Add review submission (if not exists)
5. **Video Preview**: Product video playback
6. **3D View**: Rotating product viewer
7. **Size/Color Variants**: Product options selector
8. **Stock Counter**: Real-time inventory updates
9. **Related Products**: Carousel of similar items
10. **Share Buttons**: Social media sharing

---

**Created**: January 25, 2026  
**Version**: 1.0 - Premium Design System  
**Status**: ✅ Complete & Production Ready
