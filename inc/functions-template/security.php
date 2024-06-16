<ul>
	<?php foreach ( $functions_list as $key => $label ) :
		?>
		<?php
		if ( in_array(
			$key,
			array(
				'deny_php',
				'hidden_media_url',
					'disable_rest_api_user',
				'login_limit_3',
				'remove_author_archive',
				'login_error_message',
			)
		) ) :
			?>
			<li>
				<label for="<?php echo esc_attr( $key ); ?>" class="toggle">
					<input class="toggle__input" type="checkbox" name="add_functions_php_settings[<?php echo esc_attr( $key ); ?>]" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( 1, isset( $options[ esc_attr( $key ) ] ) ? $options[ esc_attr( $key ) ] : 0, true ); ?>>
					<span class="toggle__slider"></span>
				</label>
				<span></span>
				<span><?php echo esc_html( $label ); ?></span>
			</li>
			<br>
			<?php endif; ?>
		<?php
		if ( in_array(
			$key,
			array(
				'reject_comments',
				'reject_login',
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
</ul>
<ul>
	<li>
		<h3><?php _e( 'Change the domain name /wp-content/uploads/{file name} and the URL of the output media library', 'add-functions-php' ); ?></h3>
		<div class="pro-contents-hidden-container">
		<div class="pro-contents-hidden-inner">
			<p class="pro-contents-hidden-txt"><a target="_blank" href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/"><?php _e( 'This feature is only available in the paid version', 'add-functions-php' ); ?>
</a></p>
		<label class="text_input widget_text_input">
			<input disabled placeholder="<?php _e( 'Enter the domain name/{name of the folder you want} followed by the folder name', 'add-functions-php' ); ?>" class="textbox" type="text" name="add_functions_php_settings[upload_path]" id="menu_name_upload_path" value="<?php echo esc_attr( $options['upload_path'] ?? '' ); ?>">
			<span></span>
		</label>
		<p><?php _e( 'Media library URL after configuration：', 'add-functions-php' ); ?><a id="media_library_url_preview" href="<?php echo site_url(); ?>/"><?php echo site_url(); ?>/</a></p>
		</div>
		</div>
	</li>
	<li>
		<h3><?php _e( 'If you wish to change the login page URL, please enter any string below.', 'add-functions-php' ); ?></h3>
		<p><?php _e( 'recommendation：', 'add-functions-php' ); ?><a href="https://rakko.tools/tools/6/" target="_blank">ランダムパスワード簡単に大量生成 | ラッコツールズ</a></p>
		<div class="pro-contents-hidden-container">
		<div class="pro-contents-hidden-inner">
		<p class="pro-contents-hidden-txt"><a target="_blank" href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/"><?php _e( 'This feature is only available in the paid version', 'add-functions-php' ); ?></a></p>
		<label class="text_input widget_text_input widget_text_input">
			<input disabled placeholder="login.php?の後に続く文字列を入力する<?php _e( 'Enter the string that follows “login.php?"', 'add-functions-php' ); ?>" class="textbox" type="text" name="add_functions_php_settings[login_txt]" id="menu_name_login_txt" value="<?php echo esc_attr( $options['login_txt'] ?? '' ); ?>">
			<span></span>
		</label>

		<ul>
			<li><?php _e( 'The URL of the login page after setup：', 'add-functions-php' ); ?><a href="<?php echo site_url(); ?>/login.php?<?php echo esc_attr( $options['login_txt'] ?? '' ); ?>" id="login_url_preview" target="_blank" rel="noopener noreferrer"><?php echo site_url(); ?>/wp-login.php?<?php echo esc_attr( $options['login_txt'] ?? '' ); ?></a></li>
		</ul>
		</div>
		</div>
		</li>
	<li>
		<h3><?php _e( 'If you wish to apply BASIC authentication to the entire site, please enter your ID and password below.', 'add-functions-php' ); ?></h3>
		<div class="pro-contents-hidden-container">
		<div class="pro-contents-hidden-inner">
		<p class="pro-contents-hidden-txt"><a target="_blank" href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/"><?php _e( 'This feature is only available in the paid version', 'add-functions-php' ); ?></a></p>
		<label class="text_input widget_text_input">
			<input disabled placeholder="<?php _e( 'Enter user ID for BASIC authentication', 'add-functions-php' ); ?>" class="textbox" type="text" name="add_functions_php_settings[basic_auth_id]" id="menu_name_basic_auth_id" value="<?php echo esc_attr( $options['basic_auth_id'] ?? '' ); ?>">
			<span></span>
		</label>
		<label class="text_input widget_text_input">
			<input disabled placeholder="<?php _e( 'Enter the password for BASIC authentication', 'add-functions-php' ); ?>" class="textbox" type="text" name="add_functions_php_settings[basic_auth_pass]" id="menu_name_basic_auth_pass" value="<?php echo esc_attr( $options['basic_auth_pass'] ?? '' ); ?>">
			<span></span>
		</label>			
		</div>
		</div>

	</li>
		<li><a target="_blank" href="https://add-functions-php.seripoyo.work/downloads/add-functions-php/">半額で体験版を試すならこちら</a></li>
</ul>

<script>
jQuery(document).ready(function($) {
	// URLを更新する関数
	function updateMediaLibraryUrl() {
		var uploadPath = $('#menu_name_upload_path').val(); // テキストボックスから値を取得
		var domain = '<?php echo site_url(); ?>'; // ドメイン名を取得
		var mediaLibraryUrl = domain + '/' + uploadPath; // メディアライブラリのURLを組み立て
		$('#media_library_url_preview').attr('href', mediaLibraryUrl).text(mediaLibraryUrl); // プレビューのリンクを更新
	}

	// ページ読み込み時にURLを更新
	updateMediaLibraryUrl();

	// テキストボックスの値が変更されたときにURLを更新
	$('#menu_name_upload_path').on('input', function() {
		updateMediaLibraryUrl();
	});
});
jQuery(document).ready(function($) {
	function updateMediaLibraryUrl() {
		var uploadPath = $('#menu_name_upload_path').val(); // テキストボックスから値を取得
		var domain = '<?php echo site_url(); ?>'; // ドメイン名を取得
		var mediaLibraryUrl = domain + '/' + uploadPath; // メディアライブラリのURLを組み立て
		$('#media_library_url_preview').attr('href', mediaLibraryUrl).text(mediaLibraryUrl); // プレビューのリンクを更新
	}

	// ページ読み込み時にURLを更新
	updateMediaLibraryUrl();

	// テキストボックスの値が変更されたときにURLを更新
	$('#menu_name_upload_path').on('input', function() {
		updateMediaLibraryUrl();
	});

	// テキストボックスの値が変更されたときのイベントハンドラーを設定
	$('#menu_name_login_txt').on('input', function() {
		// テキストボックスから値を取得
		var LoginPass = $(this).val();

		// ドメイン名を取得
		var domain = '<?php echo site_url(); ?>/wp-login.php?';

		// ログインURLを組み立て
		var LoginUrl = domain + LoginPass;

		// プレビューのリンクを更新
		$('#login_url_preview').attr('href', LoginUrl).text(LoginUrl);
	});
});
</script>