<?php
/**
 * Plugin Name: Wooflare
 * Plugin URI:
 * Description: WooCommerce integration with Cloudflare.
 * Author: Miller Media
 * Author URI: www.millermedia.io
 * Depends:
 * Text Domain: wooflare
 * Version: 1.0.4
 * Requires PHP: 8.1
 * Tested up to: 6.9
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 9.6
 */

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
        $notice_message = 'Wooflare requires <a href="http://wordpress.org/plugins/woocommerce" target="_blank">WooCommerce</a> to be installed and activated.';
    }

    if( !version_check('3.3.0') ){
        $notice_message = 'Wooflare requires a <a href="http://wordpress.org/plugins/woocommerce" target="_blank">WooCommerce</a> version of at least 3.3.0 to function correctly. Please update.';
    }

    if(!$notice_message){
        return false;
    }

    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e( $notice_message, 'wooflare' ); ?></p>
    </div>
    <?php
}
add_action('admin_notices', 'woocf_version_check');

define('WOOCF_PLUGIN_VERSION', '1.0.4');
define('WOOCF_MAIN_CLOUDFLARE_PLUGIN_DIR', plugins_url('cloudflare'));

include_once('classes/Helpers.php');
include_once('classes/Plugin.php');
include_once('classes/SiteSettings.php');
include_once('classes/CloudflareAPIController.php');

$WOOCF_Main = new WOOCF_Main();