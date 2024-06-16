<ul>
	<?php foreach ( $functions_list as $key => $label ) :
		?>
		<?php
		if ( in_array(
			$key,
			array(
				'img_resize_cancel',
				'remove_blank_p',
				'svg_upload',
				'pdf_upload',
				'template_file',
				'redirect_single_search_result',
				'maintenance_mode',
				'hide_update_notices',
				'enable_wp_major_auto_update',
				'enable_auto_updates',
				'remove_default_widgets',
				'fontawesome_v6',
				'not_output_srcset',

			)
		) ) :
			?>
			<li>
				<label for="<?php echo esc_attr( $key ); ?>" class="toggle">
					<input class="toggle__input" type="checkbox" name="add_functions_php_settings[<?php echo esc_attr( $key ); ?>]" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( 1, isset( $options[ esc_attr( $key ) ] ) ? $options[ esc_attr( $key ) ] : 0, true ); ?>>
					<span class="toggle__slider"></span>
				</label>
				<span><?php echo esc_html( $label ); ?></span>
			</li>
			<br>
			<?php endif; ?>
		<?php
		if ( in_array(
			$key,
			array(
				'hidden_admin_bar',
				'add_browser_name_to_body_class',
				'add_slug_body_class',
			)
		) ) :
			?>
<li>
						<label for="<?php echo esc_attr( $key ); ?>" class="toggle">
							<input disabled class="toggle__input input_pro" type="checkbox" name="add_functions_php_settings[<?php echo esc_attr( $key ); ?>]" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( 1, isset( $options[ esc_attr( $key ) ] ) ? $options[ esc_attr( $key ) ] : 0, true ); ?>>
							<a href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/" target="_blank" rel="noopener noreferrer"><span class="toggle__slider input_pro_slider">PRO</span></a>
						</label>
						<span><?php echo esc_html( $label ); ?></span>

					</li>
			<br>
			<?php endif; ?>
	<?php endforeach; ?>
	<li><a target="_blank" href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/">半額で体験版を試すならこちら</a></li>
</ul>


<script type="text/javascript">
jQuery(document).ready(function($) {
	function initColorPicker(widget) {
		widget.find(".alpha-color-picker").wpColorPicker({
			change: function(event, ui) {
				var color = ui.color.toString();
				var index = $(this).closest('.widget_item_container').data('item-index');
				var containerIndex = $(this).closest('.widget_container').data('index');
				var iconPreviewId = '#icon_preview_' + containerIndex + '_' + index;
				$(iconPreviewId).css('background-color', color);
			},
			clear: function() {
				var index = $(this).closest('.widget_item_container').data('item-index');
				var containerIndex = $(this).closest('.widget_container').data('index');
				var iconPreviewId = '#icon_preview_' + containerIndex + '_' + index;
				$(iconPreviewId).css('background-color', 'transparent');
			}
		});
	}

	$('.widget_container').each(function() {
		initColorPicker($(this));
	});
});
</script>
