<?php
/**
 * Cart Recovery Table & Category Analytics Partial View.
 * Queries real captured checkout sessions and WooCommerce database orders.
 *
 * @package    OmniChat_Order_Chat
 * @subpackage OmniChat_Order_Chat/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

/**
 * Print a money amount (wc_price returns safe HTML).
 *
 * @param float $amount Amount.
 */
$omnichat_meta_api_ready = false;
if ( class_exists( 'OmniChat_Meta_API' ) ) {
	$omnichat_meta_api_obj   = new OmniChat_Meta_API();
	$omnichat_meta_api_ready = $omnichat_meta_api_obj->is_configured();
}

if ( ! function_exists( 'omnichat_admin_price' ) ) {
	function omnichat_admin_price( $amount ) {
		if ( function_exists( 'wc_price' ) ) {
			echo wp_kses_post( wc_price( $amount ) );
		} else {
			echo esc_html( '$' . number_format( (float) $amount, 2 ) );
		}
	}
}

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom plugin table, live admin data.
$omnichat_captured_sessions = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}omnichat_abandoned_carts ORDER BY updated_at DESC LIMIT 100" );

$omnichat_is_wc_active = class_exists( 'WooCommerce' );

$omnichat_pending_count       = 0;
$omnichat_recovered_count     = 0;
$omnichat_total_abandoned_val = 0.00;
$omnichat_total_recovered_val = 0.00;
$omnichat_category_stats      = array();
$omnichat_table_rows          = array();

// Process captured sessions.
if ( ! empty( $omnichat_captured_sessions ) ) {
	foreach ( $omnichat_captured_sessions as $omnichat_row ) {
		$omnichat_contents  = json_decode( $omnichat_row->cart_contents, true );
		$omnichat_items_arr = array();
		$omnichat_cat_arr   = array();

		if ( is_array( $omnichat_contents ) ) {
			foreach ( $omnichat_contents as $omnichat_item ) {
				$omnichat_items_arr[] = ( $omnichat_item['name'] ?? __( 'Product', 'omnichat-order-chat' ) ) . ' (x' . ( $omnichat_item['quantity'] ?? 1 ) . ')';
				$omnichat_cat_name    = $omnichat_item['category'] ?? __( 'General', 'omnichat-order-chat' );
				$omnichat_cat_arr[]   = $omnichat_cat_name;

				if ( ! isset( $omnichat_category_stats[ $omnichat_cat_name ] ) ) {
					$omnichat_category_stats[ $omnichat_cat_name ] = array(
						'abandoned' => 0,
						'lost_val'  => 0,
						'recovered' => 0,
						'rec_val'   => 0,
					);
				}
				if ( 'recovered' === $omnichat_row->cart_status ) {
					$omnichat_category_stats[ $omnichat_cat_name ]['recovered']++;
					$omnichat_category_stats[ $omnichat_cat_name ]['rec_val'] += floatval( $omnichat_item['price'] ?? 0 );
				} else {
					$omnichat_category_stats[ $omnichat_cat_name ]['abandoned']++;
					$omnichat_category_stats[ $omnichat_cat_name ]['lost_val'] += floatval( $omnichat_item['price'] ?? 0 );
				}
			}
		}

		$omnichat_is_rec = ( 'recovered' === $omnichat_row->cart_status );
		if ( $omnichat_is_rec ) {
			$omnichat_recovered_count++;
			$omnichat_total_recovered_val += floatval( $omnichat_row->cart_total );
		} else {
			$omnichat_pending_count++;
			$omnichat_total_abandoned_val += floatval( $omnichat_row->cart_total );
		}

		$omnichat_table_rows[] = array(
			'id'       => (int) $omnichat_row->id,
			'name'     => ! empty( $omnichat_row->customer_name ) ? $omnichat_row->customer_name : __( 'Guest Customer', 'omnichat-order-chat' ),
			'email'    => $omnichat_row->customer_email,
			'phone'    => $omnichat_row->customer_phone,
			'items'    => ! empty( $omnichat_items_arr ) ? implode( ', ', $omnichat_items_arr ) : __( 'Cart Items', 'omnichat-order-chat' ),
			'category' => ! empty( $omnichat_cat_arr ) ? implode( ' • ', array_unique( $omnichat_cat_arr ) ) : __( 'General', 'omnichat-order-chat' ),
			'total'    => floatval( $omnichat_row->cart_total ),
			'status'   => $omnichat_row->cart_status,
			'time_ago' => human_time_diff( strtotime( $omnichat_row->updated_at ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'omnichat-order-chat' ),
			'api_sent' => ! empty( $omnichat_row->recovery_sent_at ),
		);
	}
}

// Fallback: WooCommerce pending/failed orders when the capture table is fresh.
if ( $omnichat_is_wc_active && empty( $omnichat_table_rows ) ) {
	$omnichat_wc_pending = wc_get_orders(
		array(
			'status' => array( 'pending', 'failed', 'on-hold' ),
			'limit'  => 20,
		)
	);

	foreach ( $omnichat_wc_pending as $omnichat_o ) {
		$omnichat_pending_count++;
		$omnichat_total_abandoned_val += floatval( $omnichat_o->get_total() );
		$omnichat_table_rows[]         = array(
			'id'       => 0, // Not a captured-cart row; mark-recovered not applicable.
			'name'     => $omnichat_o->get_formatted_billing_full_name() ? $omnichat_o->get_formatted_billing_full_name() : __( 'Guest Customer', 'omnichat-order-chat' ),
			'email'    => $omnichat_o->get_billing_email(),
			'phone'    => $omnichat_o->get_billing_phone(),
			'items'    => sprintf( /* translators: %d: order ID. */ __( 'Order #%d Items', 'omnichat-order-chat' ), $omnichat_o->get_id() ),
			'category' => __( 'Store Products', 'omnichat-order-chat' ),
			'total'    => floatval( $omnichat_o->get_total() ),
			'status'   => 'abandoned',
			'time_ago' => human_time_diff( strtotime( $omnichat_o->get_date_created() ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'omnichat-order-chat' ),
			'api_sent' => false,
		);
	}
}

$omnichat_total_carts = $omnichat_pending_count + $omnichat_recovered_count;
$omnichat_win_rate    = $omnichat_total_carts > 0 ? round( ( $omnichat_recovered_count / $omnichat_total_carts ) * 100, 1 ) : 0;

// Message template resolved once, outside the row loop.
$omnichat_recovery_template = $options['abandoned_cart_msg'] ?? "Hi {customer_name}! We noticed you left items in your cart on {site_title}. Use coupon code 'SAVE10' for 10% off your order today! Click here to resume: {cart_url}";
?>
<div class="wrap omnichat-admin-wrap">
	<!-- Header -->
	<div class="omnichat-admin-header">
		<div class="omnichat-brand-title">
			<div class="omnichat-logo-icon"><span class="dashicons dashicons-cart"></span></div>
			<div>
				<h1><?php esc_html_e( 'Abandoned Cart Recovery & Analytics', 'omnichat-order-chat' ); ?></h1>
				<p class="omnichat-version-tag"><?php esc_html_e( 'Real-time captured checkout sessions • 1-click WhatsApp customer recovery • By Cubixsol', 'omnichat-order-chat' ); ?></p>
			</div>
		</div>
		<div class="omnichat-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnichat-settings' ) ); ?>" class="button button-secondary"><?php esc_html_e( '← Settings & Widget', 'omnichat-order-chat' ); ?></a>
			<?php if ( ! empty( $omnichat_table_rows ) ) : ?>
				<button type="button" id="omnichat-export-csv-btn" class="button button-primary omnichat-btn-green">
					<?php esc_html_e( 'Export Leads (CSV)', 'omnichat-order-chat' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<!-- Metric Cards -->
	<div class="omnichat-stats-grid">
		<div class="omnichat-stat-card">
			<span class="omnichat-stat-label"><?php esc_html_e( 'Pending Recovery Carts', 'omnichat-order-chat' ); ?></span>
			<div class="omnichat-stat-value"><?php echo esc_html( $omnichat_pending_count ); ?> <span class="omnichat-stat-sub">(<?php omnichat_admin_price( $omnichat_total_abandoned_val ); ?>)</span></div>
		</div>
		<div class="omnichat-stat-card omnichat-stat-success">
			<span class="omnichat-stat-label"><?php esc_html_e( 'Recovered Orders', 'omnichat-order-chat' ); ?></span>
			<div class="omnichat-stat-value"><?php echo esc_html( $omnichat_recovered_count ); ?> <span class="omnichat-stat-sub">(+<?php omnichat_admin_price( $omnichat_total_recovered_val ); ?>)</span></div>
		</div>
		<div class="omnichat-stat-card omnichat-stat-highlight">
			<span class="omnichat-stat-label"><?php esc_html_e( 'Recovery Win Rate', 'omnichat-order-chat' ); ?></span>
			<div class="omnichat-stat-value"><?php echo esc_html( $omnichat_win_rate ); ?>% <span class="omnichat-stat-sub"><?php esc_html_e( 'Success Rate', 'omnichat-order-chat' ); ?></span></div>
		</div>
		<div class="omnichat-stat-card omnichat-stat-warning">
			<span class="omnichat-stat-label"><?php esc_html_e( 'Total Captured Leads', 'omnichat-order-chat' ); ?></span>
			<div class="omnichat-stat-value"><?php echo esc_html( $omnichat_total_carts ); ?> <span class="omnichat-stat-sub"><?php esc_html_e( 'Sessions in DB', 'omnichat-order-chat' ); ?></span></div>
		</div>
	</div>

	<!-- Category Breakdown -->
	<?php if ( ! empty( $omnichat_category_stats ) ) : ?>
	<div class="omnichat-card">
		<div class="omnichat-card-head">
			<h3><?php esc_html_e( 'Live Category-Wise Performance Analysis', 'omnichat-order-chat' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Category metrics computed dynamically from your store checkout cart items.', 'omnichat-order-chat' ); ?></p>
		</div>

		<div class="omnichat-table-container">
			<table class="wp-list-table widefat striped omnichat-cat-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Product Category', 'omnichat-order-chat' ); ?></th>
						<th><?php esc_html_e( 'Pending Carts', 'omnichat-order-chat' ); ?></th>
						<th><?php esc_html_e( 'Pending Revenue', 'omnichat-order-chat' ); ?></th>
						<th><?php esc_html_e( 'Recovered Orders', 'omnichat-order-chat' ); ?></th>
						<th><?php esc_html_e( 'Recovered Revenue', 'omnichat-order-chat' ); ?></th>
						<th><?php esc_html_e( 'Success Rate', 'omnichat-order-chat' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $omnichat_category_stats as $cat => $omnichat_data ) :
						$omnichat_c_total = $omnichat_data['abandoned'] + $omnichat_data['recovered'];
						$omnichat_c_rate  = $omnichat_c_total > 0 ? round( ( $omnichat_data['recovered'] / $omnichat_c_total ) * 100, 1 ) : 0;
						?>
					<tr>
						<td><strong><?php echo esc_html( $cat ); ?></strong></td>
						<td><?php echo esc_html( $omnichat_data['abandoned'] ); ?> <?php esc_html_e( 'carts', 'omnichat-order-chat' ); ?></td>
						<td><span class="text-danger"><?php omnichat_admin_price( $omnichat_data['lost_val'] ); ?></span></td>
						<td><span class="text-success"><?php echo esc_html( $omnichat_data['recovered'] ); ?> <?php esc_html_e( 'orders', 'omnichat-order-chat' ); ?></span></td>
						<td><span class="text-success">+<?php omnichat_admin_price( $omnichat_data['rec_val'] ); ?></span></td>
						<td>
							<div class="omnichat-progress-bar"><div class="omnichat-progress-fill" style="width:<?php echo esc_attr( $omnichat_c_rate ); ?>%;"></div></div>
							<strong><?php echo esc_html( $omnichat_c_rate ); ?>%</strong>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>

	<!-- Live Abandoned Carts Log -->
	<div class="omnichat-card">
		<div class="omnichat-card-head">
			<h3><?php esc_html_e( 'Live Abandoned Carts & Recovery Queue', 'omnichat-order-chat' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Captured in real-time when visitors enter their details at checkout. Send direct WhatsApp recovery messages in 1 click.', 'omnichat-order-chat' ); ?></p>
		</div>

		<?php if ( empty( $omnichat_table_rows ) ) : ?>
			<div class="omnichat-empty-state">
				<span class="dashicons dashicons-cart" style="font-size:36px;color:#94a3b8;height:36px;width:36px;margin-bottom:10px;"></span>
				<h4><?php esc_html_e( 'No abandoned carts captured yet', 'omnichat-order-chat' ); ?></h4>
				<p class="description"><?php esc_html_e( 'When a visitor enters their phone number or email on the WooCommerce checkout page and leaves without completing the purchase, their abandoned cart will appear here instantly.', 'omnichat-order-chat' ); ?></p>
			</div>
		<?php else : ?>
			<!-- Filter Bar -->
			<div class="omnichat-filter-toolbar">
				<div class="omnichat-filter-group">
					<button type="button" class="omnichat-filter-pill active" data-filter="all">
						<?php
						/* translators: %d: total number of carts. */
						printf( esc_html__( 'All Carts (%d)', 'omnichat-order-chat' ), (int) count( $omnichat_table_rows ) );
						?>
					</button>
					<button type="button" class="omnichat-filter-pill" data-filter="abandoned">
						<?php
						/* translators: %d: number of pending carts. */
						printf( esc_html__( 'Pending Recovery (%d)', 'omnichat-order-chat' ), (int) $omnichat_pending_count );
						?>
					</button>
					<button type="button" class="omnichat-filter-pill" data-filter="recovered">
						<?php
						/* translators: %d: number of recovered carts. */
						printf( esc_html__( 'Recovered (%d)', 'omnichat-order-chat' ), (int) $omnichat_recovered_count );
						?>
					</button>
				</div>

				<div class="omnichat-search-wrapper">
					<input type="text" id="omnichat-table-search" placeholder="<?php esc_attr_e( 'Search customer, phone, product...', 'omnichat-order-chat' ); ?>" class="regular-text">
				</div>
			</div>

			<div class="omnichat-table-container">
				<table class="wp-list-table widefat striped omnichat-recovery-table" id="omnichat-leads-table">
					<thead>
						<tr>
							<th style="width:20%;"><?php esc_html_e( 'Customer Name & Email', 'omnichat-order-chat' ); ?></th>
							<th style="width:14%;"><?php esc_html_e( 'WhatsApp Phone', 'omnichat-order-chat' ); ?></th>
							<th style="width:24%;"><?php esc_html_e( 'Cart Items & Category', 'omnichat-order-chat' ); ?></th>
							<th style="width:10%;"><?php esc_html_e( 'Cart Total', 'omnichat-order-chat' ); ?></th>
							<th style="width:12%;"><?php esc_html_e( 'Status', 'omnichat-order-chat' ); ?></th>
							<th style="width:20%; text-align:right;"><?php esc_html_e( 'WhatsApp Action', 'omnichat-order-chat' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $omnichat_table_rows as $omnichat_row ) :
							$omnichat_is_recovered  = ( 'recovered' === $omnichat_row['status'] );
							$omnichat_status_filter = $omnichat_is_recovered ? 'recovered' : 'abandoned';
							$omnichat_clean_phone   = preg_replace( '/[^0-9]/', '', (string) $omnichat_row['phone'] );

							$omnichat_msg = str_replace(
								array( '{customer_name}', '{site_title}', '{cart_url}' ),
								array(
									$omnichat_row['name'],
									get_bloginfo( 'name' ),
									add_query_arg( 'coupon', 'SAVE10', function_exists( 'wc_get_cart_url' ) && wc_get_cart_url() ? wc_get_cart_url() : home_url( '/cart/' ) ),
								),
								$omnichat_recovery_template
							);

							$omnichat_wa_url = ! empty( $omnichat_clean_phone ) ? 'https://wa.me/' . $omnichat_clean_phone . '?text=' . rawurlencode( $omnichat_msg ) : '#';
							?>
						<tr class="cart-item-row" data-status="<?php echo esc_attr( $omnichat_status_filter ); ?>" data-cart-id="<?php echo esc_attr( $omnichat_row['id'] ); ?>">
							<td class="col-customer">
								<strong class="lead-name"><?php echo esc_html( $omnichat_row['name'] ); ?></strong><br>
								<span class="lead-email text-muted"><?php echo esc_html( $omnichat_row['email'] ? $omnichat_row['email'] : __( 'No email', 'omnichat-order-chat' ) ); ?></span><br>
								<small class="text-muted"><?php echo esc_html( $omnichat_row['time_ago'] ); ?></small>
							</td>
							<td class="col-phone">
								<?php if ( ! empty( $omnichat_row['phone'] ) ) : ?>
									<span class="omnichat-phone-pill lead-phone"><?php echo esc_html( $omnichat_row['phone'] ); ?></span>
								<?php else : ?>
									<span class="text-muted"><?php esc_html_e( 'No phone', 'omnichat-order-chat' ); ?></span>
								<?php endif; ?>
							</td>
							<td class="col-items">
								<strong class="lead-item"><?php echo esc_html( wp_trim_words( $omnichat_row['items'], 8 ) ); ?></strong><br>
								<span class="omnichat-cat-tag lead-category"><?php echo esc_html( $omnichat_row['category'] ); ?></span>
							</td>
							<td class="col-total">
								<strong class="lead-total"><?php omnichat_admin_price( $omnichat_row['total'] ); ?></strong>
							</td>
							<td class="col-status">
								<?php if ( $omnichat_is_recovered ) : ?>
									<span class="omnichat-badge-recovered lead-status"><?php esc_html_e( 'Recovered ✓', 'omnichat-order-chat' ); ?></span>
								<?php else : ?>
									<span class="omnichat-badge-abandoned lead-status"><?php esc_html_e( 'Abandoned', 'omnichat-order-chat' ); ?></span>
								<?php endif; ?>
							</td>
							<td style="text-align:right;">
								<?php if ( $omnichat_is_recovered ) : ?>
									<span class="omnichat-recovered-check">✓ <?php esc_html_e( 'Paid Order', 'omnichat-order-chat' ); ?></span>
								<?php elseif ( ! empty( $omnichat_clean_phone ) ) : ?>
									<a href="<?php echo esc_url( $omnichat_wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary omnichat-wa-send-btn">
										<?php esc_html_e( '1-Click WhatsApp', 'omnichat-order-chat' ); ?>
									</a>
									<?php if ( ! empty( $omnichat_row['id'] ) && $omnichat_meta_api_ready ) : ?>
										<?php if ( ! empty( $omnichat_row['api_sent'] ) ) : ?>
											<span class="omnichat-api-sent-tag" title="<?php esc_attr_e( 'Recovery message already sent via Meta API', 'omnichat-order-chat' ); ?>"><?php esc_html_e( 'API ✓', 'omnichat-order-chat' ); ?></span>
										<?php else : ?>
											<button type="button" class="button button-secondary omnichat-api-send-btn" data-cart-id="<?php echo esc_attr( $omnichat_row['id'] ); ?>" title="<?php esc_attr_e( 'Send recovery message now via Meta Cloud API', 'omnichat-order-chat' ); ?>"><?php esc_html_e( 'Send via API', 'omnichat-order-chat' ); ?></button>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ( ! empty( $omnichat_row['id'] ) ) : ?>
										<button type="button" class="button button-secondary omnichat-mark-recovered-btn" data-cart-id="<?php echo esc_attr( $omnichat_row['id'] ); ?>" title="<?php esc_attr_e( 'Mark this cart as recovered', 'omnichat-order-chat' ); ?>">✓</button>
									<?php endif; ?>
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
