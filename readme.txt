=== WooCommerce Cloudflare ===
Contributors: MillerMediaNow, mikemm01
Tags: security, login, passwords, password, profile, users, brute force, username, banned usernames, cloudflare
Requires PHP: 5.6
Requires at least: 3.0
Tested up to: 5.3.2
Stable tag: 1.0.0
License: GPLv2

Blacklist IP addresses that attempt to login with a banned username through Cloudflare.

== Description ==

Cloudflare provides security and optimization services for websites all around the internet. WordPress sites are often attacked by bots or hackers and, while Cloudflare has preset firewall rules to help, it doesn't always filter out all malicious activity.

Cloudflare IP Blacklist allows WordPress admins to add a list of prohibited usernames and if someone attempts to log in with one of these usernames, this users IP address is automatically added to the Cloudflare blacklist. By doing this, the next time this user attempts to load the site, they will be blocked by Cloudflare before their requests get to your site's server. Add an extra layer of security today to your site!

** This plugin relies on the use of Cloudflare, a third-party service that increases security and performance for web sites and services across the internet. For more information, visit the [Cloudflare website](https://www.cloudflare.com/) and their [privacy policy](https://www.cloudflare.com/privacypolicy/). This plugin is not officially endorsed, built or maintained by the Cloudflare team; rather, we are a development company that uses their services every day! **

== Installation ==

1. Upload `IP Blacklist for Cloudflare` to the `/wp-content/plugins/` directory
1. Activate the plugin through the _Plugins_ menu in WordPress
1. Configure plugin through admin menu _IP Blacklist for Cloudflare_

== Request ==

If you find that a part of this plugin isn't working, please don't simply click the Wordpress "It's broken" button. Let us know what's broken in [its support forum](https://wordpress.org/support/plugin/cloudflare-ip-blacklist/) so we can make it better.

== Screenshots ==

1. Settings screen
2. Logging screen

== Changelog ==
= 1.0 =
* Initial public release

