<?php

namespace Add_function_PHP\Posts;

defined( 'ABSPATH' ) || exit;

/**
 * 定数定義 ( PRO版と2つ有効になった時にエラーにならないようにdefined )
 */
if ( ! defined( 'POSTS_MODULES_PATH' ) ) {
	define( 'POSTS_MODULES_PATH', SERVER_PATH . 'class/posts-modules/' );
}
require_once POSTS_MODULES_PATH . 'post-setting.php';
require_once POSTS_MODULES_PATH . 'extension.php';
require_once POSTS_MODULES_PATH . 'selected.php';

if ( ! class_exists( 'Add_function_PHP\Posts\AFP_Posts' ) ) {
	class AFP_Posts {

		private $posts_class_base_dir;
		private $posts_functions_list;

		private $posts_setting;
		private $posts_extensions;
		private $posts_selected;

		/**
		 * 関数：__construct
		 * 概要：クラスのオブジェクトが作成されたときに自動的に呼び出されるコンストラクタ
		 *
		 * 詳細：投稿関連の設定や拡張機能を初期化し、必要なアクションフックを追加
		 *
		 * @var string - $posts_class_base_dir: 投稿モジュールのベースディレクトリ
		 * @var object - $posts_setting: 投稿設定オブジェクト
		 * @var object - $posts_extensions: 投稿拡張機能オブジェクト
		 * @var object - $posts_selected: 選択された投稿機能オブジェクト
		 **/
		public function __construct() {
			$this->posts_class_base_dir = __DIR__ . '/../class/posts-modules/';

			$this->posts_setting    = new \Add_function_PHP\Posts\Posts_Setting();
			$this->posts_extensions = new \Add_function_PHP\Posts\Posts_Extensions();
			$this->posts_selected   = new \Add_function_PHP\Posts\Posts_Selected(
				$this->posts_class_base_dir,
				$this->posts_functions_list,
				$this->posts_setting,
				$this->posts_extensions
			);

			$this->include_posts_function_files();
			add_action( 'admin_menu', array( $this, 'add_custom_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'init', array( $this->posts_selected, 'apply_selected_posts_functions' ) );
			register_activation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );
			$this->include_posts_function_files();

			add_action( 'init', array( $this, 'debug_rewrite_rules' ) );
			add_filter( 'register_post_type_args', array( $this, 'debug_post_has_archive' ), 10, 2 );
			// アーカイブページ表示順を変更
			// change_posts_per_page関数を呼び出す

			$this->posts_functions_list = require $this->posts_class_base_dir . 'list.php';
			// $this->posts_functions_setting = new \Add_function_PHP\Posts\Posts_Setting();
			// $this->posts_functions_setting = new Posts_Setting();
			// オプションを取得
			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			foreach ( $post_types as $post_type ) {
				$options = get_option( 'posts_functions_options_' . $post_type->name, array() );
				// 投稿画面のタグ一覧をチェックボックスで表示
				if ( isset( $options['tag_checkbox'] ) && $options['tag_checkbox'] == '1' ) {
					add_action( 'init', array( $this->posts_extensions, 'change_post_tag_to_checkbox' ), 1 );
				}
				if ( isset( $options['remove_author_archive'] ) && $options['remove_author_archive'] == '1' ) {
					add_filter( 'author_rewrite_rules', array( $this->posts_setting, '__return_empty_array' ) );
					add_action( 'init', array( $this->posts_setting, 'disable_author_archive' ) );

				}
			}
		}
		/**
		 * 関数：include_posts_function_files
		 * 概要：必要な投稿機能ファイルをインクルードする
		 *
		 * 詳細：投稿モジュールのリストと選択された投稿機能ファイルを読み込む
		 **/
		private function include_posts_function_files() {
			require_once $this->posts_class_base_dir . 'list.php';
			require_once $this->posts_class_base_dir . 'selected.php';
			// require_once $this->posts_class_base_dir . 'extension.php';
			// require_once $this->posts_class_base_dir . 'setting.php';
		}
		/**
		 * 関数：get_posts_functions_list
		 * 概要：投稿機能のリストを取得する
		 *
		 * 詳細：投稿機能のリストを返す
		 *
		 * @return array - 投稿機能のリスト
		 **/
		public function get_posts_functions_list() {
			return $this->posts_functions_list;
		}

		/**
		 * 関数：add_custom_menu
		 * 概要：カスタムメニューを追加する
		 *
		 * 詳細：管理画面のサブメニューに新しいメニュー項目を追加
		 **/
		public function add_custom_menu() {
			// サブメニューページを追加
			add_submenu_page(
				'add-functions-php', // 親メニューのスラッグ
				__( 'Post-related extensions', 'add-functions-php' ), // ページタイトル
				__( 'Post-related extensions', 'add-functions-php' ), // メニュータイトル
				'manage_options', // 必要な権限
				'posts-function', // このサブメニューページのスラッグ
				array( $this, 'render_posts_settings_page' ), // 表示するページのコールバック関数,
				3
			);
		}

		/**
		 * 関数：render_posts_settings_page
		 * 概要：設定ページの内容を表示する
		 *
		 * 詳細：設定ページのテンプレートを読み込み、必要な設定を適用
		 **/
		public function render_posts_settings_page() {
			include_once SERVER_PATH . 'inc/page-posts.php';
		}

		/**
		 * 関数：register_settings
		 * 概要：設定の登録を行う
		 *
		 * 詳細：投稿機能の設定をWordPressに登録し、サニタイズコールバックを設定
		 **/
		public function register_settings() {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			register_setting( 'posts_functions_options', 'posts_functions_options', array( $this, 'sanitize_settings' ) );

			foreach ( $post_types as $post_type ) {
				register_setting(
					'posts_functions_options',
					'posts_functions_options_' . $post_type->name,
					array(
						'sanitize_callback' => array( $this, 'sanitize_options' ),
					)
				);
			}
		}
		/**
		 * 関数：get_option
		 * 概要：指定されたオプションを取得する
		 *
		 * 詳細：指定されたキーのオプションを取得し、存在しない場合はデフォルト値を返す
		 *
		 * @param string - $post_type: 投稿タイプ
		 * @param string - $key: オプションのキー
		 * @param string - $default: デフォルト値
		 * @return mixed - オプションの値
		 **/
		public function get_option( $post_type, $key, $default = '' ) {
			$options = get_option( 'posts_functions_options_' . $post_type, array() );
			return isset( $options[ $key ] ) ? $options[ $key ] : $default;
		}
		/**
		 * 関数：extension_get_option
		 * 概要：拡張機能のオプションを取得する
		 *
		 * 詳細：指定されたキーの拡張機能オプションを取得し、存在しない場合はデフォルト値を返す
		 *
		 * @param string - $key: オプションのキー
		 * @param string - $default: デフォルト値
		 * @return mixed - オプションの値
		 **/
		public function extension_get_option( $key, $default = '' ) {
			$ex_options = get_option( 'posts_functions_options_' );
			return isset( $ex_options[ $key ] ) ? $ex_options[ $key ] : $default;
		}
		/**
		 * 関数：sanitize_options
		 * 概要：オプション値をサニタイズする
		 *
		 * 詳細：各オプション値を適切な形式にサニタイズし、サニタイズ後の値を返す
		 *
		 * @param array - $input: サニタイズするオプション値
		 * @return array - サニタイズされたオプション値
		 **/
		public function sanitize_options( $input ) {
			// デバッグ: サニタイズ前の値をログに記録
			// error_log( 'サニタイズ前の入力: ' . print_r( $input, true ) );

			if ( ! is_array( $input ) ) {
				// error_log( '入力が配列ではありません。' );
				return array();
			}

			$sanitized_input = array();

			// current_tabの値をチェック
			if ( isset( $input['current_tab'] ) ) {
				$current_tab = sanitize_text_field( $input['current_tab'] );
				// error_log( 'Current Tabが取得できました: ' . $current_tab );
				// リダイレクトURLをオプションとして保存
				update_option( 'add_functions_php_redirect_url', admin_url( 'admin.php?page=posts-function#' . $current_tab ) );
			} else {
				// error_log( 'Current Tabは取得できませんでした。' );
			}

			foreach ( $this->posts_functions_list as $key => $label ) {
				if ( isset( $input[ $key ] ) ) {
					$sanitized_input[ $key ] = sanitize_text_field( $input[ $key ] );
				}
			}

			foreach ( $input as $key => $value ) {
				if ( array_key_exists( $key, $this->posts_functions_list ) ) {
					$sanitized_input[ $key ] = isset( $value ) ? '1' : '0';
				} elseif ( is_array( $value ) ) {
					$sanitized_input[ $key ] = array_map( 'sanitize_text_field', $value );
				} elseif ( $key === 'archive_description' ) {
					$sanitized_input[ $key ] = sanitize_textarea_field( $value );
				} else {
					$sanitized_input[ $key ] = sanitize_text_field( $value );
				}
			}

			// error_log( 'サニタイズ後の入力: ' . print_r( $sanitized_input, true ) );

			return $sanitized_input;
		}



		/**
		 * 関数：set_post_revisions_by_type
		 * 概要：投稿タイプごとのリビジョン数上限を設定する
		 *
		 * 詳細：指定された投稿タイプに対してリビジョン数の上限を設定
		 *
		 * @param int -    $revisions: リビジョン数
		 * @param object - $post: 投稿オブジェクト
		 * @return int - 設定されたリビジョン数
		 **/
		public function set_post_revisions_by_type( $revisions, $post ) {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			foreach ( $post_types as $post_type ) {
				$selected_options = get_option( 'posts_functions_options_' . $post_type->name, array() );

				if ( isset( $selected_options['revisions_number'] ) && $post->post_type === $post_type->name ) {
					$revisions_number = intval( $selected_options['revisions_number'] );
					if ( $revisions_number >= 0 ) {
						$revisions = $revisions_number;
					}
				}
			}

			return $revisions;
		}
		/**
		 * 関数：set_post_revisions_limit
		 * 概要：リビジョン数の上限を設定する
		 *
		 * 詳細：指定された投稿タイプに対してリビジョン数の上限を設定
		 *
		 * @param int -    $num: デフォルトのリビジョン数
		 * @param object - $post: 投稿オブジェクト
		 * @return int - 設定されたリビジョン数
		 **/
		public function set_post_revisions_limit( $num, $post ) {
			$post_type        = $post->post_type;
			$selected_options = get_option( 'posts_functions_options_' . $post_type, array() );

			// リビジョン数の上限が設定されている場合は、その値を使用する
			if ( isset( $selected_options['revisions_number'] ) ) {
				$limit = intval( $selected_options['revisions_number'] );
				// 上限数が0以上の場合のみ、上限を設定する
				if ( $limit >= 0 ) {
					return $limit;
				}
			}

			// デフォルトのリビジョン数を返す
			return $num;
		}
		/**
		 * 関数：debug_rewrite_rules
		 * 概要：リライトルールをデバッグする
		 *
		 * 詳細：現在のリライトルールをログに記録
		 **/
		public function debug_rewrite_rules() {
			global $wp_rewrite;
			// error_log("デバッグ: リライトルール");
			// error_log(print_r($wp_rewrite->rules, true));
		}
		/**
		 * 関数：debug_post_has_archive
		 * 概要：投稿タイプのアーカイブ設定をデバッグする
		 *
		 * 詳細：指定された投稿タイプのアーカイブ設定をログに記録
		 *
		 * @param array -  $args: 投稿タイプの引数
		 * @param string - $post_type: 投稿タイプのスラッグ
		 * @return array - 投稿タイプの引数
		 **/
		public function debug_post_has_archive( $args, $post_type ) {
			if ( 'post' == $post_type ) {
				// error_log("デバッグ: post_has_archive");
				// error_log(print_r($args, true));
			}
			return $args;
		}
		/**
		 * 関数：flush_rewrite_rules
		 * 概要：リライトルールをフラッシュする
		 *
		 * 詳細：リライトルールを再生成して保存
		 **/
		public function flush_rewrite_rules() {
			flush_rewrite_rules();
		}
	}
}
