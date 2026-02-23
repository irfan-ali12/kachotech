# Filter & Sidebar Files Reference Guide
**Date:** December 23, 2025  
**Purpose:** Identification and categorization of files responsible for shop, category & sub-category sidebar filters

---

## 📋 Executive Summary

The sidebar filter system is handled by a combination of **PHP template files**, **JavaScript AJAX handlers**, **CSS styling**, and **PHP backend logic**. Below is the complete breakdown organized by responsibility.

---

## 🏗️ Architecture Overview

```
Filter System Flow:
┌─────────────────────────────────────────────────────────────┐
│  User Interaction (HTML/UI)                                 │
│  ├─ archive-product.php (Shop Page)                        │
│  └─ taxonomy-product_cat.php (Category Pages)              │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│  JavaScript Event Handlers & AJAX Setup                     │
│  └─ assets/js/shop.js                                       │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│  Backend AJAX Processing & Data Query                       │
│  └─ inc/shop-ajax.php                                       │
└──────────────────┬──────────────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────────────┐
│  Filter Styling & UI Presentation                           │
│  └─ assets/css/shop-layout.css                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 File Breakdown by Category

### 1. **TEMPLATE FILES** (UI Structure & Markup)

#### [woocommerce/archive-product.php](woocommerce/archive-product.php)
**Responsibility:** Main shop page with sidebar filters  
**Key Functions:**
- `kt_get_product_price_range()` - Calculates min/max price for the price slider
- `kt_modify_shop_query()` - Filters products to exclude those with no/zero price
- Enqueues shop CSS, JavaScript, and AJAX data
- Renders filter sidebar with:
  - **Price Slider** (min/max range)
  - **Category Filter** (checkboxes/pills)
  - **Brand Filter** (pills)
  - **Availability Filter** (In Stock / Out of Stock)
  - **Rating Filter** (1-5 star options)
- Includes mobile filter drawer overlay
- **Lines:** 848 total lines

#### [woocommerce/taxonomy-product_cat.php](woocommerce/taxonomy-product_cat.php)
**Responsibility:** Category & Sub-category archive pages with category-specific filters  
**Key Functions:**
- `kt_get_category_price_range()` - Calculates price range for specific category
- `kt_get_category_attributes()` - Gets available attributes/filters for the category
- Category-specific styling using color schemes
- Renders category featured image & description
- Similar sidebar filters to archive page but scoped to category
- **Lines:** 834 total lines

---

### 2. **JAVASCRIPT/AJAX FILES** (Client-Side Logic)

#### [assets/js/shop.js](assets/js/shop.js)
**Responsibility:** AJAX filter interactions, events, and UI updates  
**Key Features:**
- **Global Filter State Management:**
  - Tracks: `categories`, `availability`, `brands`, `min_rating`, `max_price`, `search`, `orderby`, `paged`
- **Filter Events:**
  - Brand pill clicks (`.kt-pill`, `.kt-pill-mobile`)
  - Rating filter changes (`input[name="rating"]`)
  - Category filter changes (`.kt-category-filter`)
  - Availability filter changes (`.kt-availability-filter`)
  - Price range changes
  - Search input handling

- **Mobile Filter Drawer:**
  - Toggle buttons: `#kt-open-filters` (open), `#kt-close-filters` (close)
  - Overlay drawer: `#kt-filter-drawer`
  - Mobile-specific filter handling with "Apply" button

- **Key Functions:**
  - `applyFiltersAjax()` - Main AJAX call to filter products
  - Price slider initialization using Ion Range Slider
  - Pagination handling

- **Lines:** 657 total lines

---

### 3. **BACKEND AJAX HANDLER** (Server-Side Processing)

#### [inc/shop-ajax.php](inc/shop-ajax.php)
**Responsibility:** Process filter requests and return filtered products  
**Key Functions:**
- `kt_filter_products_ajax()` - Main AJAX endpoint
- **Sanitizes & processes:**
  - Categories (comma-separated list)
  - Availability status
  - Brands
  - Minimum rating
  - Maximum price
  - Search term
  - Pagination (paged, posts_per_page)
  - Order/sorting

- **Builds WooCommerce query with:**
  - `meta_query` for price filtering
  - `tax_query` for categories/attributes
  - `s` parameter for search
  - Proper nonce verification for security

- **Security Features:**
  - Nonce verification
  - Proper data sanitization
  - Cache control headers
  - Input validation

- **Lines:** 574 total lines

---

### 4. **STYLING FILES** (CSS/Design)

#### [assets/css/shop-layout.css](assets/css/shop-layout.css)
**Responsibility:** All styling for shop layout and filters  
**Key Sections:**
- Filter sidebar styling
- Filter pill buttons (brands, ratings)
- Price slider styling
- Category filter checkboxes
- Mobile responsive design
- Filter drawer overlay styling
- Product grid layout
- Pagination styles

- **Lines:** 1,325 total lines

#### [assets/css/category-strip.css](assets/css/category-strip.css)
**Responsibility:** Category strip/breadcrumb styling (on category pages)

---

## 🔧 Filter Types Handled

| Filter Type | Files Involved | HTML Element | AJAX Parameter |
|---|---|---|---|
| **Price Range** | archive-product.php, taxonomy-product_cat.php, shop.js, shop-ajax.php | Ion Range Slider | `max_price` |
| **Categories** | archive-product.php, taxonomy-product_cat.php, shop.js, shop-ajax.php | Checkboxes | `categories` |
| **Brands/Pills** | archive-product.php, shop.js, shop-ajax.php | Pill buttons | `brands` |
| **Availability** | archive-product.php, shop.js, shop-ajax.php | Checkboxes | `availability` |
| **Ratings** | archive-product.php, shop.js, shop-ajax.php | Radio buttons | `min_rating` |
| **Search/Sort** | archive-product.php, shop.js, shop-ajax.php | Dropdown | `search`, `orderby` |

---

## 📊 Data Flow Example: Category Filter

```
User clicks category checkbox
    ↓
shop.js: $('.kt-category-filter').change() triggered
    ↓
shop.js: applyFiltersAjax() executes
    ↓
AJAX POST to admin-ajax.php
    action=kt_filter_products
    categories=[selected_cats]
    nonce=[security_token]
    ↓
inc/shop-ajax.php: kt_filter_products_ajax() receives request
    ↓
Sanitizes: $cat_array = explode(',', $categories)
    ↓
Builds: $args['tax_query'] with category terms
    ↓
Executes: new WP_Query($args)
    ↓
Returns: JSON with product HTML + pagination
    ↓
shop.js: Receives response
    ↓
shop.js: Updates DOM with new product grid
```

---

## 🎯 Key Configuration Points

### 1. **Price Range Calculation**
- **File:** [woocommerce/archive-product.php](woocommerce/archive-product.php) - Line 1-50
- **File:** [woocommerce/taxonomy-product_cat.php](woocommerce/taxonomy-product_cat.php) - Line 50-100
- Query gets products from database with `_price` meta key
- Used for slider initialization

### 2. **Posts Per Page**
- **Defined in:** [assets/js/shop.js](assets/js/shop.js) - Line 20
- **Value:** 12 products per page
- **Can be modified:** In `kt_filter_products_ajax()` function

### 3. **Filter Nonce Security**
- **Generated in:** [woocommerce/archive-product.php](woocommerce/archive-product.php) via `wp_create_nonce()`
- **Verified in:** [inc/shop-ajax.php](inc/shop-ajax.php)
- **Nonce Action:** `kt_filter_nonce`

### 4. **Mobile vs Desktop**
- Desktop filters apply **immediately** on change
- Mobile filters have:
  - Separate drawer overlay (`#kt-filter-drawer`)
  - Apply button to confirm selections
  - Close button (`#kt-close-filters`)

---

## 🔍 Important Functions

### Backend
| Function | File | Purpose |
|---|---|---|
| `kt_get_product_price_range()` | archive-product.php | Get min/max price for shop |
| `kt_get_category_price_range()` | taxonomy-product_cat.php | Get min/max price for category |
| `kt_filter_products_ajax()` | shop-ajax.php | Process AJAX filter request |
| `kt_modify_shop_query()` | archive-product.php | Exclude $0 products from query |

### Frontend (JavaScript)
| Function | File | Purpose |
|---|---|---|
| `applyFiltersAjax()` | shop.js | Trigger AJAX filter call |
| Filter change handlers | shop.js | Event listeners for all filter types |

---

## 📱 Mobile Filter Implementation

**Mobile-Specific Elements:**
- `#kt-filter-drawer` - Overlay drawer containing mobile filters
- `#kt-open-filters` - Button to open drawer
- `#kt-close-filters` - Button to close drawer
- `.kt-filter-overlay` - Overlay background to close drawer
- `.kt-pill-mobile` - Mobile brand pills
- `input[name="rating-mobile"]` - Mobile rating filter
- `.kt-category-filter-mobile` - Mobile category filter
- `.kt-availability-filter-mobile` - Mobile availability filter

**Behavior:**
- Filters DON'T apply immediately on mobile
- All selections confirmed with "Apply" button
- Drawer slides in from side with animation
- Overlay click closes drawer

---

## 🚀 Related Support Files

- [functions.php](functions.php) - Enqueues and hooks setup
- [inc/enqueue.php](inc/enqueue.php) - Asset enqueueing
- [inc/helpers.php](inc/helpers.php) - Utility functions
- [inc/woocommerce-hooks.php](inc/woocommerce-hooks.php) - WooCommerce hooks

---

## 📝 Quick Reference: File Modification Guide

### To Add a New Filter:
1. Add HTML to [archive-product.php](woocommerce/archive-product.php) and [taxonomy-product_cat.php](woocommerce/taxonomy-product_cat.php)
2. Add event handler in [shop.js](assets/js/shop.js)
3. Add sanitization in [shop-ajax.php](inc/shop-ajax.php)
4. Add styling in [shop-layout.css](assets/css/shop-layout.css)
5. Modify WP_Query args in `kt_filter_products_ajax()`

### To Modify Price Range:
1. Update calculation in [archive-product.php](woocommerce/archive-product.php)
2. Update calculation in [taxonomy-product_cat.php](woocommerce/taxonomy-product_cat.php)
3. Adjust slider settings in [shop.js](assets/js/shop.js)

---

## ✅ Summary Table

| Aspect | Responsibility | Primary Files |
|---|---|---|
| **Shop Page UI** | Display filters & products | `archive-product.php` |
| **Category Page UI** | Category-specific filters | `taxonomy-product_cat.php` |
| **Filter Events** | Handle user interactions | `shop.js` |
| **AJAX Processing** | Server-side filtering | `shop-ajax.php` |
| **Styling** | Visual presentation | `shop-layout.css` |
| **Price Calculation** | Min/max values | `archive-product.php`, `taxonomy-product_cat.php` |
| **Mobile Handling** | Mobile-specific UI/UX | `shop.js`, `shop-layout.css` |

---

**Last Updated:** December 23, 2025
