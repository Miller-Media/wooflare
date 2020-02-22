<?php

if (!defined('ABSPATH')) {
    die('Access denied.');
}

/**
 * Plugin Class
 */
class WOOCF_Main
{

    /**
     * The settings class.
     *
     * @var WOOCF_Settings
     */
    public $siteSettings;

    /**
     * The plugin settings.
     *
     * @var mixed
     */
    public $settings;

    /**
     * The API class.
     *
     * @var WOOCF_CloudflareAPIController
     */
    public $API;

    /**
     * Admin stylesheet file.
     *
     */
    public $adminStyle;

    /**
     * Admin javascript file.
     *
     */
    public $adminScript;

    /**
     * WOOCF_Main constructor.
     *
     * Initialize plugin properties/hooks.
     *
     */
    public function __construct ()
    {
        $this->siteSettings = new WOOCF_SiteSettings();
        $this->API = new WOOCF_CloudflareAPIController();
        $this->settings = get_site_option('woocf_settings');

        $this->adminStyle = plugins_url('woocommerce-cloudflare/assets/css/admin.css', 'woocommerce-cloudflare.php');
        $this->adminScript = plugins_url('woocommerce-cloudflare/assets/js/admin.js', 'woocommerce-cloudflare.php');

        // Hooks
        add_action('admin_enqueue_scripts', array ($this, 'adminEnqueueScripts'), 40, 1);

        // AJAX requests
        add_action('wp_ajax_woocf_clearlog', array ($this, 'ajaxClearLog'));
	    add_action('wp_ajax_woocf_loadlog', array ($this, 'ajaxLoadLog'));

        // Single Site Settings Screen
        add_action('admin_menu', array($this->siteSettings, 'addSiteMenu'));
        add_action('admin_menu', array($this->siteSettings, 'verifyNonce'));

        // Cache Clear on scheduled sale end.
        add_action('wc_after_products_ending_sales', array ($this, 'clearCacheScheduledSaleEnd'), 90, 1);

        // Cache Clear on product out-of-stock.
        add_action('woocommerce_no_stock_notification', array ($this, 'clearCacheProductOutOfStock'), 40, 1);

        /**
         * Store Notice
         *
         * IMPORTANT: The order of these actions is very important to the clearCacheStoreNoticeUpdated function.
         *
         * Potential race condition for these:
         * In the event of the checkbox value changing (enable/disable) AND the text changing, the cache
         * clear happens on the woocommerce_demo_store update but is prevented on the demo_store_notice update
         * because we don't want it to be sent twice. Technically, if the cache is cleared on the Cloudflare end
         * and propagates BEFORE the text has changed on the site, the text changes may not be reflected. This
         * most likely will not happen as an API call will generally be slower than the update of a value on the
         * DB and a site load.
         */
        // Cache Clear on store notice being toggled on or off
        add_action('add_option_woocommerce_demo_store', array ($this, 'clearCacheStoreNoticeUpdated'), 40, 2);
        add_action('update_option_woocommerce_demo_store', array ($this, 'clearCacheStoreNoticeUpdated'), 40, 3);

        // Cache Clear on store notice text update.
        add_action('add_option_woocommerce_demo_store_notice', array ($this, 'clearCacheStoreNoticeUpdated'), 40, 2);
        add_action('update_option_woocommerce_demo_store_notice', array ($this, 'clearCacheStoreNoticeUpdated'), 40, 3);

        // Add 'Settings' link to plugin page
        //add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), array ($this, 'woocf_add_action_links'), 10, 5);
    }

    /**
     * Enqueue admin scripts and stylesheets.
     *
     * @param $hook string
     */
    public function adminEnqueueScripts ($hook)
    {
        // Only enqueue on appropriate admin screen.
        if ($hook !== 'toplevel_page_woocf-menu')
            return;

        wp_enqueue_style('woocf_admin_style', $this->adminStyle, array(),WOOCF_PLUGIN_VERSION);
        wp_enqueue_script('woocf_admin_script', $this->adminScript, array ('jquery'),WOOCF_PLUGIN_VERSION);

        // If Cloudflare plugin is active, inherit styles from their plugin for consistent styles
        if($this->isCloudflarePluginActive()) {
            wp_enqueue_style('cf-corecss', WOOCF_MAIN_CLOUDFLARE_PLUGIN_DIR.'/stylesheets/cf.core.css');
            wp_enqueue_style('cf-componentscss', WOOCF_MAIN_CLOUDFLARE_PLUGIN_DIR.'/stylesheets/components.css');
            wp_enqueue_style('cf-hackscss', WOOCF_MAIN_CLOUDFLARE_PLUGIN_DIR.'/stylesheets/hacks.css');
        }
    }

    /**
     * Add Links to main WP plugin page
     */
    /*public function woocf_add_action_links( $actions ) {
        $custom_actions = array(
            'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=woocf-menu' ), 'Settings' )
        );

        // add the links to the front of the actions list
        return array_merge( $custom_actions, $actions );
    }*/

    /**
     * Check if official Cloudflare Plugin is installed and active
     */
    public function isCloudflarePluginActive(){
        return is_plugin_active( 'cloudflare/cloudflare.php' );
    }

    /**
     * Function that triggers a cache-clear for all product and
     * category endpoints when a scheduled sale ends.
     *
     * @param $product_ids
     */
    public function clearCacheScheduledSaleEnd ($product_ids)
    {
        // If the option is disabled, bail.
        if (!($this->getSetting('after_scheduled_sale') == 'on'))
            return;

        $this->API->purgeCache();
    }

    /**
     * Function that triggers a cache-clear for a product and
     * its category endpoints when it crosses the Out-of-Stock
     * threshold.
     *
     * @param $product WC_Product
     */
    public function clearCacheProductOutOfStock ($product)
    {
        // If the option is disabled, bail.
        if (!($this->getSetting('when_product_out_of_stock') == 'on'))
            return;

        $files = $this->getProductAndCategoryURLs($product);
        $this->API->clearCacheByFiles($files);
    }

    /**
     * Function that triggers a cache-clear for all product and
     * category endpoints when the sitewide store notice is updated (text or enabled/disabled).
     *
     * @param $option | string
     * @return string
     */
    public function clearCacheStoreNoticeUpdated ($old_option, $new_option, $option)
    {
        /**
         * If the option in WooFlare is disabled, bail.
         */
        if (!($this->getSetting('when_store_notice_updated') == 'on'))
            return $new_option;

        /**
         * Purge cache every time when the option is added for the first time
         */
        if(strpos(current_filter(),'add_option_')!==false){
            $this->API->purgeCache();
            return $new_option;
        }

        /**
         * In all cases where the option is enabled and disabled and old option doesn't
         * equal new option, we want to clear cache.
         *
         * We check the values, choose to purge cache or not and bail.
         */
        $option_changed = ($old_option != $new_option);
        $notice_toggled = get_option('woocf_notice_toggled');

        if($option=='woocommerce_demo_store') {
            // Clean option in case text changed hasn't run since the last time (two toggles back to back)
            update_option('woocf_notice_toggled', 'no');

            if($option_changed) {
                $this->API->purgeCache();
                update_option('woocf_notice_toggled', 'yes');
            }
        }

        /**
         * In the event of a text change.
         */
        if($option=='woocommerce_demo_store_notice') {
            // Get the current disabled/enabled value
            $enabled = (get_option('woocommerce_demo_store') == 'yes');

            /**
             * If enabled and text has changed, in all cases we purge cache.
             *
             * We also check to make sure that the cache wasn't just cleared if the
             * checkbox just recently changed its value. If it did, we don't want
             * to clear the cache twice.
             */
            if($enabled && $option_changed && !$notice_toggled)
                $this->API->purgeCache();
        }

        return $new_option;
    }

    /**
     * Get product and category URLs for given product.
     *
     * @param $product WC_Product
     * @return array
     */
    public function getProductAndCategoryURLs ($product)
    {
        // Array to hold all product and category URLs
        $files = array ();

        // Add product and category permalinks to $files array, skipping dupes.
        $prod_id = $product->get_id();
        $terms = get_the_terms($prod_id, 'product_cat');
        if ($terms) {
            foreach ($terms as $term) {
                $product_cat_id = $term->term_id;
                $cat_url = get_term_link((int)$product_cat_id, 'product_cat');
                if ($cat_url && !in_array($cat_url, $files))
                    $files[] = $cat_url;
            }
        }

        $product_url = get_permalink($prod_id);
        if ($product_url && !in_array($product_url, $files))
            $files[] = $product_url;

        return $files;
    }

    /**
     * Get all product and category URLs
     *
     * @return array
     */
    public function getAllProductAndCategoryURLs ()
    {
        // Array to hold all product and category URLs
        $files = array ();

        // Get all published products.
        $args = array (
            "status" => "publish",
            "limit" => -1
        );
        $products = wc_get_products($args);

        // Add product and category permalinks to $files array, skipping dupes.
        foreach ($products as $product) {
            /**
             * @var $product WC_Product
             */
            $prod_id = $product->get_id();
            $terms = get_the_terms($prod_id, 'product_cat');
            if ($terms) {
                foreach ($terms as $term) {
                    $product_cat_id = $term->term_id;
                    $cat_url = get_term_link((int)$product_cat_id, 'product_cat');
                    if ($cat_url && !in_array($cat_url, $files))
                        $files[] = $cat_url;
                }
            }

            $product_url = get_permalink($prod_id);
            if ($product_url && !in_array($product_url, $files))
                $files[] = $product_url;
        }

        return $files;
    }

    /**
     * Get all URLs for a site, including product and category URLs.
     *
     * @return array
     */
    public function getAllURLs ()
    {
        $urls = $this->getAllProductAndCategoryURLs();

        $posts = new WP_Query('post_type=any&posts_per_page=-1&post_status=publish');
        $posts = $posts->posts;

        foreach ($posts as $post) {
            $permalink = null;
            switch ($post->post_type) {
                case 'revision':
                case 'nav_menu_item':
                case 'attachment':
                case 'product':
                    break;
                case 'page':
                    $permalink = get_page_link($post->ID);
                    break;
                case 'post':
                    $permalink = get_permalink($post->ID);
                    break;
                default:
                    $permalink = get_post_permalink($post->ID);
                    break;
            }
            if ($permalink && !in_array($permalink, $urls)) {
                $urls[] = $permalink;
            }
        }

        return $urls;
    }

	/**
	 * Load/refresh log via AJAX when log tab is opened.
	 *
	 */
    public function ajaxLoadLog ()
    {
    	$response = array('log' => array());
	    $woocf_log = get_site_option('woocf_log')?:array();
	    if( $woocf_log ){
		    foreach( $woocf_log as $entry ){
		    	ob_start();
			    print_r(PHP_EOL);
		    	print_r($entry);
		    	$response['log'][] = ob_get_clean();
		    }
	    }

	    wp_die(json_encode($response));
    }

	/**
	 * Clear log via AJAX.
	 *
	 */
    public function ajaxClearLog ()
    {
        $response = array ();
        if (!isset($_POST['clear_log']) || !$_POST['clear_log']) {
            $response['error'] = 'An error occurred.';
            wp_die(json_encode($response));
        }

        if (!update_site_option('woocf_log', array ())) {
            $response['error'] = array ('error' => 'Failed to clear log.');
            wp_die(json_encode($response));
        }

        $response['result'] = 'success';
        wp_die(json_encode($response));
    }

    /**
     * Function to log events in a site option.
     *
     * @param $message
     */
    public function log ($message)
    {
        if ($this->siteSettings->isLoggingEnabled()){
            $log = get_site_option('woocf_log') ?: array();
            $log[] = $message;
            update_site_option('woocf_log', $log);
        }
    }

    /**
     * Helper function to get plugin settings.
     *
     * @param $setting
     * @return string|array|null
     */
    public function getSetting ($setting)
    {
        return $this->settings[$setting];
    }
}