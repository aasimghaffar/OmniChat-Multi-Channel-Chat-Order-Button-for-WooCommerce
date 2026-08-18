<?php
/**
 * WooCommerce Order WhatsApp Notifications Hub.
 * Queries and displays real WooCommerce orders from the database.
 *
 * @package    OmniChat_Order_Chat
 * @subpackage OmniChat_Order_Chat/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$omnichat_is_wc_active = class_exists( 'WooCommerce' );
$omnichat_orders       = array();

if ( $omnichat_is_wc_active ) {
	$omnichat_orders = wc_get_orders(
		array(
			'limit'   => 20,
			'orderby' => 'date',
			'order'   => 'DESC',
		)
	);
}

// Template resolved once, outside the loop ($options is provided by the admin class).
$omnichat_notify_template = $options['order_notify_template'] ?? "Hello {customer_name}! Your order #{order_id} on {site_title} is now {order_status}.\nTotal: {order_total}\nThank you for shopping with us!";
?>
<div class="wrap omnichat-admin-wrap">
	<div class="omnichat-admin-header">
		<div class="omnichat-brand-title">
			<div class="omnichat-logo-icon"><span class="dashicons dashicons-archive"></span></div>
			<div>
				<h1><?php esc_html_e( 'WooCommerce Order Alerts Hub', 'omnichat-order-chat' ); ?></h1>
				<p class="omnichat-version-tag"><?php esc_html_e( 'Send 1-click WhatsApp order confirmation and shipping updates to customers • By Cubixsol', 'omnichat-order-chat' ); ?></p>
			</div>
		</div>
		<div class="omnichat-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnichat-settings' ) ); ?>" class="button button-secondary"><?php esc_html_e( '← Settings & Widget', 'omnichat-order-chat' ); ?></a>
		</div>
	</div>

	<div class="omnichat-card">
		<div class="omnichat-card-head">
			<h3><?php esc_html_e( 'Live WooCommerce Orders & WhatsApp Notification Actions', 'omnichat-order-chat' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Notify customers about their order status and delivery updates directly on WhatsApp.', 'omnichat-order-chat' ); ?></p>
		</div>

		<?php if ( ! $omnichat_is_wc_active ) : ?>
			<div class="omnichat-empty-state">
				<span class="dashicons dashicons-warning" style="font-size:36px;color:#f59e0b;height:36px;width:36px;margin-bottom:10px;"></span>
				<h4><?php esc_html_e( 'WooCommerce is not installed or active', 'omnichat-order-chat' ); ?></h4>
				<p class="description"><?php esc_html_e( 'Please install and activate the WooCommerce plugin to track and notify store customers via WhatsApp.', 'omnichat-order-chat' ); ?></p>
			</div>
		<?php elseif ( empty( $omnichat_orders ) ) : ?>
			<div class="omnichat-empty-state">
				<span class="dashicons dashicons-cart" style="font-size:36px;color:#94a3b8;height:36px;width:36px;margin-bottom:10px;"></span>
				<h4><?php esc_html_e( 'No WooCommerce orders found yet', 'omnichat-order-chat' ); ?></h4>
				<p class="description"><?php esc_html_e( 'When customers place orders in your store, they will automatically appear here with 1-click WhatsApp messaging actions.', 'omnichat-order-chat' ); ?></p>
			</div>
		<?php else : ?>
			<div class="omnichat-table-container">
				<table class="wp-list-table widefat striped omnichat-recovery-table">
					<thead>
						<tr>
							<th style="width:12%;"><?php esc_html_e( 'Order ID', 'omnichat-order-chat' ); ?></th>
							<th style="width:20%;"><?php esc_html_e( 'Customer Name', 'omnichat-order-chat' ); ?></th>
							<th style="width:18%;"><?php esc_html_e( 'WhatsApp Phone', 'omnichat-order-chat' ); ?></th>
							<th style="width:14%;"><?php esc_html_e( 'Order Total', 'omnichat-order-chat' ); ?></th>
							<th style="width:14%;"><?php esc_html_e( 'Order Status', 'omnichat-order-chat' ); ?></th>
							<th style="width:22%; text-align:right;"><?php esc_html_e( '1-Click Action', 'omnichat-order-chat' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $omnichat_orders as $omnichat_order ) :
							$omnichat_order_id      = $omnichat_order->get_id();
							$omnichat_customer_name = $omnichat_order->get_formatted_billing_full_name();
							if ( '' === trim( (string) $omnichat_customer_name ) ) {
								$omnichat_customer_name = __( 'Guest Customer', 'omnichat-order-chat' );
							}
							$omnichat_phone       = $omnichat_order->get_billing_phone();
							$omnichat_clean_phone = preg_replace( '/[^0-9]/', '', (string) $omnichat_phone );
							$omnichat_total_plain = html_entity_decode( wp_strip_all_tags( $omnichat_order->get_formatted_order_total() ) );
							$omnichat_status_name = wc_get_order_status_name( $omnichat_order->get_status() );
							$omnichat_status_slug = $omnichat_order->get_status();

							$omnichat_msg = str_replace(
								array( '{customer_name}', '{order_id}', '{site_title}', '{order_status}', '{order_total}' ),
								array(
									$omnichat_order->get_billing_first_name() ? $omnichat_order->get_billing_first_name() : $omnichat_customer_name,
									$omnichat_order_id,
									get_bloginfo( 'name' ),
									$omnichat_status_name,
									$omnichat_total_plain,
								),
								$omnichat_notify_template
							);

							$omnichat_wa_url = ! empty( $omnichat_clean_phone ) ? 'https://wa.me/' . $omnichat_clean_phone . '?text=' . rawurlencode( $omnichat_msg ) : '#';
							?>
						<tr>
							<td><strong>#<?php echo esc_html( $omnichat_order_id ); ?></strong></td>
							<td><strong><?php echo esc_html( $omnichat_customer_name ); ?></strong></td>
							<td>
								<?php if ( ! empty( $omnichat_phone ) ) : ?>
									<span class="omnichat-phone-pill"><?php echo esc_html( $omnichat_phone ); ?></span>
								<?php else : ?>
									<span class="text-muted"><?php esc_html_e( 'No phone provided', 'omnichat-order-chat' ); ?></span>
								<?php endif; ?>
							</td>
							<td><strong><?php echo esc_html( $omnichat_total_plain ); ?></strong></td>
							<td>
								<span class="omnichat-badge-status omnichat-status-<?php echo esc_attr( $omnichat_status_slug ); ?>">
									<?php echo esc_html( $omnichat_status_name ); ?>
								</span>
							</td>
							<td style="text-align:right;">
								<?php if ( ! empty( $omnichat_clean_phone ) ) : ?>
									<a href="<?php echo esc_url( $omnichat_wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary omnichat-wa-send-btn">
										<?php esc_html_e( 'Send WhatsApp Update', 'omnichat-order-chat' ); ?>
									</a>
								<?php else : ?>
									<button type="button" class="button button-secondary" disabled><?php esc_html_e( 'No Phone', 'omnichat-order-chat' ); ?></button>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>
