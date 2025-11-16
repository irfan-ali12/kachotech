# KachoTech Astra Child Theme - Setup & Activation Guide

## 🚀 Quick Start

### Theme Status
✅ **Ready for Activation** - All template files created and WooCommerce integrated

### Files Summary
- **Template Files Created**: 8 core templates
- **CSS Stylesheets**: 1 homepage stylesheet  
- **Configuration**: Updated functions.php with style enqueuing
- **E-commerce Pages**: Checkout, Account/Login pages customized

---

## 📋 Pre-Activation Checklist

### 1. **Verify Dependencies**
```
✓ WordPress installed and running
✓ Astra theme (parent) activated
✓ WooCommerce plugin installed and activated
✓ Child theme folder: wp-content/themes/astra-child/
✓ All template files created
```

### 2. **Required WooCommerce Setup**
Before activating the theme, ensure:

```php
// Go to: WordPress Admin → WooCommerce → Settings

1. Products Tab:
   - Ratings enabled: Yes
   - Product images: 300x300 (or larger)
   - Featured products: Create at least 3-5

2. Catalog Tab:
   - Default category: Set to "All Products" or specific category
   - Shop page: Must be set to your shop page

3. Account Tab:
   - Guest checkout: Enabled (optional)
   - Account creation: Enabled (for registration)
   - Login/My Account page: Set to your account page

4. Checkout Tab:
   - Payment methods configured (Stripe, PayPal, etc.)
   - Shipping zones configured
```

### 3. **Create Product Categories**
Required categories for full functionality:

```
Dashboard → Products → Categories

1. Heaters
   - Description: Home heating solutions
   - Image: Add placeholder or category image
   
2. Electronics  
   - Description: Electronic gadgets
   - Image: Add placeholder or category image
   
3. Cosmetics
   - Description: Beauty and skincare products
   - Image: Add placeholder or category image
```

### 4. **Add Sample Products**
Create at least 8 products with:

```php
- Product name
- Description
- Price (and sale price if on promotion)
- Category (Heaters, Electronics, or Cosmetics)
- Featured image (placeholder: https://via.placeholder.com/300)
- Mark as "Featured" for homepage display
```

---

## 🔧 Activation Steps

### Step 1: Activate Child Theme
```
Dashboard → Appearance → Themes
→ Find "Astra Child" theme
→ Click "Activate"
```

### Step 2: Verify Homepage
```
Dashboard → Settings → Reading
→ Homepage displays as: Static page
→ Homepage: Select any page (theme uses home.php)
→ Save changes
```

### Step 3: Set Shop & Account Pages
```
Dashboard → WooCommerce → Settings → Advanced
1. Shop page: Create/Select a page
2. Go to: Dashboard → WooCommerce → Settings → Account
3. Set "My Account page" to your account page
```

---

## 📄 Template Structure & Files

### Homepage Templates (`/template-parts/home/`)

#### 1. **hero-section.php** (7.5KB)
- Winter sale promotional banner
- "Shop Winter Heaters" CTA
- Side cards for category promotions
- Uses placeholder image: https://via.placeholder.com/600x400

**When it renders:**
- Only on homepage (home.php calls it)
- Automatically fetches from WooCommerce if page set

---

#### 2. **category-strip.php** (4.2KB)
- Displays 4 product categories
- Shows category image and name
- Fallback to default categories if none exist

**Customization:**
```php
// To change fallback categories, edit line ~30:
'Heaters', 'Electronics', 'Cosmetics', 'Accessories'
```

---

#### 3. **featured-products-section.php** (6.8KB)
- Shows 8 featured products from WooCommerce
- Product grid with pricing and "Add to Cart"
- Responsive: 4 cols desktop, 2 cols tablet, 1 col mobile

**Requirements:**
- Mark products as "Featured" in WooCommerce
- Images must be set for products

---

#### 4. **promos-section.php** (9.1KB)
- Top promotional banners (3 cards)
- Bottom countdown timer + sale products
- Uses WooCommerce product queries

**Customize:**
- Edit titles/descriptions around line 20-40
- Update countdown timer date: `data-countdown="..."` (line ~90)

---

#### 5. **perks-section.php** (6.5KB)
- Trust signals: Free Shipping, 30-day Returns, 24/7 Support
- Mini promotional banners below
- Responsive layout

**Customize perks:**
```php
// Edit icon, title, description around line 30-60
// SVG icons: truck, check-circle, headphones
```

---

#### 6. **footer-section.php** (7.2KB)
- Company info, links, newsletter
- Social media integration
- App store badges

**Customize:**
- Company description: Line ~20
- Footer links: Add in `$footer_links` array
- Newsletter form: Currently basic, needs email service integration

---

#### 7. **home.php** (0.8KB)
- Main assembly file for homepage
- Calls all template parts in order
- Displays header + sections + footer

---

### Account Templates

#### 8. **account.php** (7.2KB)
- Login/Registration page when not logged in
- My Account dashboard when logged in
- Recent orders display
- Order statistics

**Features:**
- Tabbed login/register interface
- Recent orders table
- Account statistics (total orders, spent, etc.)
- Dashboard menu navigation

---

### Checkout

#### **form-checkout.php** (Located in `/woocommerce/checkout/`)
- Custom checkout page design
- Billing/Shipping forms
- Order review table
- Payment method selection
- Responsive layout

---

## 🎨 Styling & CSS

### Main Stylesheet: `/assets/css/homepage.css` (18KB)

**Color Palette:**
```css
--brand-color: #EC234A (Primary Red)
--brand-dark: #C9193A (Dark Red)
--accent-blue: #3A7AFE
--accent-mint: #40C6A8
--accent-gold: #FFC75F
--text-primary: #1A1A1D
--text-secondary: #6B6F76
--bg-light: #F6F7FA
```

**Breakpoints:**
```css
Mobile: < 640px
Tablet: 640px - 1024px
Desktop: > 1024px
```

**How styles load:**
```php
// In: inc/enqueue.php
if ( is_home() || is_front_page() ) {
    wp_enqueue_style( 'kachotech-homepage-css', 
        '/assets/css/homepage.css' );
}
```

---

## 🖼️ Placeholder Images

All templates use placeholder images from `https://via.placeholder.com/`

**To add real images:**

### Hero Section
```php
// File: template-parts/home/hero-section.php
// Line: ~55
src="https://via.placeholder.com/600x400"
→ Replace with your image URL
```

### Product Images
```php
// WooCommerce handles product images
// Upload in: Dashboard → Products → [Product] → Product images
```

### Category Images
```php
// Edit in: Dashboard → Products → Categories
// Click category → Upload image
```

---

## 🔌 WooCommerce Integration

### Hooks Used in Templates

**Product Queries:**
```php
// Featured Products - Line 20 in featured-products-section.php
new WP_Query([ 'post_type' => 'product', 'meta_key' => '_featured' ])

// Sale Products - Line 110 in promos-section.php
new WP_Query([ 'tax_query' => [['taxonomy' => 'product_visibility']] ])
```

**Category Queries:**
```php
// Categories - Line 15 in category-strip.php
get_terms([ 'taxonomy' => 'product_cat', 'hide_empty' => false ])
```

**Order Tracking:**
```php
// Orders - Line 40 in account.php
wc_get_orders([ 'customer_id' => get_current_user_id() ])
```

---

## 🧪 Testing Checklist

After activation, test:

### Page Display
- [ ] Homepage loads without errors
- [ ] All sections visible: Hero, Categories, Products, Promos, Perks, Footer
- [ ] Responsive on mobile (< 768px)
- [ ] Responsive on tablet (768-1024px)
- [ ] Responsive on desktop (> 1024px)

### Hero Section
- [ ] Title and CTA buttons visible
- [ ] Placeholder image loads
- [ ] "Shop Winter Heaters" button links to shop page

### Categories
- [ ] 4 categories display correctly
- [ ] Category images load
- [ ] Hover effects work
- [ ] Click category filters products

### Products
- [ ] 8 featured products display
- [ ] Product images load
- [ ] Prices shown correctly
- [ ] "Add to Cart" buttons work
- [ ] Product links go to product page

### Checkout
- [ ] Checkout page loads at: `/checkout/`
- [ ] Cart items display correctly
- [ ] Billing form present
- [ ] Shipping form present
- [ ] "Complete Order" button visible
- [ ] Payment method selection works

### Account
- [ ] Login page at: `/my-account/`
- [ ] Registration form present (if enabled)
- [ ] Dashboard shows when logged in
- [ ] Order history displays
- [ ] Statistics show correct numbers
- [ ] Logout button works

### Performance
- [ ] Homepage loads < 3 seconds
- [ ] No console errors (F12 → Console)
- [ ] No PHP notices/warnings

---

## 🐛 Troubleshooting

### Issue: Homepage blank or showing error
**Solution:**
```
1. Go to: Dashboard → Settings → Reading
2. Set "Homepage displays as" to "Static page"
3. Select any page for Homepage
4. Save changes
5. Verify home.php exists in theme root
```

### Issue: Products not showing
**Solution:**
```
1. Verify WooCommerce is activated
2. Go to: Dashboard → Products
3. Create/Verify at least 8 products exist
4. Mark some as "Featured"
5. Verify product images are uploaded
6. Check: WooCommerce → Settings → Products
```

### Issue: Categories not showing
**Solution:**
```
1. Go to: Dashboard → Products → Categories
2. Create categories: Heaters, Electronics, Cosmetics
3. Upload category images
4. Assign products to categories
5. Go to: WooCommerce → Settings → Catalog
6. Verify "Default category" is set
```

### Issue: Styles not loading
**Solution:**
```
1. Go to: Dashboard → Appearance → Customize
2. Check if CSS file loads (F12 → Network tab)
3. Verify: /assets/css/homepage.css exists
4. Try: Dashboard → Appearance → Themes
   → Astra Child → Customize (hard refresh with Ctrl+Shift+R)
```

### Issue: Checkout page not found
**Solution:**
```
1. Go to: WooCommerce → Settings → Advanced
2. Verify Shop page is set
3. Go to: WooCommerce → Settings → Account
4. Create/Set a page for "My Account"
5. Verify checkout endpoint exists
```

---

## 📝 Customization Guide

### Change Brand Colors
**File:** `/assets/css/homepage.css` - Lines 8-17

```css
:root {
  --brand-color: #EC234A;        /* ← Change primary red */
  --brand-dark: #C9193A;         /* ← Change dark red */
  --accent-blue: #3A7AFE;        /* ← Change blue */
  /* ... etc */
}
```

### Change Product Count
**Featured Products:**
```php
// File: template-parts/home/featured-products-section.php
// Line: ~20
'posts_per_page' => 8,    /* ← Change to 12, 16, etc */
```

### Change Category Count
```php
// File: template-parts/home/category-strip.php
// Line: ~17
'number' => 4,            /* ← Change to 6, 8, etc */
```

### Modify Hero Section Content
```php
// File: template-parts/home/hero-section.php
// Line: ~20-50
// Edit title, subtitle, button text, and links
```

---

## 🔐 Security Features

All templates include:
- `esc_url()` for URLs
- `esc_html()` for text
- `wp_kses_post()` for HTML content
- `esc_attr()` for HTML attributes
- Translation functions `esc_html__()`, `esc_html_e()`

---

## 📞 Support & Resources

### File Locations
```
Theme Root: wp-content/themes/astra-child/
├── home.php                          (Homepage)
├── account.php                       (Account/Login)
├── template-parts/
│   ├── home/
│   │   ├── hero-section.php
│   │   ├── category-strip.php
│   │   ├── featured-products-section.php
│   │   ├── promos-section.php
│   │   ├── perks-section.php
│   │   └── footer-section.php
│   └── account/
│       └── order-tracking.php
├── woocommerce/
│   └── checkout/
│       └── form-checkout.php
├── assets/
│   └── css/
│       └── homepage.css
└── inc/
    └── enqueue.php                   (Style loading)
```

### Next Steps
1. ✅ Activate theme (Appearance → Themes)
2. ✅ Create product categories and products
3. ✅ Configure WooCommerce settings
4. ✅ Test all pages and functionality
5. ✅ Upload real images
6. ✅ Customize content/colors as needed

---

**Theme Version:** 1.0  
**Created:** 2024  
**For:** KachoTech E-Commerce Store
