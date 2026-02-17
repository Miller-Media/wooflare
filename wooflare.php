<?php
/**
 * Plugin Name: Wooflare
 * Plugin URI: https://wordpress.org/plugins/wooflare/
 * Description: WooCommerce integration with Cloudflare.
 * Author: Miller Media
 * Author URI: https://mattmiller.ai
 * Depends:
 * Text Domain: wooflare
 * Version: 1.2.2
 * Requires PHP: 8.1
 * Tested up to: 6.9
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect if WooCommerce version is at least 3.3.0 (required for a couple of hooks).
 *
 * @param string $version
 * @return bool
 */
function version_check( $min_version = '3.3.0' ) {
    $woo_version = get_option('woocommerce_version');
    if ( $woo_version && version_compare( $woo_version, $min_version, ">=" ) ) {
        return true;
    }

    return false;
}

/**
 * If minimum WooCommerce version is not met, display a notice indicating as much.
 *
 * @return bool|string
 */
function woocf_version_check()
{
    /**
     * Check if WooCommerce is active
     **/
    $notice_message = false;

    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $notice_message = __('Wooflare requires <a href="http://wordpress.org/plugins/woocommerce" target="_blank">WooCommerce</a> to be installed and activated.', 'wooflare');
    }

    if( !version_check('3.3.0') ){
        $notice_message = __('Wooflare requires a <a href="http://wordpress.org/plugins/woocommerce" target="_blank">WooCommerce</a> version of at least 3.3.0 to function correctly. Please update.', 'wooflare');
    }

    if(!$notice_message){
        return false;
    }

    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php echo wp_kses_post( $notice_message ); ?></p>
    </div>
    <?php
}
add_action('admin_notices', 'woocf_version_check');

define( 'WOOCF_PLUGIN_VERSION', '1.2.2' );
define( 'WOOCF_PLUGIN_FILE', __FILE__ );
define( 'WOOCF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WOOCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCF_MAIN_CLOUDFLARE_PLUGIN_DIR', plugins_url( 'cloudflare/', WOOCF_PLUGIN_FILE ) );

add_action('plugins_loaded', function() {
    load_plugin_textdomain('wooflare', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

include_once('classes/Helpers.php');
include_once('classes/Plugin.php');
include_once('classes/SiteSettings.php');
include_once('classes/CloudflareAPIController.php');
include_once('classes/ReviewNotice.php');

register_activation_hook( __FILE__, function() {
	if ( ! get_option( 'woocf_activated_on' ) ) {
		update_option( 'woocf_activated_on', time() );
	}
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
	$settings_link = '<a href="' . admin_url('admin.php?page=woocf-menu') . '">' . __('Settings', 'wooflare') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
});

$WOOCF_Main = new WOOCF_Main();
new WOOCF_ReviewNotice( 'Wooflare', 'wooflare', 'woocf_activated_on', 'wooflare', plugin_dir_url( __FILE__ ) . 'assets/plugin-icon.jpg' );