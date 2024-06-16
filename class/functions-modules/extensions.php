<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_Extensions {

	/**
	 * 関数：__construct
	 * 概要：クラスのコンストラクタ
	 *
	 * 詳細：プラグイン・テーマの自動更新を有効にするフィルターを追加
	 *
	 * @var array - $options: add_functions_php_settings オプションの値
	 **/
	public function __construct() {
		$options = get_option( 'add_functions_php_settings' );

		// $optionsが配列であることを確認
		if ( ! is_array( $options ) ) {
			// $optionsが配列でない場合は、空の配列を設定
			$options = array();
		}
		add_filter( 'auto_update_plugin', '__return_true' );
		add_filter( 'allow_minor_auto_core_updates', '__return_true' );
		add_filter( 'auto_update_theme', '__return_true' );
	}
	/**
	 * 関数：enable_auto_updates
	 * 概要：プラグイン・テーマの自動更新を有効にする
	 *
	 * 詳細：プラグイン・テーマの自動更新を無効化するフィルターを削除
	 **/
	public function enable_auto_updates() {
		remove_filter( 'auto_update_plugin', '__return_false' );
		remove_filter( 'auto_update_theme', '__return_false' );
	}
	/**
	 * 関数：disable_srcset
	 * 概要：srcsetを出力しないようにしつつ、リンク切れを防ぐ
	 *
	 * @param array - $sources: srcsetのソース情報
	 * @return bool - 常にfalseを返す
	 **/
	public function disable_srcset( $sources ) {
		return false;
	}

	/**
	 * 関数：remove_srcset_attributes
	 * 概要：画像のsrcsetとsizes属性を削除する
	 *
	 * @param array - $attributes: 画像の属性情報
	 * @return array - srcsetとsizes属性を削除した属性情報
	 **/
	public function remove_srcset_attributes( $attributes ) {
		if ( isset( $attributes['srcset'] ) ) {
			unset( $attributes['srcset'] );
		}
		if ( isset( $attributes['sizes'] ) ) {
			unset( $attributes['sizes'] );
		}
		return $attributes;
	}


	/**
	 * 関数：disable_manual_updates
	 * 概要：手動更新を無効化する
	 *
	 * 詳細：プラグイン・テーマの手動更新を無効化するフィルターを削除
	 **/
	public function disable_manual_updates() {
		remove_filter( 'plugins_auto_update_enabled', '__return_false' );
		remove_filter( 'themes_auto_update_enabled', '__return_false' );
	}

	/**
	 * 関数：remove_dashboard_widgets
	 * 概要：ダッシュボードのウィジェットを削除する
	 *
	 * 詳細：不要なダッシュボードウィジェットを削除
	 **/
	public function remove_dashboard_widgets() {
		remove_action( 'welcome_panel', 'wp_welcome_panel' ); // WordPress へようこそ !
		remove_meta_box( 'dashboard_php_nag', 'dashboard', 'normal' ); // PHP の更新を推奨
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' ); // サイトヘルスステータス
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' ); // アクティビティ
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' ); // 概要
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' ); // クイックドラフト
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' ); // WordPress イベントとニュース
	}
	/**
	 * 関数：enqueue_fontawesome
	 * 概要：FontAwesomeのスタイルシートを読み込む
	 *
	 * 詳細：FontAwesomeのCDNからスタイルシートを読み込む
	 **/
	public function enqueue_fontawesome() {
		wp_enqueue_style(
			'fontawesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
			array(),
			'6.1.1'
		);
	}

	/**
	 * 関数：redirect_to_only_search_post
	 * 概要：検索結果が1件しかない場合はそのページにリダイレクトさせる
	 *
	 * 詳細：検索結果が1件の場合、その投稿のパーマリンクにリダイレクト
	 **/
	public function redirect_to_only_search_post() {
		if ( is_search() ) {
			global $wp_query;
			if ( $wp_query->post_count == 1 ) {
				wp_redirect( get_permalink( $wp_query->posts[0]->ID ) );
				exit;
			}
		}
	}

	/**
	 * 関数：update_message_hidden
	 * 概要：特定ユーザー以外への更新通知を非表示にする
	 *
	 * 詳細：管理者以外のユーザーに対してWordPressの更新通知を非表示にする
	 **/
	public function update_message_hidden() {
		if ( ! current_user_can( 'administrator' ) ) {
			add_filter( 'pre_site_transient_update_core', '__return_zero' );
			remove_action( 'wp_version_check', 'wp_version_check' );
			remove_action( 'admin_init', '_maybe_update_core' );
		}
	}
	/**
	 * 関数：attachment_status404
	 * 概要：添付ファイルページを404表示にする
	 *
	 * 詳細：添付ファイルページにアクセスした場合、404ステータスを返す
	 **/
	public function attachment_status404() {
		if ( is_attachment() ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}
	/**
	 * 関数：maintenance_mode
	 * 概要：メンテナンスモードを実行する
	 *
	 * 詳細：ログインしていないユーザーまたはテーマ編集権限のないユーザーにメンテナンス中のメッセージを表示
	 **/
	public function maintenance_mode() {
		if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
			wp_die( '<h1>ただいまメンテナンス中です。</h1><p>ご迷惑をお掛けしています。</p>' );
		}
	}

	/**
	 * 関数：show_current_template
	 * 概要：現在使用中のテンプレートファイル名を管理バーに表示する
	 *
	 * 詳細：ログインユーザーに対して、現在のテンプレートファイル名とテーマ名を管理バーに表示
	 **/
	public function show_current_template() {
		if ( is_user_logged_in() ) {
			global $template;
			if ( $template !== null ) {
				$template_name = basename( $template );
				$theme_name    = wp_get_theme();

				$wp_admin_bar = $GLOBALS['wp_admin_bar'];
				$wp_admin_bar->add_menu(
					array(
						'id'    => 'show_template',
						'title' => 'Template: ' . $template_name,
						'href'  => '',
						'meta'  => array(
							'title' => 'Theme: ' . $theme_name . "\nTemplate: " . $template_name,
						),
					)
				);
			}
		}
	}

	/**
	 * 関数：svg_uploader
	 * 概要：SVGファイルのアップロードを許可する
	 *
	 * @param array - $file_types: 許可するファイルタイプの配列
	 * @return array - SVGファイルタイプを追加した配列
	 **/
	public function svg_uploader( $file_types ) {
		// error_log( 'svg_uploaderが呼ばれたよ' );
		$new_files        = array();
		$new_files['svg'] = 'image/svg+xml';
		$file_types       = array_merge( $file_types, $new_files );
		return $file_types;
	}

	/**
	 * 関数：pdf_uploader
	 * 概要：PDFファイルのアップロードを許可する
	 *
	 * @param array - $file_type: 許可するファイルタイプの配列
	 * @return array - PDFファイルタイプを追加した配列
	 **/
	public function pdf_uploader( $file_type ) {

		$file_type['pdf'] = 'application/pdf';
		return $file_type;
	}
	/**
	 * 関数：remove_empty_p_tags
	 * 概要：空のpタグを自動削除する
	 *
	 * @param string - $content: 投稿コンテンツ
	 * @return string - 空のpタグを削除したコンテンツ
	 **/
	public function remove_empty_p_tags( $content ) {
		$pattern = '/<p[^>]*>\s*<\/p>/';
		$content = preg_replace( $pattern, '', $content );

		return $content;
	}


	/**
	 * 関数：remove_catch_copy
	 * 概要：ホームページのキャッチコピーを削除する
	 *
	 * @param array - $title: ページタイトルの配列
	 * @return array - キャッチコピーを削除したページタイトルの配列
	 **/
	public function remove_catch_copy( $title ) {
		if ( is_home() || is_front_page() ) {
			$title['tagline'] = '';
		}
		return $title;
	}
}
