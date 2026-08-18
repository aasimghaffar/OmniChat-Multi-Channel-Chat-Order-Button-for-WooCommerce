<?php
/**
 * Fired during plugin deactivation.
 *
 * @package OmniChat_Order_Chat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OmniChat_Deactivator {

	/**
	 * Clean up transients or scheduled events. Data is preserved;
	 * full removal happens in uninstall.php.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'omnichat_daily_maintenance' );
		wp_clear_scheduled_hook( 'omnichat_recovery_cron' );
	}
}
