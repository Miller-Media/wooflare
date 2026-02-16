<?php
/**
 * Uninstall handler for Wooflare.
 *
 * @package Wooflare
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$woocf_settings = get_option( 'woocf_settings' );
$delete_data = is_array( $woocf_settings ) && ! empty( $woocf_settings['delete_data_on_uninstall'] );

if ( ! $delete_data ) {
	return;
}

// Delete plugin options.
delete_option( 'woocf_settings' );
delete_option( 'woocf_activated_on' );
delete_option( 'woocf_notice_toggled' );

// Clean up user meta for review notice dismissals.
global $wpdb;
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'wooflare_review_dismissed' ) );
