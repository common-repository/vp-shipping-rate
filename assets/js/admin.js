jQuery(document).ready(function($) {

  //Settings page
  var vp_shipping_rate_settings = {
		$pricing_table: $('.vp-shipping-rate-settings-pricings'),
		init: function() {

			//Conditional logic controls
			var conditional_fields = [this.$pricing_table];
			var conditional_fields_ids = ['pricings'];

			//Setup conditional fields for pricing
			conditional_fields.forEach(function(table, index){
				var id = conditional_fields_ids[index];
				var singular = id.slice(0, -1);
				singular = singular.replace('_', '-');
				table.on('change', 'select.condition', {group: id}, vp_shipping_rate_settings.change_x_condition);
				table.on('change', 'select.vp-shipping-rate-settings-repeat-select', function(){vp_shipping_rate_settings.reindex_x_rows(id)});
				table.on('click', '.add-row', {group: id}, vp_shipping_rate_settings.add_new_x_condition_row);
				table.on('click', '.delete-row', {group: id}, vp_shipping_rate_settings.delete_x_condition_row);
				table.on('click', '.delete-'+singular, {group: id}, vp_shipping_rate_settings.delete_x_row);
				$('.vp-shipping-rate-settings-'+singular+'-add a.add:not([data-disabled]').on('click', {group: id, table: table}, vp_shipping_rate_settings.add_new_x_row);

				//If we already have some notes, append the conditional logics
				table.find('ul.conditions[data-options]').each(function(){
					var saved_conditions = $(this).data('options');
					var ul = $(this);

					saved_conditions.forEach(function(condition){
						var sample_row = $('#vp_shipping_rate_'+id+'_condition_sample_row').html();
						sample_row = $(sample_row);
						sample_row.find('select.condition').val(condition.category);
						sample_row.find('select.comparison').val(condition.comparison);
						sample_row.find('.value').removeClass('selected');
						sample_row.find('.value[data-condition="'+condition.category+'"]').val(condition.value).addClass('selected').attr('disabled', false);
						ul.append(sample_row);
					});
				});

				//Reindex the fields
				vp_shipping_rate_settings.reindex_x_rows(id);

			});

		},
		change_x_condition: function(event) {
			var condition = $(this).val();

			//Hide all selects and make them disabled(so it won't be in $_POST)
			$(this).parent().find('.value').removeClass('selected').prop('disabled', true);
			$(this).parent().find('.value[data-condition="'+condition+'"]').addClass('selected').prop('disabled', false);
		},
		add_new_x_condition_row: function(event) {
			var sample_row = $('#vp_shipping_rate_'+event.data.group+'_condition_sample_row').html();
			$(this).closest('ul.conditions').append(sample_row);
			vp_shipping_rate_settings.reindex_x_rows(event.data.group);
			return false;
		},
		delete_x_condition_row: function(event) {
			$(this).parent().remove();
			vp_shipping_rate_settings.reindex_x_rows(event.data.group);
			return false;
		},
		reindex_x_rows: function(group) {
			var group = group.replace('_', '-');
			$('.vp-shipping-rate-settings-'+group).find('.vp-shipping-rate-settings-repeat-item').each(function(index){
				$(this).find('textarea, select, input').each(function(){
					var name = $(this).data('name');
					if(name) {
						name = name.replace('X', index);
						$(this).attr('name', name);
					}
				});

				//Reindex conditions too
				$(this).find('li').each(function(index_child){
					$(this).find('select, input').each(function(){
						var name = $(this).data('name');
						if(name) {
							name = name.replace('Y', index_child);
							name = name.replace('X', index);
							$(this).attr('name', name);
						}
					});
				});

				$(this).find('.vp-shipping-rate-settings-repeat-select').each(function(){
					var val = $(this).val();
					if($(this).hasClass('vp-shipping-rate-settings-advanced-option-property')) {
						$('.vp-shipping-rate-settings-advanced-option-value option').hide();
						$('.vp-shipping-rate-settings-advanced-option-value option[value^="'+val+'"]').show();

						if(!$('.vp-shipping-rate-settings-advanced-option-value').val().includes(val)) {
							$('.vp-shipping-rate-settings-advanced-option-value option[value^="'+val+'"]').first().prop('selected', true);
						}
					}

					var label = $(this).find('option:selected').text();
					$(this).parent().find('label span').text(label);
					$(this).parent().find('label span').text(label);
					$(this).parent().find('label i').removeClass().addClass(val);
				});

				if(group == 'pricings') {
					if($(this).find('input[value*="packeta"]:checked').length || $(this).find('input[value*="gls_"]:checked').length) {
						$(this).find('.vp-shipping-rate-settings-pricing-countries').show();
					} else {
						$(this).find('.vp-shipping-rate-settings-pricing-countries').hide();
					}
				}

			});

			$( document.body ).trigger( 'wc-enhanced-select-init' );

			return false;
		},
		add_new_x_row: function(event) {
			var group = event.data.group;
			var table = event.data.table;
			var singular = group.slice(0, -1);
			var sample_row = $('#vp_shipping_rate_'+singular+'_sample_row').html();
			var sample_row_conditon = $('#vp_shipping_rate_'+group+'_condition_sample_row').html();
			sample_row = $(sample_row);
			sample_row.find('ul.conditions').append(sample_row_conditon);
			table.append(sample_row);
			vp_shipping_rate_settings.reindex_x_rows(group);
			$( document.body ).trigger( 'wc-enhanced-select-init' );
			return false;
		},
		delete_x_row: function(event) {
			$(this).closest('.vp-shipping-rate-settings-repeat-item').remove();
			vp_shipping_rate_settings.reindex_x_rows(event.data.group);
			return false;
		}
  	}

	//Init settings page
	if($('.vp-shipping-rate-settings-pricings').length) {
    	vp_shipping_rate_settings.init();
	}

});
