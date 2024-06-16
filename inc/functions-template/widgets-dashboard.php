<div class="widgets_wrapper">
	<?php
	$widget_design_types = get_option( ( 'add_functions_php_settings' ) )['widget_design_type'] ?? array( '0' => 'widget_01' );
	$selected_options    = get_option( ( 'add_functions_php_settings' ) );
	$widget_names        = $selected_options['widget_name'] ?? array();
	$widget_items        = $selected_options['widget_items'] ?? array();

	// 送信済みデータがある場合は、$_POSTから取得
	$submitted_data = isset( $_POST['add_functions_php_settings'] ) ? $_POST['add_functions_php_settings'] : array();


	foreach ( $widget_design_types as $index => $current_type ) :
		$widget_name = $widget_names[ $index ]['widget_name'] ?? '';
		?>
		<div class="widget_container" data-index="<?php echo $index; ?>">
			<h3><?php _e( 'Widget outline to be created', 'add-functions-php' ); ?></h3>

	
	<div class="pro-contents-hidden-container">
		<div class="pro-contents-hidden-inner">
						<div class="grid_form_contents_item">
				<label class="widget_text_input widget_name_input" for="widget_name_<?php echo $index; ?>">
					<input type="text"  placeholder="<?php _e( 'Enter a name for the widget surrounding the button', 'add-functions-php' ); ?>" name="add_functions_php_settings[widget_name][<?php echo $index; ?>][widget_name]" id="widget_name_<?php echo $index; ?>" value="<?php echo esc_attr( $selected_options['widget_name'][ $index ]['widget_name'] ?? '' ); ?>">
					<span></span>
				</label>
			</div>
<ul class="widget_radio" data-selected-type="<?php echo esc_attr( str_replace( '-', '', $current_type ) ); ?>">
				<?php
				$widget_design_type = array(
					'widget01' => 'widget01',
					'widget02' => 'widget02',
					'widget03' => 'widget03',
				);

				foreach ( $widget_design_type as $id => $label ) :
					?>
					<li class="<?php echo htmlspecialchars( $id ); ?>">
						<label class="radio_label <?php echo htmlspecialchars( $id ); ?>">
							<input class="radio_input" id="widget_design_type_<?php echo $index; ?>_<?php echo htmlspecialchars( $id ); ?>" type="radio" name="add_functions_php_settings[widget_design_type][<?php echo $index; ?>]" value="<?php echo htmlspecialchars( $id ); ?>" <?php checked( $submitted_data['widget_design_type'][ $index ] ?? '', $id ); ?>>
							<span><?php echo htmlspecialchars( $label ); ?></span>
							<ul class="<?php echo htmlspecialchars( $label ); ?> add-custom-widget">
								<li>
									<a href="#">
										<span class="add-icon-edit_square"></span>
										<p><?php echo htmlspecialchars( $label ); ?></p>
									</a>
								</li>
							</ul>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>

	<h4><?php _e( 'Button settings for widgets', 'add-functions-php' ); ?></h4>			
			<p class="pro-contents-hidden-txt">
				<a href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/" target="_blank">
					<?php _e( 'This feature is only available in the paid version', 'add-functions-php' ); ?>
				</a>
			</p>
			<div class="widget_items_wrapper">

				<?php
				$widget_items_data = $widget_items[ $index ] ?? array();
				foreach ( $widget_items_data as $item_index => $item ) :
					?>
					<div class="widget_item_container" data-item-index="<?php echo $item_index; ?>">
					
						<div class="grid_form_contents_item">
							<label class="widget_text_input" for="widget_label_<?php echo $index . '_' . $item_index; ?>">
								<input placeholder="<?php _e( 'Enter the label of the button you want to add to the widget', 'add-functions-php' ); ?>" type="text" name="add_functions_php_settings[widget_items][<?php echo $index; ?>][<?php echo $item_index; ?>][widget_label]" id="widget_label_<?php echo $index . '_' . $item_index; ?>" value="<?php echo esc_attr( $item['widget_label'] ?? '' ); ?>">
							</label>
							<span></span>
						</div>
						<div class="widget_text_input grid_form_contents_item widget_url">
							<label class="widget_text_input" for="widget_url_<?php echo $index . '_' . $item_index; ?>">
								<input placeholder="<?php _e( 'Please enter the URL to which you want the button to link', 'add-functions-php' ); ?>" type="text" name="add_functions_php_settings[widget_items][<?php echo $index; ?>][<?php echo $item_index; ?>][widget_url]" id="widget_url_<?php echo $index . '_' . $item_index; ?>" value="<?php echo esc_attr( $item['widget_url'] ?? '' ); ?>">
							</label>
							<span></span>
						</div>
						<div class="grid_form_contents_item">
							<label for="widget_color_<?php echo $index . '_' . $item_index; ?>">
								<input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[widget_items][<?php echo $index; ?>][<?php echo $item_index; ?>][widget_color]" id="widget_color_<?php echo $index . '_' . $item_index; ?>" value="<?php echo esc_attr( $item['widget_color'] ?? '#1d2327' ); ?>" />
							</label>
						</div>
						<div class="grid_item custom txt">
							<div class="grid_item setting_icon_spacer">
								<span class="add-icon-add_circle"></span>
								<input id="setting_icon_<?php echo $index . '_' . $item_index; ?>" class="button dashicons-picker" type="button" value="
								<?php
								$value = __( 'Select Icon', 'add-functions-php' );
								echo $value;
								?>
								" data-target="<?php echo $index . '_' . $item_index; ?>">
								<span class="setting_icon_preview <?php echo esc_attr( $item['widget_icon'] ?? '' ); ?>" id="icon_preview_<?php echo $index . '_' . $item_index; ?>"></span>
							</div>
							<input type="hidden" name="add_functions_php_settings[widget_items][<?php echo $index; ?>][<?php echo $item_index; ?>][widget_icon]" id="menu_icon_<?php echo $index . '_' . $item_index; ?>" value="<?php echo esc_attr( $item['widget_icon'] ?? '' ); ?>">
						</div>
						<button type="button" class="remove_widget_item" data-container-index="<?php echo $index; ?>" data-item-index="<?php echo $item_index; ?>"><?php _e( 'Delete button', 'add-functions-php' ); ?></button>
					</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="add_widget_item" data-container-index="<?php echo $index; ?>">
			<span class="add-icon-add_ad"></span><?php _e( 'Add a button', 'add-functions-php' ); ?>
			</button>
			<button type="button" class="remove_widget_container" data-container-index="<?php echo $index; ?>">
			<?php _e( 'Delete a widget', 'add-functions-php' ); ?>
			</button>
					</div>

	<?php endforeach; ?>
</div>

		</div>
		</div>


<div class="dashicon-picker-container" id="widget_icon_btn" style="display: none;">
	<ul class="dashicon-picker-list">
		<?php

		// icon_box.phpファイルを読み込む。相対パスはicon_box.phpの位置に応じて適宜調整してください。
		// 絶対パスを使用してファイルを読み込む
		$icon_box_path = SERVER_PATH . 'assets/icon_fonts/icon_box.php';
		if ( file_exists( $icon_box_path ) ) {
			include $icon_box_path;

			if ( isset( $icons ) && is_array( $icons ) ) {
				foreach ( $icons as $icon_name => $icon_code ) :
					?>
					<li data-icon="<?php echo esc_attr( $icon_code ); ?>">
						<a href="#" title="<?php echo esc_attr( $icon_name ); ?>">
							<span class="<?php echo esc_attr( $icon_name ); ?>"></span>
						</a>
					</li>
					<?php
		endforeach;
			} else {
				echo 'No icons found in the icon_box.php file.';
			}
		} else {
			echo 'The icon_box.php file does not exist.';
		}
		?>
	</ul>
	<!-- 初期設定に戻すボタンを追加 -->
	<button class="reset_icon_selection"><?php _e( 'Restore default settings', 'add-functions-php' ); ?></button>
</div>