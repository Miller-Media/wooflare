=== WooFlare ===
Contributors: MillerMediaNow, mikemm01
Tags: cloudflare, woocommerce, cache, speed, product
Requires PHP: 8.1
Requires at least: 3.0
Tested up to: 6.9.1
Stable tag: 1.1.1
License: GPLv2

WooFlare provides automated Cloudflare cache control for WooCommerce stores.

== Description ==

**Did you find this plugin helpful?** Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/wooflare/).

Cloudflare provides caching, security and optimization services for websites all around the internet. WooFlare integrates Cloudflare directly with WooCommerce stores to allow store owners to automate these optimizations in a variety of ways (when a product goes out of stock, when a sale ends, etc.).

Automate your WooCommerce store's caching optimization and free up more time to sell your products!

Please help by contributing to the GitHub repository [WooFlare](https://github.com/Miller-Media/wooflare/)

** This plugin relies on the use of Cloudflare, a third-party service that increases security and performance for web sites and services across the internet. For more information, visit the [Cloudflare website](https://www.cloudflare.com/) and their [privacy policy](https://www.cloudflare.com/privacypolicy/). This plugin is not officially endorsed, built or maintained by the Cloudflare team; rather, we are a development company that uses their services every day! **

== Localizations ==
This plugin is available in the following languages:

* Albanian (Shqip)
* Arabic (العربية)
* Armenian (Հայերեն)
* Basque (Euskara)
* Bengali (বাংলা)
* Bulgarian (Български)
* Catalan (Català)
* Chinese Simplified (简体中文)
* Croatian (Hrvatski)
* Czech (Čeština)
* Danish (Dansk)
* Dutch (Nederlands)
* Estonian (Eesti)
* Finnish (Suomi)
* French (Français)
* Galician (Galego)
* Georgian (ქართული)
* German (Deutsch)
* Greek (Ελληνικά)
* Hebrew (עברית)
* Hindi (हिन्दी)
* Hungarian (Magyar)
* Indonesian (Bahasa Indonesia)
* Irish (Gaeilge)
* Italian (Italiano)
* Japanese (日本語)
* Korean (한국어)
* Latvian (Latviešu)
* Lithuanian (Lietuvių)
* Macedonian (Македонски)
* Norwegian (Norsk)
* Persian (فارسی)
* Persian - Afghanistan (دری)
* Polish (Polski)
* Portuguese - Brazil (Português do Brasil)
* Portuguese - Portugal (Português)
* Romanian (Română)
* Russian (Русский)
* Serbian (Српски)
* Slovak (Slovenčina)
* Slovenian (Slovenščina)
* Spanish (Español)
* Swedish (Svenska)
* Tamil (தமிழ்)
* Thai (ไทย)
* Turkish (Türkçe)
* Ukrainian (Українська)
* Urdu (اردو)
* Vietnamese (Tiếng Việt)
* Welsh (Cymraeg)

== Installation ==

1. Upload `WooFlare` to the `/wp-content/plugins/` directory
1. Activate the plugin through the _Plugins_ menu in WordPress
1. Configure plugin through admin menu _WooFlare_

== Frequently Asked Questions ==

= How do I connect WooFlare to my Cloudflare account? =

Go to WooFlare in your WordPress admin menu. You can authenticate using either a scoped API Token (recommended) or your Global API Key (legacy). For API Tokens, create one in your Cloudflare dashboard with the "Zone > Cache Purge > Purge" permission.

= When does WooFlare automatically purge the cache? =

WooFlare can purge Cloudflare cache when products go out of stock, when sales start or end, and when product data is updated. You can configure which events trigger a cache purge in the plugin settings.

= Does WooFlare require WooCommerce? =

Yes. WooFlare is designed specifically for WooCommerce stores and requires WooCommerce to be installed and active.

= What PHP and WordPress versions are supported? =

The plugin requires PHP 8.1 or higher and has been tested up to WordPress 6.9.1.

= What languages are supported? =

The plugin is available in 30 languages with more being added regularly. We are working toward supporting 50 languages total!

== Request ==

If you find that a part of this plugin isn't working, please don't simply click the Wordpress "It's broken" button. Let us know what's broken in [its support forum](https://wordpress.org/support/plugin/wooflare/) so we can make it better.

== Changelog ==

= 1.1.1 =
* Added translations for Russian, Polish, Dutch, Turkish, and Swedish
* Updated localization section in readme

= 1.1.0 =
* Added Chinese Simplified (zh_CN) translation
= 1.0.9 =
* Added Japanese (ja) translation

= 1.0.8 =
* Tested up to WordPress 6.9.1

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