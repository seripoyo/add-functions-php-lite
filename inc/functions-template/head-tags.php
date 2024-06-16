		<ul>
			<?php foreach ( $functions_list as $key => $label ) :
				?>
				<?php
				if ( in_array(
					$key,
					array(
						'remove_prev_next',
						'remove_emoji',
						'remove_windows',
						'none_rss',
						'shorten_url',
						'edt_uri',
						'self_pinback_invalid',
						'wp_version',
						'stop_standard_sitemap_output',
						'no_output_rest_api',
					)
				) ) :
					?>
					<li>
						<label for="<?php echo esc_attr( $key ); ?>" class="toggle">
							<input class="toggle__input" type="checkbox" name="add_functions_php_settings[<?php echo esc_attr( $key ); ?>]" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( 1, isset( $options[ esc_attr( $key ) ] ) ? $options[ esc_attr( $key ) ] : 0, true ); ?>>
							<span class="toggle__slider"></span>
						</label>
						<span><?php  echo esc_html( $label ); ?></span>
					</li>
					<br>
					<?php endif; ?>
			<?php endforeach; ?>
		</ul>
