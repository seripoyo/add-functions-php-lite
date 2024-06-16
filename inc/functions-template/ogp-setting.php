<ul>
	<?php foreach ( $functions_list as $key => $label ) :
		?>
		<?php
		if ( in_array(
			$key,
			array(
				'ogp_meta_tag',
				'taxonomy_description',
				'ogp_img',
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
	<?php endforeach; ?>
</ul>
<h3 class="plugin_heading2"><?php _e( 'OGP image to output when eye-catching is not set', 'add-functions-php' ); ?></h3>
<div class="input_item_container" id="ogp_image_container">
	<img id="ogp_image_preview" class="lazyload" src="<?php echo esc_attr( $settings->get_option( 'ogp_image' ) ); ?>" style="max-width: 100%; height: auto; margin-top: 10px; display: <?php echo empty( $settings->get_option( 'ogp_image' ) ) ? 'none' : 'block'; ?>;" />
	<label for="ogp_image">
		<input type="hidden" id="ogp_image" name="add_functions_php_settings[ogp_image]" value="<?php echo esc_attr( $this->get_option( 'ogp_image' ) ); ?>" />
		<button type="button" class="button" id="upload_ogp_image_button"><?php _e( 'Upload Image', 'add-functions-php' ); ?></button>
		<button type="button" class="button" id="remove_ogp_image_button"><?php _e( 'Delete image', 'add-functions-php' ); ?></button>
	</label>
</div>
<div class="input_item_container" id="twitter_card_container">
	<h3 class="plugin_heading2"><?php _e( 'Twitter Card Settings', 'add-functions-php' ); ?></h3>
	<ul>
		<?php
		foreach ( $functions_list as $key => $label ) :
			?>
				<?php
				if ( in_array( $key, array( 'twitter_card' ) ) ) :
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
		<?php endforeach; ?>

		<li>
			<p><?php _e( 'Twitter (X) account ID', 'add-functions-php' ); ?></p>
			<label class="text_input widget_text_input">
				<input placeholder="<?php _e( 'Please fill in your ID without the @', 'add-functions-php' ); ?>" class="textbox" type="text" name="add_functions_php_settings[twitter_id]" id="menu_name_twitter_id" value="<?php echo esc_attr( $options['twitter_id'] ?? '' ); ?>">
				<span></span>
			</label>
		</li>
		<li>
			<p><?php _e( 'Card type you want to use', 'add-functions-php' ); ?></p>
			<ul class="radio_container">
				<?php
				$twitter_card_type = array(
					'summary_large_image' => __( 'summary_large_image (Displays featured image larger)', 'add-functions-php' ),
					'summary'             => __( 'summary (Displays featured image smaller)', 'add-functions-php' ),
				);

					$current_type = $options['twitter_card_type'] ?? 'summary_large_image'; // 現在の設定値またはデフォルト値

				foreach ( $twitter_card_type as $id => $label ) :
					?>
					<li>
						<label class="radio_label <?php echo htmlspecialchars( $id ); ?>">
							<input class="radio_input" id="twitter_card_type_<?php echo htmlspecialchars( $id ); ?>" type="radio" name="add_functions_php_settings[twitter_card_type]" value="<?php echo htmlspecialchars( $id ); ?>" <?php checked( $current_type, $id ); ?>>
							<span><?php echo htmlspecialchars( $label ); ?></span>
						</label>
					</li>
			<?php endforeach; ?>
			</ul>
			</li>
	</ul>
</div>
