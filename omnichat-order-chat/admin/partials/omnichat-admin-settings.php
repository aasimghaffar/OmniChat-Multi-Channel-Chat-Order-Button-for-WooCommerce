<?php
/**
 * Widget & Settings Admin Page Partial View.
 * Tabbed settings UI: Widget, Agents, WooCommerce Button, Messages & Recovery, API & Tracking.
 *
 * @package    OmniChat_Order_Chat
 * @subpackage OmniChat_Order_Chat/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$omnichat_agents = isset( $options['agents'] ) && is_array( $options['agents'] ) ? $options['agents'] : array();
?>
<div class="wrap omnichat-admin-wrap">

	<!-- Header -->
	<div class="omnichat-admin-header">
		<div class="omnichat-brand-title">
			<div class="omnichat-logo-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
			</div>
			<div>
				<h1><?php esc_html_e( 'OmniChat', 'omnichat-order-chat' ); ?></h1>
				<p class="omnichat-version-tag">
					<?php
					/* translators: %s: plugin version number. */
					printf( esc_html__( 'Version %s • By Cubixsol • ', 'omnichat-order-chat' ), esc_html( OMNICHAT_VERSION ) );
					?>
					<span class="omnichat-full-unlocked-badge"><?php esc_html_e( '100% Features Unlocked', 'omnichat-order-chat' ); ?></span>
				</p>
			</div>
		</div>
		<div class="omnichat-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnichat-recovery' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cart Recovery Log', 'omnichat-order-chat' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=omnichat-orders' ) ); ?>" class="button button-primary omnichat-btn-green"><?php esc_html_e( 'Order Alerts Hub', 'omnichat-order-chat' ); ?></a>
		</div>
	</div>

	<?php settings_errors(); ?>

	<!-- Tabs -->
	<div class="omnichat-tab-navigation">
		<a href="#" class="omnichat-tab-link active" data-tab="omnichat-tab-widget"><span class="dashicons dashicons-format-chat"></span> <?php esc_html_e( 'Floating Widget', 'omnichat-order-chat' ); ?></a>
		<a href="#" class="omnichat-tab-link" data-tab="omnichat-tab-agents"><span class="dashicons dashicons-groups"></span> <?php esc_html_e( 'Agents & Channels', 'omnichat-order-chat' ); ?></a>
		<a href="#" class="omnichat-tab-link" data-tab="omnichat-tab-woo"><span class="dashicons dashicons-cart"></span> <?php esc_html_e( 'WooCommerce Button', 'omnichat-order-chat' ); ?></a>
		<a href="#" class="omnichat-tab-link" data-tab="omnichat-tab-messages"><span class="dashicons dashicons-email"></span> <?php esc_html_e( 'Messages & Recovery', 'omnichat-order-chat' ); ?></a>
		<a href="#" class="omnichat-tab-link" data-tab="omnichat-tab-api"><span class="dashicons dashicons-chart-line"></span> <?php esc_html_e( 'API & Tracking', 'omnichat-order-chat' ); ?></a>
	</div>

	<form method="post" action="options.php">
		<?php settings_fields( 'omnichat_options_group' ); ?>

		<!-- TAB 1: Floating Widget -->
		<div id="omnichat-tab-widget" class="omnichat-tab-content active">
			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Floating Chat Widget', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Configure the floating chat button shown on your storefront.', 'omnichat-order-chat' ); ?></p>
				</div>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Floating Widget', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-switch">
								<input type="checkbox" name="omnichat_settings[enable_floating_widget]" value="yes" <?php checked( $options['enable_floating_widget'], 'yes' ); ?>>
								<span class="omnichat-slider"></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-default-phone"><?php esc_html_e( 'Default WhatsApp Number', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<input type="text" id="omnichat-default-phone" class="regular-text" name="omnichat_settings[default_phone]" value="<?php echo esc_attr( $options['default_phone'] ); ?>" placeholder="+15551234567">
							<p class="description"><?php esc_html_e( 'Include the country code, e.g. +447712345678. Used when no agent is selected.', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-widget-position"><?php esc_html_e( 'Widget Position', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<select id="omnichat-widget-position" name="omnichat_settings[widget_position]">
								<option value="bottom-right" <?php selected( $options['widget_position'], 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'omnichat-order-chat' ); ?></option>
								<option value="bottom-left" <?php selected( $options['widget_position'], 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'omnichat-order-chat' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-theme-color"><?php esc_html_e( 'Widget Theme Color', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="color" id="omnichat-theme-color" name="omnichat_settings[widget_theme_color]" value="<?php echo esc_attr( $options['widget_theme_color'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-cta-text"><?php esc_html_e( 'Button CTA Text', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-cta-text" class="regular-text" name="omnichat_settings[button_cta_text]" value="<?php echo esc_attr( $options['button_cta_text'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-greeting"><?php esc_html_e( 'Greeting Message', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-greeting" class="large-text" name="omnichat_settings[widget_greeting]" value="<?php echo esc_attr( $options['widget_greeting'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-header-title"><?php esc_html_e( 'Popup Header Title', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-header-title" class="regular-text" name="omnichat_settings[popup_header_title]" value="<?php echo esc_attr( $options['popup_header_title'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-header-subtitle"><?php esc_html_e( 'Popup Header Subtitle', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-header-subtitle" class="regular-text" name="omnichat_settings[popup_header_subtitle]" value="<?php echo esc_attr( $options['popup_header_subtitle'] ); ?>"></td>
					</tr>
				</table>
				<input type="hidden" name="omnichat_settings[button_icon]" value="<?php echo esc_attr( $options['button_icon'] ); ?>">
			</div>
		</div>

		<!-- TAB 2: Agents -->
		<div id="omnichat-tab-agents" class="omnichat-tab-content">
			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Support Agents & Channels', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Add multiple agents with individual channels (WhatsApp, Telegram, Phone) and working hours. Online/Away status is shown automatically based on working hours.', 'omnichat-order-chat' ); ?></p>
				</div>

				<div id="omnichat-agents-list">
					<?php
					if ( ! empty( $omnichat_agents ) ) :
						foreach ( $omnichat_agents as $omnichat_i => $omnichat_agent ) :
							?>
						<div class="omnichat-agent-box">
							<div class="omnichat-agent-fields-grid">
								<div>
									<label><?php esc_html_e( 'Agent Name', 'omnichat-order-chat' ); ?></label>
									<input type="text" class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][name]" value="<?php echo esc_attr( $omnichat_agent['name'] ?? '' ); ?>">
								</div>
								<div>
									<label><?php esc_html_e( 'Role', 'omnichat-order-chat' ); ?></label>
									<input type="text" class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][role]" value="<?php echo esc_attr( $omnichat_agent['role'] ?? '' ); ?>">
								</div>
								<div>
									<label><?php esc_html_e( 'Channel', 'omnichat-order-chat' ); ?></label>
									<select class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][channel]">
										<option value="whatsapp" <?php selected( $omnichat_agent['channel'] ?? 'whatsapp', 'whatsapp' ); ?>><?php esc_html_e( 'WhatsApp', 'omnichat-order-chat' ); ?></option>
										<option value="telegram" <?php selected( $omnichat_agent['channel'] ?? '', 'telegram' ); ?>><?php esc_html_e( 'Telegram', 'omnichat-order-chat' ); ?></option>
										<option value="phone" <?php selected( $omnichat_agent['channel'] ?? '', 'phone' ); ?>><?php esc_html_e( 'Phone Call', 'omnichat-order-chat' ); ?></option>
									</select>
								</div>
								<div>
									<label><?php esc_html_e( 'Phone / Username', 'omnichat-order-chat' ); ?></label>
									<input type="text" class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][phone]" value="<?php echo esc_attr( $omnichat_agent['phone'] ?? '' ); ?>">
								</div>
								<div>
									<label><?php esc_html_e( 'Hours Start', 'omnichat-order-chat' ); ?></label>
									<input type="time" class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][hours_start]" value="<?php echo esc_attr( $omnichat_agent['hours_start'] ?? '09:00' ); ?>">
								</div>
								<div>
									<label><?php esc_html_e( 'Hours End', 'omnichat-order-chat' ); ?></label>
									<input type="time" class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][hours_end]" value="<?php echo esc_attr( $omnichat_agent['hours_end'] ?? '18:00' ); ?>">
								</div>
								<div>
									<label><?php esc_html_e( 'Status', 'omnichat-order-chat' ); ?></label>
									<select class="widefat" name="omnichat_settings[agents][<?php echo esc_attr( $omnichat_i ); ?>][status]">
										<option value="online" <?php selected( $omnichat_agent['status'] ?? 'online', 'online' ); ?>><?php esc_html_e( 'Online', 'omnichat-order-chat' ); ?></option>
										<option value="offline" <?php selected( $omnichat_agent['status'] ?? '', 'offline' ); ?>><?php esc_html_e( 'Offline', 'omnichat-order-chat' ); ?></option>
									</select>
								</div>
								<div style="display:flex;align-items:flex-end;">
									<button type="button" class="button button-link-delete omnichat-remove-agent"><?php esc_html_e( 'Remove', 'omnichat-order-chat' ); ?></button>
								</div>
							</div>
						</div>
							<?php
						endforeach;
					endif;
					?>
				</div>

				<button type="button" id="omnichat-add-agent" class="button button-secondary"><?php esc_html_e( '+ Add Agent', 'omnichat-order-chat' ); ?></button>

				<!-- Blank row template used by admin JS. -->
				<script type="text/template" id="omnichat-agent-template">
					<div class="omnichat-agent-box">
						<div class="omnichat-agent-fields-grid">
							<div>
								<label><?php esc_html_e( 'Agent Name', 'omnichat-order-chat' ); ?></label>
								<input type="text" class="widefat" name="omnichat_settings[agents][__INDEX__][name]" value="">
							</div>
							<div>
								<label><?php esc_html_e( 'Role', 'omnichat-order-chat' ); ?></label>
								<input type="text" class="widefat" name="omnichat_settings[agents][__INDEX__][role]" value="">
							</div>
							<div>
								<label><?php esc_html_e( 'Channel', 'omnichat-order-chat' ); ?></label>
								<select class="widefat" name="omnichat_settings[agents][__INDEX__][channel]">
									<option value="whatsapp"><?php esc_html_e( 'WhatsApp', 'omnichat-order-chat' ); ?></option>
									<option value="telegram"><?php esc_html_e( 'Telegram', 'omnichat-order-chat' ); ?></option>
									<option value="phone"><?php esc_html_e( 'Phone Call', 'omnichat-order-chat' ); ?></option>
								</select>
							</div>
							<div>
								<label><?php esc_html_e( 'Phone / Username', 'omnichat-order-chat' ); ?></label>
								<input type="text" class="widefat" name="omnichat_settings[agents][__INDEX__][phone]" value="">
							</div>
							<div>
								<label><?php esc_html_e( 'Hours Start', 'omnichat-order-chat' ); ?></label>
								<input type="time" class="widefat" name="omnichat_settings[agents][__INDEX__][hours_start]" value="09:00">
							</div>
							<div>
								<label><?php esc_html_e( 'Hours End', 'omnichat-order-chat' ); ?></label>
								<input type="time" class="widefat" name="omnichat_settings[agents][__INDEX__][hours_end]" value="18:00">
							</div>
							<div>
								<label><?php esc_html_e( 'Status', 'omnichat-order-chat' ); ?></label>
								<select class="widefat" name="omnichat_settings[agents][__INDEX__][status]">
									<option value="online"><?php esc_html_e( 'Online', 'omnichat-order-chat' ); ?></option>
									<option value="offline"><?php esc_html_e( 'Offline', 'omnichat-order-chat' ); ?></option>
								</select>
							</div>
							<div style="display:flex;align-items:flex-end;">
								<button type="button" class="button button-link-delete omnichat-remove-agent"><?php esc_html_e( 'Remove', 'omnichat-order-chat' ); ?></button>
							</div>
						</div>
					</div>
				</script>
			</div>
		</div>

		<!-- TAB 3: WooCommerce Button -->
		<div id="omnichat-tab-woo" class="omnichat-tab-content">
			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( '1-Click "Order via WhatsApp" Button', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Shows a WhatsApp order button on single product pages with the product name, price, SKU and URL pre-filled.', 'omnichat-order-chat' ); ?></p>
				</div>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Product Button', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-switch">
								<input type="checkbox" name="omnichat_settings[enable_woo_order_btn]" value="yes" <?php checked( $options['enable_woo_order_btn'], 'yes' ); ?>>
								<span class="omnichat-slider"></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-woo-btn-text"><?php esc_html_e( 'Button Text', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-woo-btn-text" class="regular-text" name="omnichat_settings[woo_btn_text]" value="<?php echo esc_attr( $options['woo_btn_text'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Button Position', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-radio-label"><input type="radio" name="omnichat_settings[woo_btn_position]" value="after_add_to_cart" <?php checked( $options['woo_btn_position'], 'after_add_to_cart' ); ?>> <?php esc_html_e( 'After "Add to Cart" button', 'omnichat-order-chat' ); ?></label>
							<label class="omnichat-radio-label"><input type="radio" name="omnichat_settings[woo_btn_position]" value="before_add_to_cart" <?php checked( $options['woo_btn_position'], 'before_add_to_cart' ); ?>> <?php esc_html_e( 'Before "Add to Cart" button', 'omnichat-order-chat' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-woo-btn-color"><?php esc_html_e( 'Button Background Color', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="color" id="omnichat-woo-btn-color" name="omnichat_settings[woo_btn_bg_color]" value="<?php echo esc_attr( $options['woo_btn_bg_color'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-woo-msg"><?php esc_html_e( 'Order Message Template', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<textarea id="omnichat-woo-msg" class="large-text" rows="5" name="omnichat_settings[woo_message_template]"><?php echo esc_textarea( $options['woo_message_template'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Placeholders: {product_name}, {product_price}, {product_sku}, {product_url}', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<!-- TAB 4: Messages & Recovery -->
		<div id="omnichat-tab-messages" class="omnichat-tab-content">
			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Order Status Notification Template', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Used by the 1-click WhatsApp buttons on the WooCommerce Orders list and the Order Alerts Hub.', 'omnichat-order-chat' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="omnichat-order-template"><?php esc_html_e( 'Message Template', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<textarea id="omnichat-order-template" class="large-text" rows="4" name="omnichat_settings[order_notify_template]"><?php echo esc_textarea( $options['order_notify_template'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Placeholders: {customer_name}, {order_id}, {site_title}, {order_status}, {order_total}', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Abandoned Cart Recovery', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Captures checkout sessions in real time so you can recover abandoned carts via WhatsApp.', 'omnichat-order-chat' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Cart Capturing', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-switch">
								<input type="checkbox" name="omnichat_settings[enable_abandoned_cart]" value="yes" <?php checked( $options['enable_abandoned_cart'], 'yes' ); ?>>
								<span class="omnichat-slider"></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-recovery-msg"><?php esc_html_e( 'Recovery Message Template', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<textarea id="omnichat-recovery-msg" class="large-text" rows="4" name="omnichat_settings[abandoned_cart_msg]"><?php echo esc_textarea( $options['abandoned_cart_msg'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Placeholders: {customer_name}, {site_title}, {cart_url}', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<!-- TAB 5: API & Tracking -->
		<div id="omnichat-tab-api" class="omnichat-tab-content">
			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Sending Mode', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Direct mode opens WhatsApp chat links (wa.me) and requires no API. Meta Cloud API mode sends messages from your server through the official WhatsApp Business API — used for automated cart recovery and manual sends from the Recovery page.', 'omnichat-order-chat' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Mode', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-radio-label"><input type="radio" name="omnichat_settings[sending_mode]" value="direct" <?php checked( $options['sending_mode'], 'direct' ); ?>> <?php esc_html_e( 'Direct (wa.me links, no API required)', 'omnichat-order-chat' ); ?></label>
							<label class="omnichat-radio-label"><input type="radio" name="omnichat_settings[sending_mode]" value="meta_api" <?php checked( $options['sending_mode'], 'meta_api' ); ?>> <?php esc_html_e( 'Meta WhatsApp Cloud API', 'omnichat-order-chat' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-meta-token"><?php esc_html_e( 'Meta API Token', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="password" id="omnichat-meta-token" class="regular-text" name="omnichat_settings[meta_api_token]" value="<?php echo esc_attr( $options['meta_api_token'] ); ?>" autocomplete="off"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-meta-phone-id"><?php esc_html_e( 'Meta Phone Number ID', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-meta-phone-id" class="regular-text" name="omnichat_settings[meta_phone_number_id]" value="<?php echo esc_attr( $options['meta_phone_number_id'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-meta-template"><?php esc_html_e( 'Message Template Name', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<input type="text" id="omnichat-meta-template" class="regular-text" name="omnichat_settings[meta_template_name]" value="<?php echo esc_attr( $options['meta_template_name'] ); ?>" placeholder="cart_recovery">
							<p class="description"><?php esc_html_e( 'Name of a template approved in Meta Business Manager, with {{1}} = customer name and {{2}} = cart link. Recommended: WhatsApp only delivers free-text messages within 24 hours of the customer messaging you first — templates deliver any time. Leave empty to send the plain "Abandoned cart message" text instead.', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-meta-template-lang"><?php esc_html_e( 'Template Language Code', 'omnichat-order-chat' ); ?></label></th>
						<td><input type="text" id="omnichat-meta-template-lang" class="small-text" style="width:110px" name="omnichat_settings[meta_template_lang]" value="<?php echo esc_attr( $options['meta_template_lang'] ); ?>" placeholder="en_US"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto-send Recovery Messages', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-toggle">
								<input type="checkbox" name="omnichat_settings[enable_auto_recovery_send]" value="yes" <?php checked( $options['enable_auto_recovery_send'], 'yes' ); ?>>
								<span class="omnichat-toggle-slider"></span>
							</label>
							<p class="description"><?php esc_html_e( 'Automatically send the recovery message to abandoned carts (runs every 15 minutes, max 10 per run, carts no older than 7 days). Off by default — no message is ever sent without turning this on.', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="omnichat-recovery-delay"><?php esc_html_e( 'Send After (minutes)', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<input type="number" id="omnichat-recovery-delay" class="small-text" name="omnichat_settings[recovery_send_delay]" value="<?php echo esc_attr( $options['recovery_send_delay'] ); ?>" min="5" max="10080" step="1">
							<p class="description"><?php esc_html_e( 'How long a cart must sit abandoned before the automatic message goes out (5–10080).', 'omnichat-order-chat' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Test Your API Connection', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Save your settings above first, then send a test. With no template configured, the built-in "hello_world" template is used — every Meta test number has it pre-approved. The recipient must be a verified test recipient while your Meta app is in development mode.', 'omnichat-order-chat' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="omnichat-test-phone"><?php esc_html_e( 'Recipient Phone', 'omnichat-order-chat' ); ?></label></th>
						<td>
							<input type="text" id="omnichat-test-phone" class="regular-text" placeholder="+15551234567">
							<button type="button" class="button button-secondary" id="omnichat-send-test-btn"><?php esc_html_e( 'Send Test Message', 'omnichat-order-chat' ); ?></button>
							<p class="description" id="omnichat-test-result" aria-live="polite"></p>
						</td>
					</tr>
				</table>
			</div>

			<div class="omnichat-card">
				<div class="omnichat-card-head">
					<h3><?php esc_html_e( 'Conversion Tracking', 'omnichat-order-chat' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Fires click events into your existing Google Analytics 4 (gtag) and Meta Pixel (fbq) installations. No extra scripts are loaded by OmniChat.', 'omnichat-order-chat' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Google Analytics 4 Events', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-switch">
								<input type="checkbox" name="omnichat_settings[enable_ga4_tracking]" value="yes" <?php checked( $options['enable_ga4_tracking'], 'yes' ); ?>>
								<span class="omnichat-slider"></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Meta Pixel Events', 'omnichat-order-chat' ); ?></th>
						<td>
							<label class="omnichat-switch">
								<input type="checkbox" name="omnichat_settings[enable_fb_tracking]" value="yes" <?php checked( $options['enable_fb_tracking'], 'yes' ); ?>>
								<span class="omnichat-slider"></span>
							</label>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php submit_button( __( 'Save All Settings', 'omnichat-order-chat' ) ); ?>
	</form>
</div>
