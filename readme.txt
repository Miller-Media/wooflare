=== WooFlare ===
Contributors: MillerMediaNow, mikemm01
Tags: cache, speed, cloudflare, woocommerce, sales, product, category
Requires PHP: 8.1
Requires at least: 3.0
Tested up to: 6.9
Stable tag: 1.0.7
License: GPLv2

WooFlare provides automated Cloudflare cache control for WooCommerce stores.

== Description ==

**Did you find this plugin helpful?** Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/wooflare/).

Cloudflare provides caching, security and optimization services for websites all around the internet. WooFlare integrates Cloudflare directly with WooCommerce stores to allow store owners to automate these optimizations in a variety of ways (when a product goes out of stock, when a sale ends, etc.).

Automate your WooCommerce store's caching optimization and free up more time to sell your products!

Please help by contributing to the GitHub repository [WooFlare](https://github.com/Miller-Media/wooflare/)

** This plugin relies on the use of Cloudflare, a third-party service that increases security and performance for web sites and services across the internet. For more information, visit the [Cloudflare website](https://www.cloudflare.com/) and their [privacy policy](https://www.cloudflare.com/privacypolicy/). This plugin is not officially endorsed, built or maintained by the Cloudflare team; rather, we are a development company that uses their services every day! **

== Installation ==

1. Upload `WooFlare` to the `/wp-content/plugins/` directory
1. Activate the plugin through the _Plugins_ menu in WordPress
1. Configure plugin through admin menu _WooFlare_

== Request ==

If you find that a part of this plugin isn't working, please don't simply click the Wordpress "It's broken" button. Let us know what's broken in [its support forum](https://wordpress.org/support/plugin/wooflare/) so we can make it better.

== Changelog ==
= 1.0.7 =
* Added dismissible review prompt notice after 14 days of usage

= 1.0.6 =
* Added translations for Spanish, French, German, Portuguese (Brazilian), and Italian

= 1.0.5 =
* Added support for scoped API tokens as an alternative to Global API Key
* Added connected state UI with credential status bar and masked credential preview
* Added Disconnect action to easily switch authentication methods
* Added internationalization (i18n) support for all user-facing strings
* Labeled Global API Key as Legacy, recommending API Token for new installs
* Added required Cloudflare token permissions in API Token field description

= 1.0.4 =
* Removed dead code copied from CFIP plugin
* Added output escaping to settings page
* Scoped nonce verification to plugin settings page only
* Removed unnecessary error suppression on nonce verification

= 1.0.3 =
* Fixed bug where Cloudflare credentials were not being saved
* Improved setup instructions and Store Notice description
* Improved settings UI layout and tab styling

= 1.0.2 =
* Improved setup instructions for Cloudflare API credentials

= 1.0.1 =
* Compatibility updates for WordPress 6.9 and PHP 8.1+
* Fixed PHP 8.2 dynamic property deprecation warnings

= 1.0 =
* Initial public release