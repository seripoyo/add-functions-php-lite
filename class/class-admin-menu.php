<?php

namespace Add_function_PHP\Side_Menu;

defined( 'ABSPATH' ) || exit;
// デバッグメッセージを追加

// WordPressのサイドバーメニューをカスタマイズするクラス
if ( ! class_exists( 'Add_function_PHP\Side_Menu\AFP_Side_Menu' ) ) {
	class AFP_Side_Menu {

		private $menu_slug    = 'sidebar-menu-customizer'; // メニューのスラッグ
		private $options_name = 'sidebar_menu_hidden_options'; // オプション名
		private $labels; // メニュー項目のラベルを格納する変数

		private static $filter_applied = false;


/**
 * 関数：__construct
 * 概要：クラスのオブジェクトが作成されたときに自動的に呼び出されるコンストラクタ
 *
 * 詳細：カスタムメニューの追加、設定の登録、サイドバーメニューのカスタマイズを行うアクションフックを追加
 *
 * @var string - $menu_slug: メニューのスラッグ
 * @var string - $options_name: オプション名
 * @var array - $labels: メニュー項目のラベル
 * @var bool - $filter_applied: フィルターが適用されたかどうか
 **/
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_custom_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'remove_sidebar_menus' ), 999 );
			add_action( 'admin_menu', array( $this, 'customize_admin_menu_label' ), 9999 ); // ここでカスタマイズ関数を追加
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
				__( 'Admin Menu Adjustment', 'add-functions-php' ), // ページタイトル
				__( 'Admin Menu Adjustment', 'add-functions-php' ), // メニュータイトル
				'manage_options', // 必要な権限
				$this->menu_slug, // メニューのスラッグ
				array( $this, 'settings_page' ), // 設定ページのコールバック関数
				2
			);
		}


		/**
		 * 関数：settings_page
		 * 概要：設定ページの内容を表示する
		 *
		 * 詳細：管理者権限を確認し、POSTデータを処理して設定を保存
		 **/
		public function settings_page() {
			// error_log('POST Data: ' . print_r($_POST, true));
			// 管理者権限を持っていなければアクセス禁止
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have permission to access this page.', 'add-functions-php' ) );
			}
			// アイコン情報のデバッグ
			if ( isset( $_POST['custom_menu_icons'] ) ) {
				// error_log( 'Custom Menu Icons POST Data: ' . print_r( $_POST['custom_menu_icons'], true ) );
			}
			// 保存された新しいメニュー名を取得
			$custom_menu_names = get_option( 'custom_menu_names', array() );

			// サイドバーのメニュー項目を取得
			$this->labels = $this->get_sidebar_menu_items();

			// $this->labelsがnullの場合、空の配列を割り当てる
			if ( ! is_array( $this->labels ) ) {
				$this->labels = array();
			}

			foreach ( $this->labels as $id => $label ) {
				$value = isset( $custom_menu_names[ $id ] ) ? esc_attr( $custom_menu_names[ $id ] ) : '';
			}

			// POSTリクエストが送信された場合の処理
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
				// POSTデータのデバッグ情報を出力
				// error_log( 'POST Data: ' . print_r( $_POST, true ) );

				// 新しいメニュー名の保存
				if ( isset( $_POST['custom_menu_names'] ) ) {
					$sanitized_menu_names = array_map( 'sanitize_text_field', $_POST['custom_menu_names'] );
					update_option( 'custom_menu_names', $sanitized_menu_names );
				}

				if ( isset( $_POST['custom_menu_icons'] ) ) {
					$sanitized_menu_icons = array_map( 'sanitize_text_field', $_POST['custom_menu_icons'] );
					update_option(
						'custom_menu_icons',
						$sanitized_menu_icons
					);
				}

				// 新しいメニューの表示/非表示設定の保存
				if ( isset( $_POST['sidebar_menu_hidden_options'] ) ) {
					$sanitized_hidden_options = self::sanitize_selected_options( $_POST['sidebar_menu_hidden_options'] );
					update_option( 'sidebar_menu_hidden_options', $sanitized_hidden_options );
				}
			}

			// 隠すメニュー項目のリストを取得
			$hidden_menus = get_option( $this->options_name, array() );
			// サイドバーのメニュー項目を取得
			$this->labels = $this->get_sidebar_menu_items();
			// 設定ページのテンプレートを読み込み
			include_once SERVER_PATH . 'inc/page-admin-menu.php';

			// 保存された新しいメニュー名を取得
			$custom_menu_names = get_option( 'custom_menu_names', array() );
		}

		/**
		 * 関数：register_settings
		 * 概要：設定の登録を行う
		 *
		 * 詳細：カスタムメニューの設定をWordPressに登録
		 **/
		public function register_settings() {
			// register_setting( 'sidebar-menu-customizer-settings', $this->options_name, array( 'sanitize_callback' => array( $this, 'sanitize_selected_options' ) ) );
			register_setting( 'sidebar-menu-customizer-settings', 'sidebar_menu_hidden_options', array( 'sanitize_callback' => array( 'Add_function_PHP\Side_Menu\AFP_Side_Menu', 'sanitize_selected_options' ) ) );
			register_setting( 'sidebar-menu-customizer-settings', 'custom_menu_names', array( 'sanitize_callback' => array( 'Add_function_PHP\Side_Menu\AFP_Side_Menu', 'sanitize_custom_menu_names' ) ) );

			// 管理バーのカスタマイズ設定を登録
			register_setting(
				'sidebar-menu-customizer-settings',
				'admin_bar_customizer_selected_options',
				array(
					'sanitize_callback' => array( 'Add_function_PHP\Side_Menu\AFP_Side_Menu', 'sanitize_selected_options' ),
				)
			);
			register_setting( 'sidebar-menu-customizer-settings', 'custom_menu_icons' );
		}


		/**
		 * 関数：sanitize_custom_menu_icons
		 * 概要：POSTされたアイコン情報をサニタイズ処理
		 *
		 * 詳細：入力されたアイコン情報を安全な形式に変換
		 *
		 * @param array $input - サニタイズするアイコン情報
		 * @return array - サニタイズされたアイコン情報
		 **/
		// POSTされたアイコン情報をサニタイズ処理
		public static function sanitize_custom_menu_icons( $input ) {
			if ( ! is_array( $input ) ) {
				return array();
			}
			// $output = array_map('sanitize_text_field', $input);
			return $output;
		}

		/**
		 * 関数：sanitize_selected_options
		 * 概要：サニタイズ（安全な値に処理）する
		 *
		 * 詳細：メニュー項目の表示/非表示設定をサニタイズ
		 *
		 * @param array $input - サニタイズする設定情報
		 * @return array - サニタイズされた設定情報
		 **/
		public static function sanitize_selected_options( $input ) {
			if ( self::$filter_applied ) {
				error_log( 'サニタイズ処理はすでに適用されています。' );
				return $input; // 既に適用されている場合は何もせずに返す
			}

			error_log( 'サニタイズ前の値: ' . print_r( $input, true ) );
			self::$filter_applied = true; // フィルター適用を記録

			$sanitized_input = array();
			$menu_items      = ( new \Add_function_PHP\Side_Menu\AFP_Side_Menu() )->get_sidebar_menu_items();
			error_log( 'menu_items: ' . print_r( $menu_items, true ) );
			$users = get_users();

			// 管理者用の設定をサニタイズ
			$sanitized_input['admin'] = array();
			foreach ( $menu_items as $menu_id => $menu_label ) {
				$sanitized_input['admin'][ $menu_id ] = isset( $input['admin'][ $menu_id ] ) && $input['admin'][ $menu_id ] === 'on' ? '1' : '0';
			}

			// 全ユーザー用の設定をサニタイズ
			foreach ( $users as $user ) {
				$user_key                     = 'user_' . $user->ID;
				$sanitized_input[ $user_key ] = array();
				foreach ( $menu_items as $menu_id => $menu_label ) {
					$sanitized_input[ $user_key ][ $menu_id ] = isset( $input[ $user_key ][ $menu_id ] ) && $input[ $user_key ][ $menu_id ] === 'on' ? '1' : '0';
				}
			}

			error_log( 'サニタイズ処理後の値: ' . print_r( $sanitized_input, true ) );
			return $sanitized_input;
		}

		/**
		 * 関数：sanitize_custom_menu_names
		 * 概要：新しいメニュー名のサニタイズ
		 *
		 * 詳細：入力されたメニュー名を安全な形式に変換
		 *
		 * @param array $input - サニタイズするメニュー名
		 * @return array - サニタイズされたメニュー名
		 **/
		public static function sanitize_custom_menu_names( $input ) {

			// error_log( 'Before sanitization: ' . print_r( $input, true ) );
			if ( ! is_array( $input ) ) {
				return array();
			}
			$output = array_map( 'sanitize_text_field', $input );
			// error_log( 'After sanitization: ' . print_r( $output, true ) );
			return $output;
		}

		/**
		 * 関数：remove_sidebar_menus
		 * 概要：サイドバーメニューの項目を非表示にする
		 *
		 * 詳細：カスタムメニューの表示/非表示を処理する関数をフック
		 **/
		public function remove_sidebar_menus() {
			// admin_initアクションにhandle_custom_menu_visibility関数をフックする
			add_action( 'admin_init', array( $this, 'handle_custom_menu_visibility' ) );
		}

		/**
		 * 関数：handle_custom_menu_visibility
		 * 概要：カスタムメニューの表示/非表示を処理する
		 *
		 * 詳細：保存された設定に基づいてメニュー項目の表示/非表示を制御
		 **/
		public function handle_custom_menu_visibility() {
			// admin_headアクションに匿名関数をフックする
			add_action(
				'admin_head',
				function () {
					// グローバル変数$menuを使用して、現在のメニュー項目を取得
					global $menu;
					// 保存されたカスタムメニューの表示/非表示設定を取得
					$selected_options = get_option( 'sidebar_menu_hidden_options', array() );
					// 現在のユーザー情報を取得
					$user = wp_get_current_user();

					// 各メニュー項目のIDと<li>要素のIDをマッピングする配列を初期化
					$menu_ids = array();
					foreach ( $menu as $menu_item ) {
						// メニュー項目のIDと<li>要素のIDをマッピング
						if ( isset( $menu_item[2] ) && $menu_item[2] && isset( $menu_item[5] ) ) {
							$menu_ids[ $menu_item[2] ] = $menu_item[5];
						}
					}

					// スタイルタグを出力開始
					echo '<style>';
					// サイドバーメニューの項目を取得
					$labels = $this->get_sidebar_menu_items();
					if ( is_array( $labels ) ) {
						foreach ( $labels as $menu_slug => $label ) {
							// メニュー項目のIDを取得
							$li_id = isset( $menu_ids[ $menu_slug ] ) ? $menu_ids[ $menu_slug ] : '';
							// 特定のメニュー項目のIDを変換
							$li_id = str_replace( 'edit?post_type=', 'edit-post_type-', $li_id );

							if ( $li_id ) {
								// CSSセレクタを作成
								$css_selector = "#adminmenu li#$li_id";

								// 現在のユーザーが管理者の場合
								if ( in_array( 'administrator', $user->roles ) ) {
									// 管理者権限を持つユーザーの個別設定を取得
									$user_option_key = 'user_' . $user->ID;
									if ( isset( $selected_options[ $user_option_key ][ $menu_slug ] ) ) {
										// 個別設定が非表示の場合
										if ( $selected_options[ $user_option_key ][ $menu_slug ] === '0' ) {
											// メニュー項目を非表示にするCSSを出力
											echo "$css_selector { display: none; }";
										}
									} else {
										// 個別設定がない場合は管理者全体の設定を適用
										if ( isset( $selected_options['admin'][ $menu_slug ] ) && $selected_options['admin'][ $menu_slug ] === '0' ) {
											// メニュー項目を非表示にするCSSを出力
											echo "$css_selector { display: none; }";
										}
									}
								} else {
									// 一般ユーザー用の設定を取得
									$user_option_key = 'user_' . $user->ID;
									if ( isset( $selected_options[ $user_option_key ][ $menu_slug ] ) && $selected_options[ $user_option_key ][ $menu_slug ] === '0' ) {
										// メニュー項目を非表示にするCSSを出力
										echo "$css_selector { display: none; }";
									}
								}
							}
						}
					}

					// スタイルタグを閉じる
					echo '</style>';
				}
			);
		}
		/**
		 * 関数：get_sidebar_menu_items
		 * 概要：サイドバーメニューの項目を取得する
		 *
		 * 詳細：現在のメニュー項目と追加されたメニュー項目を取得
		 *
		 * @return array - メニュー項目の配列
		 **/
		private function get_sidebar_menu_items() {
			global $menu;
			$menu_items = array();

			foreach ( $menu as $item ) {
				if ( ! empty( $item[0] ) && $item[2] !== $this->menu_slug ) {
					$menu_items[ $item[2] ] = strip_tags( $item[0] );
				}
			}

			// 新しいプラグインによって追加されたメニュー項目も取得する
			$additional_menus = apply_filters( 'add_custom_sidebar_menus', array() );
			foreach ( $additional_menus as $menu_key => $menu_value ) {
				$menu_items[ $menu_key ] = $menu_value;
			}

			return $menu_items;
		}
		/**
		 * 関数：get_admin_menu_items
		 * 概要：管理画面のメニュー項目を取得する
		 *
		 * 詳細：管理画面のメニュー項目とアイコンURLを取得
		 *
		 * @return array - メニュー項目の配列
		 **/
		public function get_admin_menu_items() {
			global $menu;
			$menu_items = array();

			foreach ( $menu as $item ) {
				$icon_url     = isset( $item[6] ) && ! empty( $item[6] ) ? $item[6] : 'none';
				$menu_items[] = array(
					'title'    => strip_tags( $item[0] ),
					'slug'     => $item[2],
					'icon_url' => $icon_url,
				);
			}
			return $menu_items;
		}
		/**
		 * 関数：get_menu_index_by_id
		 * 概要：メニューIDからインデックスを取得する
		 *
		 * 詳細：指定されたメニューIDに対応するインデックスを取得
		 *
		 * @param string $id - メニューID
		 * @return int|null - メニューインデックス、存在しない場合はnull
		 **/
		private function get_menu_index_by_id( $id ) {
			global $menu;
			foreach ( $menu as $index => $item ) {
				if ( $item[2] === $id ) {
					return $index;
				}
			}
			return null;
		}
		/**
		 * 関数：customize_admin_menu_label
		 * 概要：サイドバーメニューの名称を変更する
		 *
		 * 詳細：保存された設定に基づいてメニュー項目の名称とアイコンを変更
		 **/
		public function customize_admin_menu_label() {
			global $menu;

			// デバッグ: $menuの内容を確認
			// error_log( 'Debug: $menu = ' . print_r( $menu, true ) );

			// デバッグ: $menuの内容を確認
			// error_log('Debug: $menu = ' . print_r($menu, true));

			$custom_menu_names = get_option( 'custom_menu_names', array() );
			$custom_menu_icons = get_option( 'custom_menu_icons', array() );

			// デバッグ: $custom_menu_namesと$custom_menu_iconsの内容を確認
			// error_log('Debug: $custom_menu_names = ' . print_r($custom_menu_names, true));
			// error_log('Debug: $custom_menu_icons = ' . print_r($custom_menu_icons, true));

			// icon_box.phpファイルをインクルード
			include_once SERVER_PATH . 'assets/icon_fonts/icon_box.php';

			// $custom_menu_namesが配列であるかチェック
			if ( ! is_array( $custom_menu_names ) ) {
				// $custom_menu_namesが配列でない場合、空の配列を割り当てる
				$custom_menu_names = array();
				error_log( 'Error: $custom_menu_names is not an array. Initialized to empty array.' );
			}

			foreach ( $custom_menu_names as $id => $name ) {
				$index = $this->get_menu_index_by_id( $id );

				// デバッグ: $id, $name, $indexの値を確認
				// error_log("Debug: \$id = $id, \$name = $name, \$index = " . print_r($index, true));

				if ( ! is_null( $index ) && $name !== '' ) {
					$menu[ $index ][0] = sanitize_text_field( $name );
				}
			}

			// $custom_menu_iconsが配列であるかチェック
			if ( ! is_array( $custom_menu_icons ) ) {
				// $custom_menu_iconsが配列でない場合、空の配列を割り当てる
				$custom_menu_icons = array();
				error_log( 'Error: $custom_menu_icons is not an array. Initialized to empty array.' );
			}

			// サイドバーメニューにアイコンクラスを追加
			add_action(
				'admin_head',
				function () use ( $custom_menu_icons ) {
					echo '<style type="text/css">';
					foreach ( $custom_menu_icons as $id => $icon_class ) {
						if ( ! empty( $icon_class ) ) {
							// 特定のメニュー項目のIDを変換
							$css_selector = "#adminmenu a[href*='$id'] .wp-menu-image";

							// edit.php?post_type= で始まる場合の特別な処理
							if ( strpos( $id, 'edit.php?post_type=' ) === 0 ) {
								$css_selector = "#adminmenu a[href='$id'] .wp-menu-image";
							}

							if ( strpos( $icon_class, 'add-' ) === 0 ) {
								include SERVER_PATH . 'assets/icon_fonts/icon_box.php';
								if ( isset( $icons[ $icon_class ] ) ) {
									$icon_content = $icons[ $icon_class ];
									echo "$css_selector::before { 
                                font-family: \"icon-fonts\";
                                display: inline-block;
                                content: \"\\e$icon_content\"; 
                            }";
								}
							} elseif ( strpos( $icon_class, 'dashicons-' ) === 0 ) {
								include __DIR__ . '/../assets/icon_fonts/dashicons.php';
								if ( isset( $dashicons[ $icon_class ] ) ) {
									$icon_content = $dashicons[ $icon_class ];
									echo "$css_selector::before { 
                                font-family: \"dashicons\";
                                display: inline-block;
                                content: \"\\f$icon_content\"; 
                            }";
									echo "$css_selector { 
                                font-family: \"dashicons\";
                            }";
								}
							}
							echo "$css_selector { opacity: 1; }";
						}
					}
					echo '</style>';
				},
				PHP_INT_MAX // 優先度を最高に設定
			);

			// 優先度を最高に設定して、dashicons-admin-postをdashicons-admin-pageに置換
			add_action(
				'admin_head',
				function () {
					echo '<script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    var iconPreview = document.getElementById("icon_preview_edit.php?post_type=page");
                    if (iconPreview && iconPreview.classList.contains("dashicons-admin-post")) {
                        iconPreview.classList.remove("dashicons-admin-post");
                        iconPreview.classList.add("dashicons-admin-page");
                    }
                });
            </script>';
				},
				PHP_INT_MAX // 優先度を最高に設定
			);
		}
	}
}
