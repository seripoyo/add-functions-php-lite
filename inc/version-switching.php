<?php

defined( 'ABSPATH' ) || exit;

// 指定されたパスと現在のプラグインのパスが一致するかどうかを確認
if ( $current_plugin_path === ADD_FUNCTIONS_PHP_LITE_PATH ) {
	if ( ! defined( 'UPDATE_PATH' ) ) {
		// サーバーのパスを取得
		$server_path = __DIR__ . '/';

		define( 'UPDATE_PATH', $server_path . 'update/' );
		require_once UPDATE_PATH . 'AFP_Lite_plugin.php';
	}
}

if ( ! class_exists( 'Add_Functions_PHP_Lite_Notification' ) ) {

	class Add_Functions_PHP_Lite_Notification {

		public function __construct() {
			add_action( 'admin_notices', array( $this, 'show_trial_notification' ) );
		}

		public function show_trial_notification() {
			// Check if the plugin is active
			if ( is_plugin_active( ADD_FUNCTIONS_PHP_LITE_PATH ) ) {
				// Check if the current page is one of the specified pages
				$current_screen = get_current_screen();
				$current_url    = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

				if (
				$current_screen->id === 'toplevel_page_add-functions-php' ||
				$current_screen->id === 'toplevel_page_sidebar-menu-customizer' ||
				strpos( $current_url, 'https://add-functions-php.seripoyo.work/wp-admin/admin.php?page=posts-function' ) !== false
				) {
					?>
				<div class="notice notice-info is-dismissible">
					<p>
						<strong><?php _e( 'A trial version is recommended for those who are interested in the PRO version!', 'add-functions-php' ); ?></strong><br>
						<?php _e( 'If you purchase the PRO version after using the trial version, the trial version can be tried for practically 0 yen.', 'add-functions-php' ); ?><br>
						<a href="https://add-functions-php.seripoyo.work/downloads/trial/" target="_blank"><?php _e( 'Click here for more information about the trial version.', 'add-functions-php' ); ?></a>
					</p>
				</div>
					<?php
				}
			}
		}
	}

	new Add_Functions_PHP_Lite_Notification();

}


/**
 * 関数：translate_AFP_LITE_text
 * 概要：プラグインのテキストを翻訳
 *
 * @param string - $translated_text: 翻訳後のテキスト
 * @param string - $text: 翻訳前のテキスト
 * @param string - $domain: テキストドメイン
 * @return string - 翻訳後のテキスト
 *
 * 詳細：プラグインのテキストドメインに応じて、指定されたテキストを翻訳。
 **/
function translate_AFP_LITE_text( $translated_text, $text, $domain ) {
	if ( $domain == 'add-functions-php' ) {
		switch ( $text ) {
			case 'A trial version is recommended for those who are interested in the PRO version!':
				$translated_text = 'PRO版が気になる方には体験版がおすすめ！';
				break;
			case 'If you purchase the PRO version after using the trial version, the trial version can be tried for practically 0 yen.':
				$translated_text = '体験版を利用した上でPRO版を購入する場合、体験版は実質0円でお試し可能です。';
				break;
			case 'Click here for more information about the trial version.':
				$translated_text = '体験版の詳細はこちら';
				break;
			case 'Side Menu Setting':
				$translated_text = 'サイドメニュー設定';
				break;
		}
	}
	return $translated_text;
}
add_filter( 'gettext', 'translate_AFP_LITE_text', 20, 3 );
