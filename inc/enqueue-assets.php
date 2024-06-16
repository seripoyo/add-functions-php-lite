<?php
namespace Add_function_PHP\Enqueue;

defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'AFP_Plugin_Enqueue' ) ) {
	class AFP_Plugin_Enqueue {
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue_scripts' ) );
			add_action( 'init', array( $this, 'translation_load_textdomain' ) );
			add_action( 'wp_head', array( $this, 'add_custom_css' ) );
			add_action( 'admin_head', array( $this, 'add_custom_css' ) );
			add_action( 'admin_footer', array( $this, 'admin_footer_script' ) );
			add_filter( 'the_content', array( $this, 'add_custom_link_after_legend' ) );
			add_action( 'admin_head', array( $this, 'preload' ) );
			add_action( 'admin_bar_menu', array( $this, 'add_clear_cache_button' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_clear_cache_script' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_clear_cache_script' ) );
			add_action( 'wp_ajax_clear_cache', array( $this, 'clear_cache_ajax_handler' ) );
		}
		/**
		 * 関数：enqueue_assets
		 * 概要：管理画面でのCSSとJavaScriptファイルを読み込む
		 *
		 * @param string - $hook: 現在のページのフック名
		 *
		 * 詳細：管理画面で必要なCSSファイルとJavaScriptファイルを読み込む。また、Google Fontsも読み込む。
		 **/
		public function enqueue_assets( $hook ) {
			// CSSファイルのエンキュー
			wp_enqueue_style( 'add-functions-php-style', CSS_PATH . 'style.css' );
			wp_enqueue_style( 'add-functions-php-icons', CSS_PATH . 'icon.css' );
			wp_enqueue_style( 'alpha-color-picker', CSS_PATH . 'alpha-color-picker.min.css', array( 'wp-color-picker' ) );

			// グーグルフォント
			wp_enqueue_style( 'add-functions-php-mplus1p', 'https://fonts.googleapis.com/css?family=M+PLUS+1p' );

			// JavaScriptファイルのエンキュー
			wp_enqueue_script( 'add-functions-php-src', JS_PATH . 'admin.js', array(), false, true );
			wp_enqueue_script( 'icon_picker', JS_PATH . 'icon-picker.js', array(), false, true );
			wp_enqueue_script( 'lazysizes', 'lazysizes.min.js', array(), false, true );
			wp_enqueue_script( 'unveilhooks', 'ls.unveilhooks.min.js', array(), false, true );
			wp_enqueue_script( 'repeat_field', JS_PATH . 'repeat-field.js', array( 'wp-i18n', 'jquery' ), false, true );
			wp_enqueue_script( 'widgets_icon_picker', JS_PATH . 'widgets-icon-picker.js', array(), false, true );
			wp_enqueue_script( 'seo', JS_PATH . 'seo.js', array(), false, true );
			wp_enqueue_script( 'img', JS_PATH . 'img-uploader.js', array(), false, true );
			wp_enqueue_script( 'cookie', 'http://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.3.1/jquery.cookie.min.js' );
			wp_enqueue_script( 'keep', JS_PATH . 'jquery.keep-position.js', array(), false, true );
			wp_enqueue_script( 'alpha-color-picker-js', JS_PATH . 'alpha-color-picker.min.js', array( 'jquery', 'wp-color-picker' ), null, true );
			wp_localize_script( 'custom-admin-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
		/**
		 * 関数：front_enqueue_scripts
		 * 概要：フロントエンドでのCSSファイルを読み込む
		 *
		 * 詳細：フロントエンドで必要なCSSファイルを読み込む。
		 **/
		public function front_enqueue_scripts() {
			wp_enqueue_style( 'add-functions-php-icon', CSS_PATH . 'icon.min.css' );
		}
		/**
		 * 関数：translation_load_textdomain
		 * 概要：翻訳ファイルを読み込む
		 *
		 * 詳細：プラグインの翻訳ファイルを読み込む。
		 **/
		public function translation_load_textdomain() {
			load_plugin_textdomain( 'add-functions-php', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
		}
		/**
		 * 関数：add_custom_css
		 * 概要：カスタムCSSを出力する
		 *
		 * 詳細：プラグインで使用するカスタムフォントとその他のカスタムスタイルを出力する。
		 **/
		public function add_custom_css() {
			$site_url = esc_url( home_url() );
			?>
		<style>
			@font-face {
				font-family: "icon-fonts";
				font-style: normal;
				font-weight: normal;
				font-display: block;
				src: url("<?php echo PLUGIN_PATH; ?>assets/icon_fonts/icon-fonts.woff2") format("woff2"),
					url("<?php echo PLUGIN_PATH; ?>assets/icon_fonts/icon-fonts.woff") format("woff"),
					url("<?php echo PLUGIN_PATH; ?>assets/icon_fonts/icon-fonts.svg") format("svg");
			}
			/* その他のカスタムスタイルをここに追加 */
		</style>
			<?php
		}
		/**
		 * 関数：admin_footer_script
		 * 概要：管理画面のフッターにJavaScriptを出力する
		 *
		 * 詳細：管理画面のフッターにリダイレクト用のJavaScriptを出力する。リダイレクト先のURLはオプションから取得する。
		 **/
		public function admin_footer_script() {
			$redirect_url = get_option( 'add_functions_php_redirect_url', '' );
			if ( ! empty( $redirect_url ) ) {
				delete_option( 'add_functions_php_redirect_url' );
				echo "<script>window.location.href = '{$redirect_url}';</script>";
			}
		}
		/**
		 * 関数：add_custom_link_after_legend
		 * 概要：特定の要素の後にカスタムリンクを追加する
		 *
		 * @param string - $content: 元のコンテンツ
		 * @return string - 変更後のコンテンツ
		 *
		 * 詳細：特定の要素の後にカスタムリンクを追加し、変更後のコンテンツを返す。
		 **/
		public function add_custom_link_after_legend( $content ) {
			$target      = '<legend>Register For a New Account</legend>';
			$link        = '<a href="https://add-functions-php.seripoyo.work/wp-login.php?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">ログインまたは登録はこちら</a>';
			$new_content = str_replace( $target, $target . $link, $content );
			return $new_content;
		}
		/**
		 * 関数：preload
		 * 概要：プラグインで使用する画像をプリロードする
		 *
		 * 詳細：管理画面でのみ、プラグインで使用する画像をプリロードし、CSSでも参照できるようにする。
		 **/
		public function preload() {
			if ( is_admin() ) {
				echo '<link rel="preload" href="' . PLUGIN_PATH . 'assets/img/sep.webp" as="image">';
				echo '<style type="text/css">';
				echo '.toggle .toggle__slider::before { background-image: url( ' . PLUGIN_PATH . 'assets/img/sep.webp); }';
				echo '</style>';
			}
		}
		/**
		 * 関数：add_clear_cache_button
		 * 概要：管理バーにキャッシュクリアボタンを追加する
		 *
		 * @param object - $wp_admin_bar: 管理バーオブジェクト
		 *
		 * 詳細：管理者権限を持つユーザーに対して、管理バーにキャッシュクリアボタンを追加する。
		 **/
		public function add_clear_cache_button( $wp_admin_bar ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$args = array(
				'id'    => 'clear-cache',
				'title' => __( 'Delete cache', 'add-functions-php' ),
				'href'  => '#',
				'meta'  => array( 'class' => 'clear-cache-button' ),
			);
			$wp_admin_bar->add_node( $args );
		}
		/**
		 * 関数：enqueue_clear_cache_script
		 * 概要：キャッシュクリア用のJavaScriptを読み込む
		 *
		 * 詳細：管理者権限を持つユーザーに対して、キャッシュクリア用のJavaScriptを読み込む。
		 **/
		public function enqueue_clear_cache_script() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			wp_enqueue_script( 'clear-cache-script', JS_PATH . 'clear-cache.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				'clear-cache-script',
				'clearCacheAjax',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'clear-cache-nonce' ),
				)
			);
		}
		/**
		 * 関数：clear_cache_ajax_handler
		 * 概要：キャッシュクリアのAjax処理を行う
		 *
		 * 詳細：管理者権限を持つユーザーからのAjaxリクエストに対して、キャッシュをクリアし、結果をJSON形式で返す。
		 **/
		public function clear_cache_ajax_handler() {
			if ( ! current_user_can( 'manage_options' ) || ! check_ajax_referer( 'clear-cache-nonce', 'nonce', false ) ) {
				wp_send_json_error( 'Unauthorized' );
			}

			wp_cache_flush();

			header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
			header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
			header( 'Pragma: no-cache' );

			wp_send_json_success( __( 'Cache deletion succeeded! Reload the screen.', 'add-functions-php' ) );
		}
	}
}
new AFP_Plugin_Enqueue();

