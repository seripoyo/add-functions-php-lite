<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

/**
 * 定数定義 ( PRO版と2つ有効になった時にエラーにならないようにdefined )
 */
if ( ! defined( 'FUNCTIONS_MODULES_PATH' ) ) {
	define( 'FUNCTIONS_MODULES_PATH', SERVER_PATH . 'class/functions-modules/' );
}

require_once FUNCTIONS_MODULES_PATH . 'appearance.php';
require_once FUNCTIONS_MODULES_PATH . 'extensions.php';
require_once FUNCTIONS_MODULES_PATH . 'setting_htaccess.php';
require_once FUNCTIONS_MODULES_PATH . 'ogp.php';
require_once FUNCTIONS_MODULES_PATH . 'head.php';
require_once FUNCTIONS_MODULES_PATH . 'security.php';
require_once FUNCTIONS_MODULES_PATH . 'speed-up.php';

if ( ! class_exists( 'Add_function_PHP\Functions\AFP_Functions' ) ) {
	class AFP_Functions {

		private $functions_class_base_dir;
		private $functions_list;
		private $appearance;
		private $option_name = 'add_functions_php_settings';
		private $functions_extensions;
		private $functions_ogp;
		private $functions_head;
		private $functions_media;
		private $functions_security;
		private $functions_speed;


		/**
		 * 関数：show_settings_page
		 * 概要：設定ページの内容を表示する
		 *
		 * 詳細：管理者権限を確認し、POSTデータを処理して設定を保存
		 **/
		public function show_settings_page() {
			$options = get_option( 'add_functions_php_settings' );
			require_once SERVER_PATH . 'inc/page-functions.php';
		}

		/**
		 * 関数：__construct
		 * 概要：クラスのオブジェクトが作成されたときに自動的に呼び出されるコンストラクタ
		 *
		 * 詳細：各種機能モジュールを初期化し、必要なアクションフックを追加
		 *
		 * @var string - $functions_class_base_dir: 機能モジュールのベースディレクトリ
		 * @var array - $functions_list: 機能のリスト
		 * @var object - $appearance: 管理画面の外観設定オブジェクト
		 * @var string - $option_name: オプション名
		 * @var object - $functions_extensions: 拡張機能オブジェクト
		 * @var object - $functions_htaccess: htaccess設定オブジェクト
		 * @var object - $functions_ogp: OGP設定オブジェクト
		 * @var object - $functions_head: headタグ設定オブジェクト
		 * @var object - $functions_security: セキュリティ設定オブジェクト
		 * @var object - $functions_speed: スピードアップ設定オブジェクト
		 * @var object - $functions_widget: ウィジェット設定オブジェクト
		 **/
		public function __construct() {
			$this->functions_class_base_dir = __DIR__ . '/../class/functions-modules/';
			$this->include_function_files();
			$this->functions_list = require $this->functions_class_base_dir . 'list.php';

			$this->appearance           = new \Add_function_PHP\Functions\Functions_Admin_Color( $this );
			$this->functions_extensions = new \Add_function_PHP\Functions\Functions_Extensions();
			$this->functions_ogp        = new \Add_function_PHP\Functions\Functions_ogp(); // インスタンスを作成
			$this->functions_head       = new \Add_function_PHP\Functions\Functions_head(); // インスタンスを作成
			$this->functions_security   = new \Add_function_PHP\Functions\Functions_Security(); // インスタンスを作成
			$this->functions_speed      = new \Add_function_PHP\Functions\Functions_Speed(); // インスタンスを作成

			$options = get_option( 'add_functions_php_settings' );

			add_action( 'admin_menu', array( $this, 'set_plugin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'init', array( $this, 'apply_selected_functions' ) );
			// add_action( 'admin_notices', array( $this->functions_security, 'debug_htaccess' ) );

			// 投稿画面のタグ一覧をチェックボックスで表示
			if ( isset( $options['remove_author_archive'] ) && $options['remove_author_archive'] == '1' ) {
				add_action( 'init', array( $this->functions_security, 'disable_author_archive' ) );
				add_filter( 'author_rewrite_rules', '__return_empty_array' );

			}
		}


		/**
		 * 関数：apply_selected_functions
		 * 概要：選択された関数を適用する
		 *
		 * 詳細：選択された関数を含むファイルを読み込む
		 **/
		private function include_function_files() {
			require_once $this->functions_class_base_dir . 'list.php';
		}

		/**
		 * 関数：get_functions_list
		 * 概要：関数のリストを取得する
		 *
		 * 詳細：関数のリストを返す
		 *
		 * @return array - 関数のリスト
		 **/
		public function apply_selected_functions() {
			// $options =get_option( 'add_functions_php_settings' );
			require_once $this->functions_class_base_dir . 'selected.php';
		}

		/**
		 * 関数：get_functions_list
		 * 概要：機能のリストを取得する
		 *
		 * 詳細：機能のリストを返す
		 *
		 * @return array - 機能のリスト
		 **/
		public function get_functions_list() {
			return $this->functions_list;
		}
		/**
		 * 関数：set_plugin_menu
		 * 概要：プラグインのメニューを設定する
		 *
		 * 詳細：管理画面のメニューにプラグインのトップメニューとサブメニューを追加
		 **/
		public function set_plugin_menu() {
			$top_menu_title = 'Add functions PHP';
			$top_menu_slug  = 'add-functions-php';

			add_menu_page(
				__( 'Add functions PHP', 'add-functions-php' ), // ページタイトル
				__( 'Add functions PHP', 'add-functions-php' ), // メニュータイトル
				'manage_options', // アクセス権限レベル
				$top_menu_slug,
				array( $this, 'show_settings_page' ),
				'dashicons-admin-tools',  // アイコン(SVGのURLまたはダッシュアイコンのクラス)
				99 // サイドバーメニュー内の表示位置
			);
			// サブメニュー
			add_submenu_page(
				$top_menu_slug, // 親メニューのスラッグ
				__( 'Extensions', 'add-functions-php' ), // ページタイトル
				__( 'Extensions', 'add-functions-php' ), // メニュータイトル
				'manage_options', // アクセス権限レベル
				$top_menu_slug, // 親メニューのスラッグと同じにする
				array( $this, 'show_settings_page' ),
				1
			);
		}

		/**
		 * 関数：register_settings
		 * 概要：設定の登録を行う
		 *
		 * 詳細：設定フィールドを登録し、サニタイズコールバックを設定
		 **/
		public function register_settings() {
			// 正しいオプション名で設定を登録
			// error_log( 'register_settings メソッドが呼び出されました。' );

			// オプションページの登録
			$option_group      = 'add_functions_php_settings';
			$option_name       = 'add_functions_php_settings';
			$sanitize_callback = array( $this, 'sanitize_settings' );

			// オプションページが登録されているかチェック
			// if ( get_option( $option_name ) !== false ) {
			// error_log( 'オプションページ ' . $option_name . ' は既に登録されています。' );
			// } else {
			// error_log( 'オプションページ ' . $option_name . ' は登録されていません。' );
			// }

			// register_setting の結果をチェック
			$registration_result = register_setting( $option_group, $option_name, $sanitize_callback );
			// if ( $registration_result ) {
			// error_log( 'オプションページ ' . $option_name . ' の登録に成功しました。' );
			// } else {
			// error_log( 'オプションページ ' . $option_name . ' の登録に失敗しました。' );
			// }
			register_setting( 'add_functions_php_settings', 'add_functions_php_settings', array( $this, 'sanitize_settings' ) );

			// register_setting( 'admin_custom_settings', 'admin_custom_settings', array( $this, 'sanitize_settings' ) );
			add_settings_field( 'admin_bg_color', __( 'Admin Panel Background Color', 'add-functions-php' ), array( $this->appearance, 'render_admin_bg_color_field' ), 'admin_custom', 'admin_custom_section' );
			add_settings_field( 'admin_text_color', __( 'Admin Panel Text Color', 'add-functions-php' ), array( $this->appearance, 'render_admin_text_color_field' ), 'admin_custom', 'admin_custom_section' );
			add_settings_field( 'admin_menu_bg_color', __( 'Admin Panel Menu Background Color', 'add-functions-php' ), array( $this->appearance, 'render_admin_menu_bg_color_field' ), 'admin_custom', 'admin_custom_section' );
			add_settings_field( 'admin_submenu_bg_color', __( 'Admin Panel Submenu Background Color', 'add-functions-php' ), array( $this->appearance, 'render_admin_submenu_bg_color_field' ), 'admin_custom', 'admin_custom_section' );
			add_settings_field( 'admin_menu_text_color', __( 'Admin Panel Menu Text Color', 'add-functions-php' ), array( $this->appearance, 'render_admin_menu_text_color_field' ), 'admin_custom', 'admin_custom_section' );
			register_setting( $this->option_name, $this->option_name, array( $this, 'sanitize_settings' ) );
			// error_log( '設定が登録されました。' );
		}
		/**
		 * 関数：render_example_setting
		 * 概要：例示設定フィールドを表示する
		 *
		 * 詳細：設定オプションを取得し、入力フィールドを表示
		 **/
		public function render_example_setting() {
			$options = get_option( ( 'add_functions_php_settings' ) );
			echo '<input type="text" name="add_functions_php_settings[example_setting]" value="' . esc_attr( $options['example_setting'] ?? '' ) . '"/>';
		}
		/**
		 * 関数：get_option
		 * 概要：指定されたオプションを取得する
		 *
		 * 詳細：指定されたキーのオプションを取得し、存在しない場合はデフォルト値を返す
		 *
		 * @param string - $key: オプションのキー
		 * @param string - $default: デフォルト値
		 * @return mixed - オプションの値
		 **/
		public function get_option( $key, $default = '' ) {
			$options = get_option( 'add_functions_php_settings' );
			return isset( $options[ $key ] ) ? $options[ $key ] : $default;
		}
		/**
		 * 関数：sanitize_settings
		 * 概要：設定値をサニタイズする
		 *
		 * 詳細：各設定値を適切な形式にサニタイズし、サニタイズ後の値を返す
		 *
		 * @param array - $input: サニタイズする設定値
		 * @return array - サニタイズされた設定値
		 **/
		public function sanitize_settings( $input ) {
			// デバッグ: サニタイズ前の値をログに記録
			// error_log( 'サニタイズ前の入力: ' . print_r( $input, true ) );
			$new_input = array();
			foreach ( $this->functions_list as $key => $label ) {
				if ( isset( $input[ $key ] ) ) {
					$new_input[ $key ] = sanitize_text_field( $input[ $key ] );
				}
			}
			// デバッグ: 各入力値が存在するかチェック
			// error_log( 'current_tab: ' . ( isset( $input['current_tab'] ) ? 'exists' : 'does not exist' ) );

			// カスタムサニタイズ関数を使用してHTMLタグを許可
			$allowed_html = array(
				'link'   => array(
					'rel'   => array(),
					'href'  => array(),
					'type'  => array(),
					'media' => array(),
				),
				'script' => array(
					'src'   => array(),
					'type'  => array(),
					'async' => array(),
					'defer' => array(),
				),
			);

			$new_input['add_head']   = isset( $input['add_head'] ) ? wp_kses( $input['add_head'], $allowed_html ) : '';
			$new_input['body_start'] = isset( $input['body_start'] ) ? wp_kses( $input['body_start'], $allowed_html ) : '';
			$new_input['body_after'] = isset( $input['body_after'] ) ? wp_kses( $input['body_after'], $allowed_html ) : '';

			// アップロードをする実際の場所を指定するupload_path
			if ( isset( $input['upload_path'] ) ) {
				$new_input['upload_path'] = sanitize_text_field( $input['upload_path'] );
			}
			// アップした画像を表示させるURLを指定するupload_url_path
			if ( isset( $input['upload_url_path'] ) ) {
				$new_input['upload_url_path'] = sanitize_text_field( $input['upload_url_path'] );
			}



			// twitter_id
			if ( isset( $input['twitter_id'] ) ) {
				$new_input['twitter_id'] = sanitize_text_field( $input['twitter_id'] );
			}
			if ( isset( $_POST['submit'] ) ) {
				// アップロードされた画像を保存・サニタライズ処理し、OGP画像として適用
				if ( isset( $input['ogp_image'] ) ) {
					$new_input['ogp_image'] = esc_url_raw( $input['ogp_image'] );
					// error_log('OGP画像が正常に保存・サニタライズされました。');
					// error_log('保存された画像のURL: ' . $new_input['ogp_image']);
				}
			}

			// Twitter Card Typeのサニタイズ
			$valid_types = array(
				'SummaryLarge' => 'summary_large_image',
				'summary'      => 'summary',
			);
			if (
			isset( $input['twitter_card_type'] ) && array_key_exists( $input['twitter_card_type'], $valid_types )
			) {
				$new_input['twitter_card_type'] = $valid_types[ $input['twitter_card_type'] ];
			} else {
				$new_input['twitter_card_type'] = 'summary_large_image'; // デフォルト値
			}

			// デバッグ: サニタイズ後の値をログに記録
			// error_log('Sanitized input: ' . print_r($new_input, true));


			// 管理画面フォントのサニタイズ処理
			// error_log('受け取った入力: ' . print_r($input, true));

			// 管理画面フォントのデバッグ
			if ( isset( $input['admin_font'] ) ) {
				// error_log( 'admin_fontが存在します。値: ' . $input['admin_font'] );
				if ( array_key_exists( $input['admin_font'], $this->appearance->get_font_options() ) ) {
					// error_log( 'admin_fontの値は有効な選択肢です。' );
					$new_input['admin_font'] = $input['admin_font'];
				} else {
					// error_log( 'admin_fontの値は無効な選択肢です。デフォルト値に設定します。' );
					$new_input['admin_font'] = 'default';
				}
			} else {
				// error_log( 'admin_fontが存在しません。デフォルト値に設定します。' );
				$new_input['admin_font'] = 'default';
			}

			// 管理画面背景色
			if ( isset( $input['admin_bg_color'] ) ) {
				$new_input['admin_bg_color'] = $this->appearance->sanitize_color( $input['admin_bg_color'] );
			}

			// 管理画面テキスト色
			if ( isset( $input['admin_text_color'] ) ) {
				$new_input['admin_text_color'] = $this->appearance->sanitize_color( $input['admin_text_color'] );
			}

			// 管理画面メニュー背景色
			if ( isset( $input['admin_menu_bg_color'] ) ) {
				$new_input['admin_menu_bg_color'] = $this->appearance->sanitize_color( $input['admin_menu_bg_color'] );
			}

			// 管理画面メニューテキスト色
			if ( isset( $input['admin_menu_text_color'] ) ) {
				$new_input['admin_menu_text_color'] = $this->appearance->sanitize_color( $input['admin_menu_text_color'] );
			}
			// デバッグ: サニタイズ後の値をログに記録

			// options.php内の設定保存処理後にリダイレクトを行う部分
			if ( isset( $input['current_tab'] ) ) {
				$current_tab = sanitize_text_field( $input['current_tab'] );
				// リダイレクトURLをオプションとして保存
				update_option( 'add_functions_php_redirect_url', admin_url( 'admin.php?page=add-functions-php#' . $current_tab ) );
			} else {
				// error_log( 'Current Tabは取得出来ませんでした ' ); // デバッグログ
			}
			// error_log( 'サニタイズ後の入力: ' . print_r( $new_input, true ) );

			return $new_input;
		}

		/**
		 * 関数：output_admin_custom_page
		 * 概要：管理画面のカスタムページを出力する
		 *
		 * 詳細：カスタムページのテンプレートを読み込み、必要な設定を適用
		 **/
		public function output_admin_custom_page() {
			if ( ! isset( $this->output_admin_custom_page_called ) ) {
				$this->output_admin_custom_page_called = true;
				$admin_font                            = $this->get_option( 'admin_font' );
				// $dashboard_columns                     = $this->get_option( 'dashboard_columns' );

				// 保存されたチェック状況を取得
				$adminbar_settings = $this->get_option( 'adminbar_settings' );
				// error_log( '保存されたチェック状況: ' . print_r( $adminbar_settings, true ) );

				require_once SERVER_PATH . 'inc/page_functions.php';
			}
		}
	}
}
