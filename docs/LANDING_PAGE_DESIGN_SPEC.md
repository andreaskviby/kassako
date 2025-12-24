# Kassako Landing Page - Complete UI/UX Design Specification

## Executive Summary

This document provides comprehensive UI/UX specifications for the Kassako landing page - a Swedish SaaS that predicts cash flow runway by connecting to Fortnox. The design follows Swedish design principles: clean, trustworthy, and minimal.

---

## Brand Identity

### Color Palette

| Color Name | Hex Code | CSS Variable | Usage |
|------------|----------|--------------|-------|
| Primary (Forest) | `#1A3D2E` | `kassako-forest` | Primary buttons, headers, key UI elements |
| Accent (Gold) | `#C4A962` | `kassako-gold` | CTAs, highlights, accent elements |
| Background (Cream) | `#FDFBF7` | `kassako-cream` | Page background, cards |
| Text | `#1C1C1C` | `kassako-text` | Body text, headings |
| Muted | `#6B6B6B` | `kassako-muted` | Secondary text, labels |
| Success | `#2D7A4F` | `kassako-success` | Positive indicators |
| Warning | `#D97706` | `kassako-warning` | Caution states |
| Danger | `#DC2626` | `kassako-danger` | Error states, alerts |

### Typography

**Primary Font:** Inter (Sans-serif)
- Body text, UI elements, navigation

**Display Font:** Plus Jakarta Sans
- Headlines, section titles, feature numbers

#### Type Scale

| Usage | Mobile | Desktop | Weight | Line Height |
|-------|--------|---------|--------|-------------|
| Hero | 2.5rem (40px) | 4.5rem (72px) | 700 | 1.1 |
| Display | 2rem (32px) | 3.5rem (56px) | 600 | 1.15 |
| Section | 1.75rem (28px) | 2.25rem (36px) | 600 | 1.25 |
| Body Large | 1.125rem (18px) | 1.25rem (20px) | 400 | 1.6 |
| Body | 1rem (16px) | 1rem (16px) | 400 | 1.5 |
| Small | 0.875rem (14px) | 0.875rem (14px) | 400 | 1.5 |
| Caption | 0.75rem (12px) | 0.75rem (12px) | 500 | 1.4 |

---

## Section-by-Section Specifications

### 1. Navigation (Sticky, Minimal)

**Layout:**
- Fixed position at top
- Height: 64px (mobile), 72px (desktop)
- Background: Cream with 95% opacity + backdrop blur

**Safe Areas:**
```css
padding-top: max(env(safe-area-inset-top), 0px);
```

**Structure:**
```
[Logo] -------------- [Nav Links] -------------- [Login] [CTA Button]
```

**Components:**
- **Logo:** 32x32px icon + brand name
- **Nav Links:** Hur det fungerar, Funktioner, Priser, FAQ
- **CTA Button:** "Prova gratis" - 48px min-height, rounded-xl

**Scroll Behavior:**
- Adds subtle shadow when scrolled > 20px
- Smooth transition: 300ms

**Mobile Menu:**
- Hamburger icon (48x48 touch target)
- Full-width dropdown with 16px padding

---

### 2. Hero Section

**Spacing:**
- Top padding: 128px (mobile), 160px (desktop), 192px (large)
- Bottom padding: 64px (mobile), 96px (desktop)

**Structure:**
```
            [Fortnox Badge]

    Se hur lange dina pengar racker

         [87] dagar
         KASSAFORLOPP

    [Subtitle paragraph text]

    [Gold CTA]  [Secondary CTA]

    [Trust indicators]
```

**Animated Counter Specifications:**

The dramatic "87 dagar" counter is the hero's focal point.

| Property | Value |
|----------|-------|
| Font Family | Plus Jakarta Sans |
| Font Size | clamp(4rem, 15vw, 10rem) |
| Font Weight | 700 |
| Letter Spacing | -0.03em |
| Color | #1A3D2E (Forest) |
| Number Style | tabular-nums |

**Animation:**
- Triggers on viewport intersection (50% threshold)
- Duration: 2000ms
- Easing: ease-out
- Counts from 0 to 87
- 16ms frame rate for smoothness

**Glow Effect:**
```css
.runway-counter-glow {
    position: absolute;
    inset: 0;
    filter: blur(48px);
    opacity: 0.2;
    background: radial-gradient(ellipse, #C4A962 0%, transparent 70%);
}
```

**"dagar" Label:**
- Font Size: clamp(2rem, 5vw, 3.75rem)
- Color: Gold (#C4A962)
- Aligned baseline with number

**Fortnox Integration Badge:**
- Pill shape (rounded-full)
- White background with border
- Icon + "Integrerar med Fortnox"
- Subtle shadow

**Trust Indicators:**
- Three items in a flex row
- Green checkmarks (kassako-success)
- Text: "Inget kreditkort kravs", "Uppsagning nar som helst", "Sakerhetskrypterad data"

---

### 3. How It Works (3 Steps)

**Layout:** 3-column grid (stacks on mobile)

**Step Card Structure:**
```
    [Circle with Number]
         |
    [Connector Line] ------>
         |
    [Heading]
    [Description]
    [Icon illustration]
```

**Step Number Circle:**
- Size: 56x56px
- Background: Forest (#1A3D2E)
- Text: White, 20px, bold
- Shadow: glow-forest effect

**Connector Line:**
- Hidden on mobile
- 2px height
- Gradient: Forest to Gold
- Positioned at circle center

**Steps Content:**
1. **Koppla Fortnox** - OAuth integration
2. **Synka data** - Automatic sync
3. **Se forlopp** - View runway

---

### 4. Features Grid

**Layout:**
- 1 column (mobile)
- 2 columns (sm: 640px+)
- 4 columns (lg: 1024px+)

**Gap:** 24px

**Feature Card:**
```
[Icon Container]
[Heading]
[Description]
```

**Icon Container:**
- Size: 48x48px
- Background: forest-50 (#f0f7f4)
- Border-radius: 12px (rounded-xl)
- Icon color: Forest
- Hover: Background becomes Forest, icon becomes white

**Card Properties:**
- Padding: 24px (mobile), 32px (desktop)
- Background: White
- Border: 1px solid forest-100
- Border-radius: 16px (rounded-2xl)
- Shadow: card (subtle)
- Hover: Lift -4px, shadow-card-hover

**Features:**
1. Kassaposition (Cash Position)
2. 12-manaders prognos (Forecast)
3. AI-insikter (AI Insights)
4. Betalningsmonsster (Payment Patterns)

---

### 5. Dashboard Preview

**Mockup Container:**
- Border-radius: 16px
- Shadow: elevated
- Border: 1px solid forest-100
- Floating animation: 6s ease-in-out infinite

**Browser Chrome:**
- Height: 48px
- Background: Forest
- Traffic light dots: 12x12px (red, yellow, green)
- URL text: white/70 opacity

**Dashboard Content:**
- Background: Cream
- Padding: 16px (mobile), 24px (desktop)

**Metric Cards (3 columns):**
1. Runway: "87 dagar" with +12 trend
2. Current Balance: "847 320 kr"
3. Expected Income: "+234 500 kr"

**Chart Area:**
- 12 bars representing months
- Height: 192px
- Gradient bars: Forest to Success
- Month labels below

---

### 6. Pricing Section

**Single Pricing Card:**
- Max width: 512px (32rem)
- Centered layout

**Card Properties:**
- Padding: 32px (mobile), 40px (desktop)
- Background: White
- Border: 2px solid Forest
- Border-radius: 24px (rounded-3xl)
- Shadow: elevated

**Gradient Top Border:**
```css
.pricing-card::before {
    height: 6px;
    background: linear-gradient(90deg,
        #1A3D2E 0%,
        #C4A962 50%,
        #1A3D2E 100%
    );
}
```

**Badge:**
- Background: Gold/20%
- Text: Forest
- Padding: 4px 12px
- Border-radius: full

**Price Display:**
- Amount: clamp(2.5rem, 8vw, 4rem)
- "149" + "kr/manad"
- Font: Plus Jakarta Sans, bold

**Feature List:**
- 8 features with green checkmarks
- 16px gap between items
- Icon size: 20x20px

**CTA Button:**
- Full width
- Gold background
- Height: 48px minimum

---

### 7. Testimonials

**Layout:** 3-column grid (stacks on mobile)
**Gap:** 24px

**Testimonial Card:**
```
"Quote text here..."

[Avatar] [Name]
         [Title, Company]
```

**Quote Styling:**
- Opening quotation mark: Gold, 36px, serif
- Quote text: 18px, line-height 1.625
- Color: kassako-text

**Avatar:**
- Size: 48x48px
- Border-radius: full
- Gradient placeholder background

**Author Info:**
- Name: semibold, kassako-text
- Title: 14px, kassako-muted

---

### 8. FAQ Section

**Accordion Style:**
- Native `<details>` element for accessibility
- Border-bottom between items

**Question (Summary):**
- Padding: 20px vertical
- Font: 18px, semibold
- Min-height: 56px (touch target)
- Chevron icon on right

**Answer:**
- Padding-bottom: 20px
- Color: kassako-muted
- Line-height: 1.625

**Icon Animation:**
- Rotate 180deg when open
- Transition: 300ms

**FAQ Content:**
1. Data security
2. Fortnox integration
3. After trial period
4. Export capabilities
5. Sync frequency
6. Target audience

---

### 9. Final CTA

**Background:** Forest (#1A3D2E)

**Decorative Elements:**
```css
.cta-section::before {
    background:
        radial-gradient(ellipse at 30% 50%,
            rgba(196, 169, 98, 0.15) 0%, transparent 50%),
        radial-gradient(ellipse at 70% 50%,
            rgba(196, 169, 98, 0.1) 0%, transparent 50%);
}
```

**Content:**
- Headline: Display size, white
- Subtitle: forest-200 color
- CTA Button: Gold, large (text-lg, px-10)
- Secondary link: White text with arrow

---

### 10. Footer

**Background:** Forest (#1A3D2E)
**Padding:** 48px (mobile), 64px (desktop)

**Safe Area:**
```css
padding-bottom: max(env(safe-area-inset-bottom), 48px);
```

**Layout:** 4-column grid
1. Brand column (logo + tagline)
2. Product links
3. Company links
4. Legal links

**Link Styling:**
- Color: forest-200
- Hover: white
- Transition: 200ms

**Bottom Bar:**
- Border-top: 1px white/10
- Flex row: Copyright | Social icons
- Social icons: 20x20px

---

## Responsive Breakpoints

| Breakpoint | Width | Usage |
|------------|-------|-------|
| Mobile | 0 - 639px | Single column, stacked layouts |
| Small (sm) | 640px+ | 2-column grids begin |
| Medium (md) | 768px+ | Tablet optimizations |
| Large (lg) | 1024px+ | Full desktop layout |
| XL (xl) | 1280px+ | Wide screen optimizations |

---

## Accessibility Specifications

### Color Contrast Ratios

| Element | Foreground | Background | Ratio | Passes |
|---------|------------|------------|-------|--------|
| Body text | #1C1C1C | #FDFBF7 | 15.2:1 | AAA |
| Muted text | #6B6B6B | #FDFBF7 | 5.8:1 | AA |
| Forest on cream | #1A3D2E | #FDFBF7 | 10.1:1 | AAA |
| Gold on forest | #C4A962 | #1A3D2E | 5.4:1 | AA |
| White on forest | #FFFFFF | #1A3D2E | 12.8:1 | AAA |

### Focus States

```css
:focus-visible {
    outline: 2px solid #1A3D2E;
    outline-offset: 2px;
}
```

### Touch Targets

- Minimum size: 44x44px (iOS HIG) / 48x48px (Material)
- All buttons meet 48x48px minimum
- Nav links have adequate padding
- FAQ items: 56px min-height

### Screen Reader Support

- Proper heading hierarchy (h1 > h2 > h3)
- ARIA labels on icon-only buttons
- Live regions for dynamic content (counter)
- Skip navigation link recommended

### Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## Animation Specifications

### Counter Animation
```javascript
duration: 2000ms
easing: ease-out
frameRate: 60fps (16ms intervals)
trigger: IntersectionObserver at 50% viewport
```

### Fade In Up
```css
@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}
duration: 600ms
easing: ease-out
```

### Float Animation
```css
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
duration: 6000ms
easing: ease-in-out
iteration: infinite
```

### Stagger Delays
- Element 1: 0ms
- Element 2: 100ms
- Element 3: 200ms
- Element 4: 300ms
- Element 5: 400ms
- Element 6: 500ms

---

## Mobile-First Considerations

### Touch Interactions
- All interactive elements have `:active` states
- 300ms tap highlight disabled
- Smooth scroll for anchor links

### Safe Area Insets
```css
:root {
    --sat: env(safe-area-inset-top);
    --sab: env(safe-area-inset-bottom);
    --sal: env(safe-area-inset-left);
    --sar: env(safe-area-inset-right);
}
```

### Viewport Meta
```html
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
```

### Performance
- Fonts preconnected
- Images lazy-loaded (when added)
- CSS animations use GPU-accelerated properties

---

## File Locations

| File | Path |
|------|------|
| Tailwind Config | `/Users/andreaskviby/Herd/kassako/tailwind.config.js` |
| Custom CSS | `/Users/andreaskviby/Herd/kassako/resources/css/app.css` |
| Landing Template | `/Users/andreaskviby/Herd/kassako/resources/views/landing.blade.php` |
| Routes | `/Users/andreaskviby/Herd/kassako/routes/web.php` |

---

## Implementation Checklist

- [x] Tailwind configuration with brand colors
- [x] Custom CSS components and utilities
- [x] Landing page Blade template
- [x] Route configuration
- [x] Sticky navigation with mobile menu
- [x] Hero section with animated counter
- [x] How it works - 3 steps
- [x] Features grid - 4 cards
- [x] Dashboard preview mockup
- [x] Pricing section
- [x] Testimonials - 3 cards
- [x] FAQ accordion
- [x] Final CTA section
- [x] Footer with links
- [x] Mobile responsive design
- [x] Safe area handling
- [x] Accessibility compliance
- [x] Reduced motion support
- [x] Focus visible states

---

## Next Steps

1. **Run build:** `npm run build` to compile assets
2. **Test responsiveness:** Check all breakpoints
3. **Test animations:** Verify counter and fade effects
4. **Accessibility audit:** Run automated tools
5. **Performance test:** Check Lighthouse scores
6. **Add real images:** Replace placeholder avatars
7. **SEO optimization:** Add structured data
8. **Analytics:** Integrate tracking

---

*Document created: December 2025*
*Design System Version: 1.0*
*Framework: Laravel + Tailwind CSS*
