
<div class="input_item_wrapper">
	<h2 class="plugin_heading2"><?php _e( 'Font setting in the admin', 'add-functions-php' ); ?></h2>
	<div class="input_item_container">
		<ul class="font_list_container">
			<?php
			$admin_font = isset( $options['admin_font'] ) ? $options['admin_font'] : 'default';
			$fonts      = $this->appearance->get_font_options();

			foreach ( $fonts as $id => $label ) :
				?>
				<li>
					<label class="radio_label <?php echo htmlspecialchars( $id ); ?>">
<input class="radio_input" id="admin_font_<?php echo htmlspecialchars( $id ); ?>" type="radio" name="add_functions_php_settings[admin_font]" value="<?php echo htmlspecialchars( $id ); ?>" <?php checked( $admin_font, $id ); ?>>


						<span><?php echo esc_html( $label ); ?></span>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>



<div class="input_item_wrapper">
	<h2 class="plugin_heading2"><?php _e( 'Admin Bar/Side Menu Color Settings', 'add-functions-php' ); ?></h2>
	<div class="input_item_container">
		<!-- 管理画面メニュー背景カラー 配色設定 -->
		<label for="" id="admin_menu_bg_color">
			<?php _e( 'Menu Background Color', 'add-functions-php' ); ?>
			<?php $this->appearance->render_admin_menu_bg_color_field(); ?>
		</label>
	</div>
	<div class="input_item_container">
		<!-- 管理画面メニューテキストカラー 配色設定 -->
		<label for="" id="admin_menu_text_color">
			<?php _e( 'Menu Text Color', 'add-functions-php' ); ?>
			<?php $this->appearance->render_admin_menu_text_color_field(); ?>
		</label>
	</div>
</div>
