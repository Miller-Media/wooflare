<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

/**
 * WOOCF_CloudflareAPIController Class
 */
class WOOCF_CloudflareAPIController
{

	public $settings;

	/**
	 * The base URL for this API version.
	 *
	 * @var string
	 */
	public $url = "https://api.cloudflare.com/client/v4/";
	
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
        $this->siteSettings = new WOOCF_SiteSettings();
        $this->settings = $this->siteSettings->settings;
        $this->helperFunctions = new WOOCF_Helpers();
	}

	private function validate(){
	    if(!$this->siteSettings->areCredentialsSet()) {
            if ($this->siteSettings->isLoggingEnabled()) {
                $this->log("No credentials present. Cancelling API call.");
            }

            return false;
        }

	    return true;
    }

	/**
	 * Master function to perform a request on the Cloudflare API.
	 *
	 * @param $method | e.g. GET, POST, DELETE
	 * @param $url
	 * @param $body | null or a JSON-encoded array of values
	 * @return array|mixed|object
	 */
	public function request( $method, $url, $body=null )
    {
        if(!$this->validate())
            return false;

        // Log request
        if ($this->siteSettings->isLoggingEnabled()){
            $this->log("=== REQUEST ===");
            $this->log("Timestamp: " . current_time('mysql'));
            $this->log("Method: " . $method);
            $this->log("URL: " . $url);
            if ($body) {
                $this->log(json_decode($body));
            }
        }

		// Get credentials from plugin settings.
		$key = $this->getSetting('cf_key');
		$email = $this->getSetting('cf_email');

		if( !$key || !$email )
			return false;

		// Set headers.
		$headers = array(
			'X-Auth-Email' => $email,
			'X-Auth-Key' => $key,
			'Content-type' => 'application/json'
		);

		// Set args.
		$args = array(
			'method' => $method,
			'headers' => $headers
		);

		// Add body content to args array, if exists.
		if( $body )
			$args['body'] = $body;

		// Handle request method types.
		switch( $method ){
			case 'GET':
				$result = wp_remote_get($url, $args);
				break;
			default:
				$result = wp_remote_post($url, $args);
				break;
		}

		$result = json_decode($result['body']);

		// Handle errors
		if ($this->siteSettings->isLoggingEnabled()) {
            if (!$result || (!property_exists($result, 'success') && (property_exists($result, 'errors') || property_exists($result, 'error')))) {
                $this->log("=== RESPONSE (Error) ===");
            } else {
                $this->log("=== RESPONSE (Success) ===");
            }
            $this->log($result);
        }

		return $result;
	}

	/**
	 * Blacklist IP in CloudFlare via API call.
	 *
	 * @see      https://api.cloudflare.com/#firewall-access-rule-for-a-zone-create-access-rule
	 *
	 * @param $zone_id, $ip
	 * @return array|mixed|object
	 */
	public function blacklistIP( $zone_id, $ip )
	{
        $url = $this->url . "zones/$zone_id/firewall/access_rules/rules";
        $body = json_encode(array(
            'mode' => 'block',
            'configuration' => array(
                'target' => 'ip',
                'value' => $ip
            ),
            'notes' => 'Blocked for attempting to login using a banned username.'
        ));

        $result = $this->request("POST", $url, $body);
        return $result;
	}

	/**
	 * Utility function to delete a blacklisted IP rule in Cloudflare.
	 * (Perhaps a text input and button in settings page to accomplish this?)
	 *
	 * @see      https://api.cloudflare.com/#firewall-access-rule-for-a-zone-delete-access-rule
	 *
	 * @param $zone_id
	 * @param $rule_id
	 * @return array|mixed|object
	 */
	public function deleteBlacklistedIP( $zone_id, $rule_id )
	{
		$url = $this->url . "zones/$zone_id/firewall/access_rules/rules/$rule_id";
		$result = $this->request("DELETE", $url);
		return $result;
	}

	/**
	 * Get all Cloudflare zones.
	 *
	 * @return array|mixed|object
	 */
	public function getZones()
	{
		$url = $this->url . "zones";
		$result = $this->request("GET", $url);
		return $result;
	}

    /**
     * Get zone ID for current site.
     *
     * @return mixed
     */
    public function getZoneId($domain)
    {
        $domain = $this->helperFunctions->parseBaseDomainFromURL($domain)['domain'];

        // Check for saved zone ID and return, if exists. Saves an API call.
        $zone_id = null;

        if( !empty( $this->settings['zone_id'] ) )
            $zone_id = $this->settings['zone_id'];

        if( $zone_id )
            return $zone_id;

        // Only log if logging is enabled
        if ($this->siteSettings->isLoggingEnabled()) {
            $this->log('Zone ID not set in database. Retrieving via API.');
        }

        // Build URL and make request.
        $url = $this->url . "zones?name=$domain";
        $result = $this->request("GET", $url);
        if( !$result || !empty($result->errors) || !$result->result ){
            return false;
        }

        // Get zone ID and update settings for future use.
        $zone_id = $result->result[0]->id;
        $settings = get_site_option('woocf_settings');
        $settings['zone_id'] = $zone_id;
        update_site_option('woocf_settings', $settings);

        return $zone_id;
    }

	/**
	 * Get access rules for a given zone_id.
	 *
	 * @param $zone_id
	 * @return array|mixed|object
	 */
	public function getZoneAccessRules($zone_id)
	{
		$url = $this->url . "zones/$zone_id/firewall/access_rules/rules?mode=block&scope_type=zone&configuration_target=ip";
		$result = $this->request("GET", $url);
		return json_decode($result['body']);
	}

	/**
	 * Function to log events in a site option.
	 *
	 * @param $message
	 */
	public function log( $message )
	{
        // Only log if logging is enabled
        if ($this->siteSettings->isLoggingEnabled()) {
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
	public function getSetting($setting)
	{
		return $this->settings[$setting];
	}
}
