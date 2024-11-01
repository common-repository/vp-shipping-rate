<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Get saved values(this could be better, but only way i could find that works after save)
$saved_values = get_option($this->get_instance_option_key());
if($saved_values && isset($saved_values['pricing'])) {
	$saved_values = $saved_values['pricing'];
}

//Apply filters
$conditions = VP_Shipping_Rate_Conditions::get_conditions('pricings');

//Get sample row data
$group = 'pricings';
$group_id = substr($group, 0, -1);

?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
	</th>
	<td class="forminp <?php echo esc_attr( $data['class'] ); ?>">
		<div class="vp-shipping-rate-settings-pricings">
			<?php if($saved_values): ?>
				<?php foreach ( $saved_values as $pricing_id => $pricing ): ?>
					<div class="vp-shipping-rate-settings-pricing vp-shipping-rate-settings-repeat-item">
						<div class="vp-shipping-rate-settings-pricing-title">
							<div class="cost-field">
								<input placeholder="<?php esc_html_e('Shipping cost(net)', 'vp-shipping-rate'); ?>" type="text" data-name="woocommerce_vp_shipping_rate_pricing[X][cost]" value="<?php echo esc_attr($pricing['cost']); ?>">
								<small><?php echo esc_html(get_woocommerce_currency_symbol()); ?></small>
							</div>
							<a href="#" class="delete-pricing"><?php esc_html_e('delete', 'vp-shipping-rate'); ?></a>
						</div>
						<div class="vp-shipping-rate-settings-pricing-if">
							<div class="vp-shipping-rate-settings-pricing-if-header">
								<span><?php esc_html_e('Apply this pricing, if', 'vp-shipping-rate'); ?></span>
								<select data-name="woocommerce_vp_shipping_rate_pricing[X][logic]">
									<option value="and" <?php if(isset($pricing['logic'])) selected( $pricing['logic'], 'and' ); ?>><?php esc_html_e('All', 'vp-shipping-rate'); ?></option>
									<option value="or" <?php if(isset($pricing['logic'])) selected( $pricing['logic'], 'or' ); ?>><?php esc_html_e('One', 'vp-shipping-rate'); ?></option>
								</select>
								<span><?php esc_html_e('of the following match', 'vp-shipping-rate'); ?></span>
							</div>
							<ul class="vp-shipping-rate-settings-pricing-if-options conditions" <?php if(isset($pricing['conditions'])): ?>data-options="<?php echo esc_attr(wp_json_encode($pricing['conditions'])); ?>"<?php endif; ?>></ul>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="vp-shipping-rate-settings-pricing-add">
			<a href="#" class="button add"><span class="dashicons dashicons-plus-alt2"></span> <span><?php esc_html_e('Add new cost', 'vp-shipping-rate'); ?></span></a>
			<p class="description">
				<?php
				$weight_unit = get_option('woocommerce_weight_unit');
				$length_unit = get_option('woocommerce_dimension_unit');
				echo esc_html(sprintf( __( 'Tip: if you want to hide this shipping method, enter -1 as the cost and if the conditions match, this method will be hidden. You can use dots or commas for decimal points. Measurements: %s and %s.', 'vp-shipping-rate' ), esc_attr($weight_unit), esc_attr($length_unit) ));
				?>
			</p>
		</div>
	</td>
</tr>

<script type="text/html" id="vp_shipping_rate_pricing_sample_row">
	<div class="vp-shipping-rate-settings-pricing vp-shipping-rate-settings-repeat-item">
		<div class="vp-shipping-rate-settings-pricing-title">
			<div class="cost-field">
				<input placeholder="<?php esc_attr_e('Shipping cost(net)', 'vp-shipping-rate'); ?>" type="text" data-name="woocommerce_vp_shipping_rate_pricing[X][cost]">
				<small><?php echo esc_html(get_woocommerce_currency_symbol()); ?></small>
			</div>
			<a href="#" class="delete-pricing"><?php esc_html_e('delete', 'vp-shipping-rate'); ?></a>
		</div>
		<div class="vp-shipping-rate-settings-pricing-if">
			<div class="vp-shipping-rate-settings-pricing-if-header">
				<span><?php esc_html_e('Apply this pricing, if', 'vp-shipping-rate'); ?></span>
				<select data-name="woocommerce_vp_shipping_rate_pricing[X][logic]">
					<option value="and"><?php esc_html_e('All', 'vp-shipping-rate'); ?></option>
					<option value="or"><?php esc_html_e('One', 'vp-shipping-rate'); ?></option>
				</select>
				<span><?php esc_html_e('of the following match', 'vp-shipping-rate'); ?></span>
			</div>
			<ul class="vp-shipping-rate-settings-pricing-if-options conditions"></ul>
		</div>
	</div>
</script>

<script type="text/html" id="vp_shipping_rate_<?php echo esc_attr($group); ?>_condition_sample_row">
	<li>
		<select class="condition" data-name="woocommerce_vp_shipping_rate_<?php echo esc_attr($group_id); ?>[X][conditions][Y][category]">
			<?php foreach ($conditions as $condition_id => $condition): ?>
				<option value="<?php echo esc_attr($condition_id); ?>"><?php echo esc_html($condition['label']); ?></option>
			<?php endforeach; ?>
		</select>
		<select class="comparison" data-name="woocommerce_vp_shipping_rate_<?php echo esc_attr($group_id); ?>[X][conditions][Y][comparison]">
			<option value="equal"><?php esc_html_e('equal', 'vp-shipping-rate'); ?></option>
			<option value="not_equal"><?php esc_html_e('not equal', 'vp-shipping-rate'); ?></option>
			<option value="greater"><?php esc_html_e('greater than', 'vp-shipping-rate'); ?></option>
			<option value="less"><?php esc_html_e('less than', 'vp-shipping-rate'); ?></option>
		</select>
		<?php foreach ($conditions as $condition_id => $condition): ?>
			<?php if($condition['options']): ?>
				<select class="value" data-condition="<?php echo esc_attr($condition_id); ?>" data-name="woocommerce_vp_shipping_rate_<?php echo esc_attr($group_id); ?>[X][conditions][Y][<?php echo esc_attr($condition_id); ?>]" <?php if($condition_id != 'payment_method'): ?>disabled="disabled"<?php endif; ?>>
					<?php foreach ($condition['options'] as $option_id => $option_name): ?>
						<option value="<?php echo esc_attr($option_id); ?>"><?php echo esc_html($option_name); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<input type="text" data-condition="<?php echo esc_attr($condition_id); ?>" data-name="woocommerce_vp_shipping_rate_<?php echo esc_attr($group_id); ?>[X][conditions][Y][<?php echo esc_attr($condition_id); ?>]" class="value <?php if($condition_id == 'weight'): ?>selected<?php endif; ?>" <?php if($condition_id != 'weight'): ?>disabled="disabled"<?php endif; ?>>
			<?php endif; ?>
		<?php endforeach; ?>
		<a href="#" class="add-row"><span class="dashicons dashicons-plus-alt"></span></a>
		<a href="#" class="delete-row"><span class="dashicons dashicons-dismiss"></span></a>
	</li>
</script>