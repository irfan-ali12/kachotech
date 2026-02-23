# KachoTech Live Deployment Guide

## Issues Fixed for Live Site

### 1. **Category Strip Styling Not Applied**
**Problem:** "Shop Deals by Category" section styling wasn't visible on live site but worked locally.

**Root Cause:** CSS files used hardcoded version '1.0', which prevented cache from updating when files changed.

**Solution Implemented:**
- Updated `inc/enqueue.php` to use `filemtime()` instead of hardcoded versions
- All CSS/JS files now automatically update in browser cache when modified

**Files Modified:**
- `inc/enqueue.php` - All `wp_enqueue_style()` and `wp_enqueue_script()` calls now use file modification time

### 2. **Featured Products Not Showing Heaters First**
**Problem:** Featured products should prioritize heaters category, but rule wasn't applied on live site.

**Root Cause:** AJAX responses were being cached by server-side cache plugins, preventing fresh data from loading.

**Already Implemented:**
- `functions.php` - Featured products AJAX has LiteSpeed cache disabling
- `inc/shop-ajax.php` - Shop filter AJAX has LiteSpeed cache disabling
- Both files send no-cache headers

---

## Deployment Checklist

### Step 1: Deploy Files
When deploying theme to live site, ensure these files are uploaded:
- All files in `assets/css/` - especially `category-strip.css`, `shop-layout.css`, `tailwind.min.css`
- All files in `assets/js/` - especially `shop.js`
- `inc/enqueue.php` - contains new cache-busting logic
- `functions.php` - featured products AJAX
- `inc/shop-ajax.php` - shop filtering AJAX

### Step 2: Rebuild Tailwind CSS
**IMPORTANT:** After deploying, rebuild Tailwind CSS on live server:

```bash
cd /path/to/wp-content/themes/astra-child
npm install  # Only needed first time
npm run build
```

This generates `assets/css/tailwind.min.css` with all latest styles.

### Step 3: Clear All Caches

#### LiteSpeed Cache (if enabled)
1. Go to **WordPress Admin → LiteSpeed Cache → Purge All**
2. Or run: `wp litespeed-cache flush` (WP-CLI)

#### CloudFlare Cache (if enabled)
1. Go to **Cloudflare Dashboard → Caching → Purge Cache**
2. Select "Purge Everything"

#### Browser Cache
- **Chrome/Firefox:** Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
- Or: Press F12 → DevTools → Right-click reload button → Empty cache and hard refresh

#### WordPress Cache (if using cache plugin)
- WP Super Cache: Admin → WP Super Cache → Delete Cache
- W3 Total Cache: Admin → Performance → Purge All Caches

### Step 4: Test
After deployment:
1. **Category Strip:** Check homepage - should see colored category boxes with proper styling
2. **Featured Products:** Check homepage hero section - should show heater products first, sorted by rating
3. **Mobile Filters:** Check shop page on mobile - filters should apply correctly
4. **Category Filters:** Check category page on mobile - filters should apply correctly

---

## How Cache Busting Works

### Before (Broken)
```php
wp_enqueue_style('kt-category-strip-css', ...uri, '1.0');  // Always '1.0'
```
Browser caches as: `category-strip.css?ver=1.0`
- If file changes, browser still uses old cached version
- Cache never updates!

### After (Fixed)
```php
wp_enqueue_style('kt-category-strip-css', ...uri, filemtime(...));
```
Browser caches as: `category-strip.css?ver=1702050123` (timestamp)
- If file changes, timestamp changes
- Browser downloads fresh version automatically!

---

## If Issues Persist

### Category Strip Not Showing
1. Check browser DevTools (F12) → Network tab
   - Is `category-strip.css` loading? Should see 200 status
   - Is `tailwind.min.css` loading? Should see 200 status
2. Check if `tailwind.min.css` file exists and is non-empty
   - If missing/empty, run `npm run build`
3. Check PHP error logs for any `filemtime()` warnings
4. Clear ALL caches (see Step 3 above)

### Featured Products Not Sorting Correctly
1. Check browser DevTools → Network → XHR (AJAX requests)
   - Look for `admin-ajax.php?action=kt_load_featured_products`
   - Check Response tab - is JSON valid?
2. Check WordPress error logs
3. Verify database has products with ratings
   - Some products may not have `_wc_average_rating` meta
4. Check if LiteSpeed cache is actually disabled
   - Add this to test: `add_filter( 'litespeed_control_set_cacheable', '__return_true' );`

### Mobile Filters Not Working
1. Check browser console (F12 → Console) for JavaScript errors
2. Look for AJAX requests in Network tab
   - Check if filter data is being sent
   - Check Response for correct product list
3. Verify `shop.js` is loaded and latest version
4. Check if category_id is being passed correctly

---

## Important Notes

⚠️ **Tailwind CSS Build Required**
- Every time you edit HTML/PHP/JS that affects Tailwind class names, rebuild:
  ```bash
  npm run build
  ```
- Development: Use `npm run watch` to auto-rebuild on file changes

⚠️ **Cache Plugins Can Override**
- Cache plugins might ignore `Cache-Control` headers
- Must explicitly disable caching in plugin settings for AJAX endpoints
- LiteSpeed already configured: `litespeed_control_set_cacheable( false )`

⚠️ **File Permissions**
- On live server, ensure `assets/css/` and `assets/js/` are writable
- `filemtime()` needs read access to files

---

## Version History

- **Dec 8, 2025** - Implemented automatic cache busting with filemtime()
- **Earlier** - Added mobile filters, fixed featured products sorting

---

**Questions?** Check:
1. Browser console for JavaScript errors
2. WordPress debug.log for PHP errors
3. Network tab in DevTools for failed requests
4. LiteSpeed Cache control panel for blocked content
