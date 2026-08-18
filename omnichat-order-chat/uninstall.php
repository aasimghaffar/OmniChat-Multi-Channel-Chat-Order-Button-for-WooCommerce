<?php
/**
 * Fired when the plugin is uninstalled (deleted).
 * Removes all plugin data: options and the abandoned carts table.
 *
 * @package OmniChat_Order_Chat
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'omnichat_settings' );
delete_option( 'omnichat_db_version' );

global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- One-time cleanup on uninstall; caching is not applicable to DROP TABLE.
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'omnichat_abandoned_carts' ) );
