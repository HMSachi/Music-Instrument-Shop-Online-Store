# 🎨 VISUAL DESIGN REFERENCE

## Design System Overview

```
┌─────────────────────────────────────────────────────────┐
│                    COLOR PALETTE                         │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  Primary       ■ #229954  (Main brand color)            │
│  Accent        ■ #27AE60  (Highlight color)             │
│  Dark Primary  ■ #1a7a42  (Hover state)                 │
│  Background    ■ #F7F9FA  (Light gray-blue)             │
│  Text          ■ #1C5E42  (Dark green)                  │
│  Muted         ■ #7F8C8D  (Gray text)                   │
│  Border        ■ #D6DDE3  (Subtle dividers)             │
│  Success       ■ #1E8449  (In stock)                    │
│  Error         ■ #C0392B  (Out of stock)                │
│  Warning       ■ #F39C12  (Ratings)                     │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

## Component Library

### Premium Header
```
┌──────────────────────────────────────────┐
│  🎵 Melody Masters   Products  My Account │  ← Sticky
│                      Cart  Logout         │  ← Gradient BG
└──────────────────────────────────────────┘
  ↓ Shadow
Features:
- Sticky positioning
- Gradient background
- Nav link underline animation
- Cart badge with pulse
- Smooth transitions
```

### Product Detail Layout
```
┌─────────────────────────────────────────┐
│  ← Back to Products                      │
├──────────────────┬──────────────────────┤
│                  │  Product Category    │
│                  │  ═══════════════     │
│                  │  Product Name        │
│   Product        │  (Large Title)       │
│   Image          │                      │
│   (Sticky)       │  ★★★★★ 4.5 (125)    │
│                  │                      │
│   (Hover Zoom)   │  $99.99 per unit     │
│                  │                      │
│                  │  ● In Stock (45)     │
│                  │                      │
│                  │  ┌─────────────────┐ │
│                  │  │ Qty: [−] 1 [+]  │ │
│                  │  └─────────────────┘ │
│                  │  ┌─────────────────┐ │
│                  │  │ 🛒 Add to Cart  │ │ ← Gradient
│                  │  └─────────────────┘ │    Button
│                  │  📦 Free Shipping    │
│                  │  🔄 Easy Returns     │
│                  │  ✓ Quality Assured   │
└──────────────────┴──────────────────────┘

┌──────────────────────────────────────────┐
│  Product Description                     │
│  ─────────────────────────────────────── │
│  Detailed product information...         │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  Customer Reviews                        │
│  ─────────────────────────────────────── │
│  ▌ John Smith                      │ 5d  │
│  ★★★★★ Great quality!              │     │
│                                        │
│  ▌ Jane Doe                        │ 2w  │
│  ★★★★☆ Good but expensive          │     │
└──────────────────────────────────────────┘
```

### Card Styles
```
┌─────────────────────────┐
│   Card Component        │  ← 1px border
│  ─────────────────────  │  ← Top accent bar
│   Clean content...      │
│   Well spaced           │
│   Professional design   │
│                         │
└─────────────────────────┘
  ↓ shadow: 0 2px 6px (normal)
  ↓ shadow: 0 8px 20px (on hover)
  ↓ transform: translateY(-6px)
```

## Typography Hierarchy

```
H1 Product Title          ╔════════════════════════════════╗
                          ║ Product Name (2.2rem, 800)     ║
                          ╚════════════════════════════════╝

H2 Section Titles         ╔════════════════════════════════╗
                          ║ Customer Reviews (1.8rem, 800) ║
                          ╚════════════════════════════════╝

H3 Card Titles           ╔════════════════════════════════╗
                         ║ John Smith (1.05rem, 700)      ║
                         ╚════════════════════════════════╝

Body Text (1rem)         Lorem ipsum dolor sit amet, consectetur
Small Text (0.9rem)      adipiscing elit, sed do eiusmod tempor
Tiny Text (0.85rem)      incididunt ut labore et dolore magna
```

## Button States

```
Default State              Hover State               Active State
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│ 🛒 Add to Cart  │  →   │ 🛒 Add to Cart  │  →   │ 🛒 Add to Cart  │
└─────────────────┘      └─────────────────┘      └─────────────────┘
  shadow-md                shadow-lg                shadow-sm
  normal                   lifted (-2px)           pressed (0px)
  #27AE60                  darker green            darker green
```

## Form Input States

```
Default               Focus                  Error                 Success
┌─────────────┐      ┌─────────────┐      ┌─────────────┐      ┌─────────────┐
│ Input here  │      │ Input here  │      │ Input here  │      │ Input here  │
└─────────────┘      └─────────────┘      └─────────────┘      └─────────────┘
 border-light         border-primary       border-red            border-green
 shadow-none         shadow-primary       shadow-red            shadow-green
```

## Responsive Breakpoints

```
Desktop (1024px+)          Tablet (768px-1024px)      Mobile (<768px)
┌──────────────────────┐   ┌──────────────┐          ┌──────────┐
│ Image │ Details      │   │   Image      │          │  Image   │
│       │              │   ├──────────────┤          ├──────────┤
│       │              │   │  Details     │          │ Details  │
└──────────────────────┘   └──────────────┘          └──────────┘
2 columns              1 column + margin       Stacked layout
```

## Animation Reference

```
FADE IN SCALE                    SLIDE DOWN
┌────────────┐                   ┬ (offset)
│            │  0.6s             │
│  Product   │  ──→  Fade in    └──→ Slide down
│  Gallery   │        Scale 0.95    0.3s
│            │        to 1.0
└────────────┘

HOVER LIFT                       PULSING BADGE
Button normal     Hover          Before    After
┌──────────┐      ┌──────────┐   ○  1s   ◉  2s
│ Add Cart │  +2px│ Add Cart │   Size: 1 → 1.1 → 1
└──────────┘ ↓    └──────────┘
             lifted

SLIDE IN (REVIEWS)               BLINKING (STOCK)
Initial        0.4s              ●●●●●  Blinking
ஂ ────→ ║ Review Card          ● stock
 offset          aligned         0-1s, 1-2s (repeat)
```

## Spacing System (4px Base Unit)

```
0.5rem  =  8px   ├─ Small gaps
0.75rem = 12px   ├─ Component padding
1rem    = 16px   ├─ Standard spacing
1.5rem  = 24px   ├─ Section spacing
2rem    = 32px   ├─ Large gaps
3rem    = 48px   ├─ Major sections
4rem    = 64px   └─ Page gaps
```

## Shadow System

```
Small Shadow (0 2px 6px)       Medium Shadow (0 4px 12px)
┌────────────┐                 ┌────────────┐
│  Card      │ ⌢               │  Card      │ ⌢⌢
│            │                 │            │
└────────────┘                 └────────────┘

Large Shadow (0 8px 20px)
┌────────────┐
│  Modal     │ ⌢⌢⌢
│            │
└────────────┘

Used on: Base cards   Used on: Hover states   Used on: Headers
```

## Interaction Patterns

```
HOVER EFFECT                    FOCUS STATE
Normal         Hover            Normal          Focus
┌───────┐      ┌───────┐       ┌──────────┐   ┌──────────┐
│ Card  │ →    │ Card  │       │ Input    │ →│ Input    │
│       │      │  ↑ 6px│       │          │   │ ◆ color  │
└───────┘      └───────┘       └──────────┘   └──────────┘
shadow-sm      shadow-lg        border-light   border-primary
              transform-y       + shadow

ACTIVE STATE
┌──────────────────┐
│ Button Active    │ ↓ pressed (translateY 0)
│ Darker color     │ shadow-sm
│                  │
└──────────────────┘
```

## Color Usage Guide

```
Text Elements              Interactive Elements      Status Elements
─────────────────────────────────────────────────────────────────
Primary Text: #1C5E42     Links: #229954            Success: #1E8449
Secondary: #7F8C8D        Buttons: gradient         Error: #C0392B
Disabled: #AAA            Hover: darker primary     Warning: #F39C12
White on color: #FFFFFF   Active: #1a7a42          Info: #3498DB

Background Colors         Border Colors            Accent Colors
─────────────────────────────────────────────────────────────────
Page: #FFFFFF             Default: #D6DDE3         Primary: #229954
Light Bg: #F7F9FA         Light: #ECF0F1           Accent: #27AE60
Card Hover: rgba light    Focus: #229954           Highlight: #27AE60
Overlay: rgba dark        Error: #C0392B           Hover: #1a7a42
```

## Micro-interactions

```
BUTTON CLICK             INPUT FOCUS              CARD HOVER
1. Hover lift           1. Border color change   1. Shadow expand
   (-2px)                  (gray → primary)         (small → large)
2. Shadow increase      2. Inner shadow add      2. Border highlight
3. Color darken         3. Cursor position       3. Transform up (-6px)
4. On click:            4. Placeholder fade      4. Image scale (1.08)
   Scale down (0.95)    5. Focus ring visible
   then restore
```

## Design Tokens Summary

```
TYPOGRAPHY
Font Family: Segoe UI, Helvetica Neue, sans-serif
Line Height: 1.2 (headings), 1.6 (body)
Letter Spacing: -0.5px (large headings), 0.5px (labels)

COLORS
Primary: #229954      Muted: #7F8C8D
Accent: #27AE60       Border: #D6DDE3
BG: #F7F9FA          Success: #1E8449
Text: #1C5E42        Error: #C0392B

SPACING
Base Unit: 0.25rem (4px)
Common: 0.5rem, 1rem, 1.5rem, 2rem, 3rem, 4rem

SHADOWS
Small:  0 2px 6px rgba(0,0,0,0.06)
Medium: 0 4px 12px rgba(0,0,0,0.08)
Large:  0 8px 20px rgba(0,0,0,0.1)

BORDER RADIUS
Small: 4px - 6px
Medium: 8px - 10px
Large: 16px
Rounded: 50%

TRANSITIONS
Duration: 0.3s
Easing: cubic-bezier(0.25, 0.46, 0.45, 0.94)
Properties: transform, shadow, color, border

ANIMATIONS
Fade In Scale: 0.6s
Slide Down: 0.3s
Hover Lift: instant
Pulsing: 2s infinite
```

---

**Design System Version**: 1.0  
**Last Updated**: January 25, 2026  
**Status**: ✅ Complete
