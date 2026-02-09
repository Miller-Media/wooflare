# Wooflare

## Overview
WooCommerce integration with Cloudflare. Automatically clears Cloudflare cache when WooCommerce events occur (scheduled sale ends, products go out of stock, store notice changes).

## Architecture

```
wooflare.php                       # Entry point, WC version check, defines constants
classes/
├── Plugin.php                     # WOOCF_Main - main controller, WooCommerce hooks
├── CloudflareAPIController.php    # WOOCF_CloudflareAPIController - API requests
├── SiteSettings.php               # WOOCF_SiteSettings - settings + credentials
└── Helpers.php                    # WOOCF_Helpers - URL parsing
views/
└── SiteSettingsView.php           # Admin settings page template
assets/
├── css/admin.css
└── js/admin.js
```

## Key Classes

### WOOCF_Main (Plugin.php)
Main controller. Registers WooCommerce event hooks based on settings.

**Cache Clearing Methods:**
- `clearCacheScheduledSaleEnd($product_ids)` - Triggered by `wc_after_products_ending_sales`, clears product + category URLs
- `clearCacheProduct($product)` - Clears cache for a single product + its categories
- `checkOutOfStock($product_id, $status, $product)` - Fires `clearCacheProduct` if status is `outofstock`
- `clearCacheStoreNoticeAdded($option, $value)` - Cache clear on notice option creation
- `clearCacheStoreNoticeUpdated($old, $new, $option)` - Cache clear on notice option update

**URL Gathering:**
- `getProductAndCategoryURLs($product)` - Returns product permalink + category URLs
- `getAllProductAndCategoryURLs()` - All published product + category URLs
- `getAllURLs()` - Entire site (posts, pages, products)

### WOOCF_CloudflareAPIController (CloudflareAPIController.php)
Cloudflare API v4 communication.

- `request($method, $url, $body)` - Master HTTP method
- `clearCacheByFiles($files)` - Purge specific URLs (batches of 30)
- `purgeCache()` - Purge entire zone cache
- `getZones()` - List all zones
- `getZoneId($domain)` - Resolve domain to zone ID (cached)

### WOOCF_SiteSettings (SiteSettings.php)
Settings management (mirrors cloudflare-ip-blacklist pattern).

## WooCommerce Hooks
- `wc_after_products_ending_sales` - Scheduled sale end
- `woocommerce_no_stock_notification` - Out of stock notification
- `woocommerce_variation_set_stock_status` - Variation stock change
- `woocommerce_product_set_stock_status` - Product stock change
- `add_option_woocommerce_demo_store` / `update_option_woocommerce_demo_store` - Store notice toggle
- `add_option_woocommerce_demo_store_notice` / `update_option_woocommerce_demo_store_notice` - Store notice text

## Settings (wp_options)
- `woocf_settings` - CF credentials, zone ID, feature flags (`after_scheduled_sale`, `when_product_out_of_stock`)
- `woocf_log` - Debug log
- `woocf_notice_toggled` - Race condition prevention for store notice updates

## Requirements
- WooCommerce 3.3.0+ (version check in entry point)

## Testing
Tests are in `../tests/unit/wooflare/`. Run with:
```bash
make test-plugin PLUGIN=wooflare
```

## API Endpoints Used
- `GET /zones` - List zones
- `GET /zones?name={domain}` - Find zone by domain
- `DELETE /zones/{id}/purge_cache` - Purge by files or entire zone

## Important Notes
- Cache purge batches URLs in groups of 30 (Cloudflare API limit)
- Store notice has a race condition prevention mechanism (`woocf_notice_toggled`)
- Shares same Cloudflare API pattern as cloudflare-ip-blacklist
- Can inherit credentials from official Cloudflare plugin
