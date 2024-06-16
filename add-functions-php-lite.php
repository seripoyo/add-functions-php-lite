<?php

/**
 * Plugin Name
 *
 * @package           Add functions PHP Lite
 * @author            せりぽよ
 * @copyright         2024 せりぽよ
 * @license           GPL2 or later
 *
 * @wordpress-plugin
 * Plugin Name:       Add functions PHP Lite
 * Plugin URI: https://seripoyo.work/add-functions/
 * Description:       This is a Japanese plugin to customize WordPress without editing functions.php directly.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP: 7.0
 * Author:            せりぽよ
 * Author URI: https://seripoyo.work/
 * Text Domain:       add-functions-php
 * License:           GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 */

/**
 *  WordPressのコアファイル以外から直接このファイルにアクセスされた場合は実行を停止
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Add_function_PHP' ) ) {
	class Add_function_PHP {

		/**
		 * 関数：__construct
		 * 概要：クラスのコンストラクタ（初期化処理）
		 *
		 * 詳細：WordPressのバージョンをチェックし、5.9以上の場合のみ以下の処理を実行。
		 * - 翻訳ファイルを登録
		 * - プラグインのベースディレクトリ、サーバーパス、CSSパス、JSパスを定義
		 * - クラスファイルを読み込み
		 * - CSSとJSファイルを読み込み
		 * - PRO版の場合、アップデートファイルを読み込み
		 **/
		public function __construct() {
			// WordPressのバージョンをチェック
			global $wp_version;
			// 5.9以上でなければ処理を中断し、5.9以上でのみ動作することを保証する
			$is_wp59 = ( version_compare( $wp_version, '5.9', '>=' ) );

			if ( ! $is_wp59 ) {
				return;
			}

			// 翻訳ファイルを登録
			load_plugin_textdomain( 'add-functions-php', false, basename( __DIR__ ) . '/languages' );

			// ドメイン/wp-content/plugins/プラグインディレクトリ/ までのURL
			if ( ! defined( 'PLUGIN_PATH' ) ) {
				define( 'PLUGIN_PATH', plugin_dir_url( __FILE__ ) );
			}
			// サーバーからの/public_html/functions_demo/wp-content/plugins/プラグインディレクトリ までのパス

			if ( ! defined( 'SERVER_PATH' ) ) {
				define( 'SERVER_PATH', plugin_dir_path( __FILE__ ) );
			}
			if ( ! defined( 'SRC_PATH' ) ) {
				define( 'CSS_PATH', PLUGIN_PATH . 'css/' );
				define( 'SRC_PATH', PLUGIN_PATH . 'src/' );
				define( 'JS_PATH', SRC_PATH . 'js/' );
			}
			/**
			 *  Classファイルの読み込み
			 */

			if ( ! defined( 'CLASS_PATH' ) ) {
				// /public_html/functions_demo/wp-content/plugins/プラグインディレクトリ/classを取得
				define( 'CLASS_PATH', SERVER_PATH . 'class/' );
				require_once CLASS_PATH . 'class-functions.php';
				require_once CLASS_PATH . 'class-admin-menu.php';
				require_once CLASS_PATH . 'class-posts.php';
			}
			/**
			 * CSS等の読み込み
			 */
			if ( ! defined( 'ENQUEUE_ASSETS_LOADED' ) ) {
				require_once SERVER_PATH . 'inc/enqueue-assets.php';
				define( 'ENQUEUE_ASSETS_LOADED', true ); // ファイルが読み込まれたことを示すために定数を定義
			}
		}
	}

	add_action(
		'plugins_loaded',
		function () {
			new Add_function_PHP();
		}
	);
}
/**
 * 関数：add_functions_php_init
 * 概要：プラグインの初期化処理
 *
 * 詳細：以下のクラスのインスタンスを作成し、プラグインの各機能を初期化。
 * - AFP_Functions: 基本機能
 * - AFP_Side_Menu: サイドメニュー
 * - AFP_Posts: 投稿関連機能
 */
if ( ! function_exists( 'add_functions_php_init' ) ) {
	function add_functions_php_init() {
		new \Add_function_PHP\Functions\AFP_Functions();
		new \Add_function_PHP\Side_Menu\AFP_Side_Menu();
		new \Add_function_PHP\Posts\AFP_Posts();
	}
	add_action( 'plugins_loaded', 'add_functions_php_init' );
}
// プラグインのパスを定数として定義
if ( ! defined( 'ADD_FUNCTIONS_PHP_LITE_PATH' ) ) {
	define( 'ADD_FUNCTIONS_PHP_LITE_PATH', 'add-functions-php-lite/add-functions-php-lite.php' );
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
						<strong>PRO版が気になる方には体験版がおすすめ！</strong><br>
						体験版を利用した上でPRO版を購入する場合、体験版は実質0円でお試し可能です。<br>
						<a href="https://add-functions-php.seripoyo.work/downloads/trial/" target="_blank">体験版の詳細はこちら</a>
					</p>
				</div>
					<?php
				}
			}
		}
	}

	new Add_Functions_PHP_Lite_Notification();

}