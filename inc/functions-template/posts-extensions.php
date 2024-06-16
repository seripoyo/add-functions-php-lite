		<ul>
			<?php foreach ( $functions_list as $key => $label ) :
				?>
				<?php
				if ( in_array(
					$key,
					array(
						'taxonomy_description',
						'add_last_updated',
						'tag_checkbox',
						'show_featured_image',
						'add_post_id',
						'add_alt',
						'last_empty_alt',
						'duplicate_post_button',
						'slug_ja_to_en',
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
