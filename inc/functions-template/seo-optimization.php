		<ul>
<?php
		/**
		 * SEO最適化機能を提供するテンプレートファイル。
		 *
		 * このファイルは、SEO関連の設定を管理するためのチェックボックスを表示します。
		 */
		?>
		
			<?php
			foreach ( $functions_list as $key => $label ) :
				?>
				<?php
				if ( in_array(
					$key,
					array(
						'add_lazy',
						'add_defer_to_jquery',
						'add_async_to_jquery',
						'use_lazysizes',
						'seo_setting',
						'jquery_head',
						'jquery_body',
						'jquery_miragrate',
						'seo_setting'
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
						'setting_enable_keep_alive',
						'cash_img_fonts',
						'compress_file',
						'cash_browser',
						'stream_webp',
						'redirect_https',
						'ignore_etags',
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
			<li><a target="_blank" href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/">PRO版の購入はこちらから</a></li>
		</ul>
