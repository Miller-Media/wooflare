<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

/**
 * Class WOOCF_SiteSettings
 *
 * Handles setting up the admin settings menus
 * and screens.
 *
 */
class WOOCF_SiteSettings
{
	public $siteBaseDomain;
	public $settings;

	/**
	 * WOOCF_SiteSettings constructor.
	 *
	 * Runs on instantiation.
	 *
	 */
	public function __construct()
	{
        $helperFunctions = new WOOCF_Helpers();
		$this->siteBaseDomain = $helperFunctions->parseBaseDomainFromURL(get_site_url())['domain'];

		$woocf_settings = get_option('woocf_settings');
		$this->settings = is_array($woocf_settings) ? $woocf_settings : array();

        /**
         * Overwrite API authentication with credentials from official Cloudflare plugin
         * if they exist and the plugin is active
         */
        if($this->isCloudflarePluginActive()) {
            $cf_plugin_email = get_option('cloudflare_api_email');
            $cf_plugin_key = get_option('cloudflare_api_key');

            // Only use if both are present
            if ($cf_plugin_key && $cf_plugin_email) {
                $this->settings['cf_email'] = $cf_plugin_email;
                $this->settings['cf_key'] = $cf_plugin_key;
            }
        }

	}

    /**
     * Checks to see if the official Cloudflare plugin is active.
     *
     * Method below is used in place of is_plugin_active because this can happen at any
     * time during site load whereas is_plugin_active only happens after admin_init
     *
     * @return bool
     */
    public function isCloudflarePluginActive(){
        $active_plugins_basenames = get_option( 'active_plugins' );
        foreach ( $active_plugins_basenames as $plugin_basename ) {
            if ( false !== strpos( $plugin_basename, '/cloudflare.php' ) ) {
                return true;
            }
        }

        return false;
	}

    /**
     * Checks to see if the official Cloudflare plugin is active and credentials are set.
     * @return array|bool
     */
    public function areCloudflarePluginCredentialsSet(){
	    // Only use credentials if Cloudflare plugin is active, even if the values are in the DB
        if(!$this->isCloudflarePluginActive())
            return false;

		if(!empty(get_option('cloudflare_api_key', null)) && !empty(get_option('cloudflare_api_email', null)))
			return array('cf_key'=>get_option('cloudflare_api_key', null),'cf_email'=>get_option('cloudflare_api_email', null));

		return false;
	}

    /**
     * Checks to see if Cloudflare credentials are set either through the official
     * Cloudflare plugin or through this plugin manually.
     *
     * @return array|bool
     */
    public function areCredentialsSet(){
        // First, get API email and key from CloudFlare plugin settings, if available.
        if($use_cloudflare_plugin_credentials = $this->areCloudflarePluginCredentialsSet()){
            $cf_key = $use_cloudflare_plugin_credentials['cf_key'];
            $cf_email = $use_cloudflare_plugin_credentials['cf_email'];
            $cf_source = 'cf_plugin';
        } else if($use_cloudflare_ip_plugin_credentials = (!empty($this->settings['cf_key']) && !empty($this->settings['cf_email']))){
            $cf_key = $this->settings['cf_key'];
            $cf_email = $this->settings['cf_email'];
            $cf_source = 'manual';
        } else {
            return false;
        }

        return array('cf key' => $cf_key, 'cf_email' => $cf_email, 'source' => $cf_source);
    }

    public function isLoggingEnabled(){
        return array_key_exists('enable_logging', $this->settings) && $this->settings['enable_logging']=='on';
    }

	/**
	 * Registers the network admin menu page.
	 *
	 */
	public function addSiteMenu()
	{
		add_menu_page(
			'Wooflare',
			'Wooflare',
			'manage_options',
			'woocf-menu',
			array($this, 'addSiteMenuCB'),
			plugin_dir_url(plugin_dir_path(__FILE__)).'assets/media/cf-facebook-card.png'
		);
	}

	/**
	 * Callback for add_menu_page to display the
	 * settings page.
	 *
	 */
	public function addSiteMenuCB()
	{
		$this->displaySiteSettingsMenu();
	}

	/**
	 * Include the settings menu template.
	 *
	 */
	public function displaySiteSettingsMenu()
	{
		include_once(plugin_dir_path(plugin_dir_path(__FILE__)).'views/SiteSettingsView.php');
	}

	/**
	 * Verify nonce on form submit, then update settings.
	 *
	 */
	public function verifyNonce() {
		if ( isset($_POST['submit']) ) {

			// Bail if nonce not set.
			if ( !isset( $_POST['woocf_settings_nonce'] ) )
				return false;

			// Verify nonce.
			if ( @!wp_verify_nonce($_POST['woocf_settings_nonce'], 'woocf_settings_nonce') )
				return false;

			return $this->updateSettings();
		}
		return false;
	}

	/**
	 * Update option 'woocf_settings' with $_POST data.
	 *
	 * @return bool
	 */
	public function updateSettings() {
		// Load current options if available
	    $settings = $this->settings;

		if (isset($_POST['woocf_settings_nonce'])) {

            // Cache Clearing settings
            $settings['after_scheduled_sale'] = isset($_POST['after_scheduled_sale']) ? sanitize_text_field($_POST['after_scheduled_sale']) : '';
            $settings['when_product_out_of_stock'] = isset($_POST['when_product_out_of_stock']) ? sanitize_text_field($_POST['when_product_out_of_stock']) : '';
            $settings['when_store_notice_updated'] = isset($_POST['when_store_notice_updated']) ? sanitize_text_field($_POST['when_store_notice_updated']) : '';
            $settings['enable_logging'] = isset($_POST['enable_logging']) ? sanitize_text_field($_POST['enable_logging']) : '';

			if( update_option('woocf_settings', $settings) )
				return true;

		}

		return false;

	}

}