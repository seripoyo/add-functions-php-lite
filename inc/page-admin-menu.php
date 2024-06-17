<?php
// 必要なファイルを読み込む
$admin_menu_path = SERVER_PATH . 'class/class-admin-menu.php';

// デバッグ: $admin_menu_pathの内容を確認

require_once $admin_menu_path;

// 現在のユーザーを取得
$current_user = wp_get_current_user();

// ユーザー一覧を取得、ただし管理者権限を持つユーザーを除外
$users = get_users();

// 現在のユーザーを配列の先頭に移動
$current_user_key = array_search($current_user, $users);
if ($current_user_key !== false) {
    unset($users[$current_user_key]);
    array_unshift($users, $current_user);
}

// 保存された新しいメニュー名を取得
$custom_menu_names = get_option( 'custom_menu_names', array() );
// 保存されたカスタムメニューアイコンを取得
$custom_menu_icons = get_option( 'custom_menu_icons', array() );
// サイドバーのメニュー項目を取得
$sidebar_menu_customizer = new \Add_function_PHP\Side_Menu\AFP_Side_Menu();
$labels                  = $sidebar_menu_customizer->get_sidebar_menu_items();

?>
<style>
	.grid_header,
	.grid_form_contents_item {
		display: grid;
		grid-template-columns: 200px repeat(<?php echo count( $users ) + 1; ?>, 130px)  600px;
		justify-content: space-around;
		align-content: stretch;
		align-items: center;
	}

	.grid_form_contents {
		display: grid;
		justify-content: space-around;
		align-content: stretch;
		align-items: center;
	}

	#adminmenu .wp-menu-image {
		opacity: 0;
		transition: opacity 0.5s ease-in-out;
	}

	#adminmenu .wp-menu-image.icon-loaded {
		opacity: 1;
	}
</style>
<div>
	<div class="container wrapper" id="SideMenu_custom">
		<h1>サイドメニュー調整</h1>
		<form id="custom-menu-form" method="post" action="options.php">
			<?php
			settings_fields( 'sidebar-menu-customizer-settings' );
			do_settings_sections( 'sidebar-menu-customizer' );

			$selected_options  = get_option( 'sidebar_menu_hidden_options', array() );
			$custom_menu_names = get_option( 'custom_menu_names', array() );
			$custom_menu_icons = get_option( 'custom_menu_icons', array() );
			?>

			<div class="grid-container">
				<div class="grid_header">
					<div class="grid-item label heading"><?php _e( 'side menu', 'add-functions-php' ); ?></div>
					<?php foreach ( $users as $user ) : ?>
						<div class="grid-item label user"><?php echo esc_html( $user->display_name ); ?></div>
					<?php endforeach; ?>
					<div class="grid-item label custom"><?php _e( 'Text to be displayed in sidebar', 'add-functions-php' ); ?></div>
				</div>

							<div class="grid_form_contents">
					<?php foreach ( $labels as $id => $label ) : ?>
						<?php
						if ( $id === 'edit-comments.php' ) {
							$label = __( 'Comments', 'add-functions-php' );
						}
						if ( $id === 'plugins.php' ) {
							$label = __( 'Plugins', 'add-functions-php' );
						}
						?>
						<div class="grid_form_contents_item">
							<div class="grid-item label">
								<span class="setting_icon_preview <?php echo esc_attr( $custom_menu_icons[ $id ] ?? '' ); ?>" id="icon_preview_<?php echo esc_attr( $id ); ?>"></span>
								<?php echo esc_html( $label ); ?>
							</div>


							<?php foreach ( $users as $user ) : ?>
								<div class="grid-item user">
									<label class="toggle">
										<input class="toggle__input" type="checkbox" name="sidebar_menu_hidden_options[user_<?php echo esc_attr( $user->ID ); ?>][<?php echo esc_attr( $id ); ?>]" 
																																					<?php
																																																			$checked = isset( $selected_options[ 'user_' . $user->ID ][ $id ] ) ? $selected_options[ 'user_' . $user->ID ][ $id ] : '1';
																																																			checked( $checked, '1' );
																																					?>
										role="switch">
										<span class="toggle__slider"></span>
									</label>
								</div>
							<?php endforeach; ?>

							<div class="grid-item custom txt">
								<input placeholder="" class="textbox" type="text" name="custom_menu_names[<?php echo esc_attr( $id ); ?>]" id="menu_name_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $custom_menu_names[ $id ] ?? '' ); ?>">
								<label class="label" for="menu_name_<?php echo esc_attr( $id ); ?>"></label>
							</div>
							<div class="grid-item setting-icon-spacer">
								<input id="setting_icon_<?php echo esc_attr( $id ); ?>" class="button dashicons-picker" type="button" value="<?php _e( 'Select Icon', 'add-functions-php' ); ?>" data-target="<?php echo esc_attr( $id ); ?>">
							</div>
							<div class="grid-item custom txt">
								<input type="hidden" name="custom_menu_icons[<?php echo esc_attr( $id ); ?>]" id="menu_icon_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $custom_menu_icons[ $id ] ?? '' ); ?>">
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
				<?php
				$button_text = __( 'Save settings', 'add-functions-php' );
				submit_button( $button_text );
				?>
		</form>
	</div>
	<div class="dashicon-picker-container" id="side_menu_icon" style="display: none;">
		<ul class="dashicon-picker-list">
			<?php
			require SERVER_PATH . 'assets/icon_fonts/icon_box.php';
			foreach ( $icons as $icon_name => $icon_code ) :
				?>
				<li data-icon="<?php echo esc_attr( $icon_code ); ?>">
					<a href="#" title="<?php echo esc_attr( $icon_name ); ?>">
						<span class="<?php echo esc_attr( $icon_name ); ?>"></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<button class="reset-icon-selection"><?php _e( 'Restore default settings', 'add-functions-php' ); ?></button>
	</div>
</div>
