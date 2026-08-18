<?php
/**
 * The plugin bootstrap file.
 *
 * @link              https://cubixsol.com/
 * @since             1.0.0
 * @package           OmniChat_Order_Chat
 *
 * @wordpress-plugin
 * Plugin Name:       OmniChat – Multi-Channel Chat & Order Button for WooCommerce
 * Plugin URI:        https://cubixsol.com/products/
 * Description:       Floating WhatsApp & multi-channel chat widget, multi-agent support, automated cart recovery, order alerts, and 1-click 'Order via WhatsApp' button for WooCommerce.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Cubixsol
 * Author URI:        https://cubixsol.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       omnichat-order-chat
 * Domain Path:       /languages
 *
 * WC requires at least: 5.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OMNICHAT_VERSION', '1.0.0' );
define( 'OMNICHAT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OMNICHAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OMNICHAT_BASENAME', plugin_basename( __FILE__ ) );
define( 'OMNICHAT_PLUGIN_FILE', __FILE__ );

/**
 * Check whether WooCommerce is active.
 *
 * @return bool
 */
function omnichat_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * The code that runs during plugin activation.
 * Blocks activation when WooCommerce is missing (fallback for WP < 6.5,
 * where the "Requires Plugins" header is not enforced).
 */
function omnichat_activate() {
	if ( ! omnichat_is_woocommerce_active() ) {
		deactivate_plugins( OMNICHAT_BASENAME );
		wp_die(
			esc_html__( 'OmniChat requires WooCommerce to be installed and active. Please install and activate WooCommerce first.', 'omnichat-order-chat' ),
			esc_html__( 'Plugin dependency check', 'omnichat-order-chat' ),
			array(
				'back_link' => true,
			)
		);
	}

	require_once OMNICHAT_PLUGIN_DIR . 'includes/class-omnichat-activator.php';
	OmniChat_Activator::activate();
}
register_activation_hook( __FILE__, 'omnichat_activate' );

/**
 * The code that runs during plugin deactivation.
 */
function omnichat_deactivate() {
	require_once OMNICHAT_PLUGIN_DIR . 'includes/class-omnichat-deactivator.php';
	OmniChat_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'omnichat_deactivate' );

/**
 * Admin notice shown when WooCommerce is deactivated while OmniChat is active.
 */
function omnichat_woocommerce_missing_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-error"><p><strong>%1$s</strong> %2$s</p></div>',
		esc_html__( 'OmniChat:', 'omnichat-order-chat' ),
		esc_html__( 'WooCommerce is required for this plugin to work. Please install and activate WooCommerce.', 'omnichat-order-chat' )
	);
}

/**
 * Admin notice shown right after OmniChat deactivates itself.
 */
function omnichat_self_deactivated_notice() {
	printf(
		'<div class="notice notice-error is-dismissible"><p><strong>%1$s</strong> %2$s</p></div>',
		esc_html__( 'OmniChat has been deactivated.', 'omnichat-order-chat' ),
		esc_html__( 'It requires WooCommerce to be installed and active. Activate WooCommerce, then reactivate OmniChat.', 'omnichat-order-chat' )
	);
}

/**
 * Self-deactivate when WooCommerce is missing or gets deactivated.
 * Runs on admin_init so plugin.php functions are available.
 */
function omnichat_maybe_self_deactivate() {
	if ( omnichat_is_woocommerce_active() ) {
		return;
	}
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	deactivate_plugins( OMNICHAT_BASENAME );
	add_action( 'admin_notices', 'omnichat_self_deactivated_notice' );

	// Suppress the default "Plugin activated." message, which would be misleading.
	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only UI state cleanup, no data processed.
		unset( $_GET['activate'] );
	}
}
add_action( 'admin_init', 'omnichat_maybe_self_deactivate' );

/**
 * Declare compatibility with WooCommerce HPOS (custom order tables).
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', OMNICHAT_PLUGIN_FILE, true );
		}
	}
);

/**
 * Begins execution of the plugin, only when WooCommerce is active.
 */
function omnichat_run() {
	if ( ! omnichat_is_woocommerce_active() ) {
		add_action( 'admin_notices', 'omnichat_woocommerce_missing_notice' );
		return;
	}

	require_once OMNICHAT_PLUGIN_DIR . 'includes/class-omnichat.php';

	$plugin = new OmniChat();
	$plugin->run();
}
add_action( 'plugins_loaded', 'omnichat_run', 20 );
