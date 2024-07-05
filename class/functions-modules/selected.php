<?php
namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;
// $options =get_option( 'add_functions_php_settings' );
$options = get_option( 'add_functions_php_settings' );

// $optionsが配列であることを確認
if ( ! is_array( $options ) ) {
	// $optionsが配列でない場合は、空の配列を設定
	$options = array();
}
// 各条件のデバッグ出力を追加


// ===============================
// セキュリティ対策
// ===============================
// WordPressのログインエラー情報を非表示にする（IDが間違っているなど）
if ( isset( $options['login_error_message'] ) && $options['login_error_message'] == '1' ) {
	add_filter( 'wp_login_errors', array( $this->functions_security, 'custom_login_errors' ), 10, 2 );
}
// /wp-content/uploads/へのアクセスを禁止して403表示にする
if ( isset( $options['hidden_media_url'] ) && $options['hidden_media_url'] == '1' ) {
	add_action( 'init', array( $this->functions_security, 'create_uploads_htaccess' ) );
}


// 日本からのみログインを許可
if ( isset( $options['reject_login'] ) && $options['reject_login'] == '1' ) {
		// error_log( 'rreject_loginは1です' );
	add_action( 'login_init', array( $this->functions_security, 'restrict_login_by_country' ) );
		// error_log( 'restrict_login_by_countryを呼び出します' );
}
// ログイン試行回数を3回までに制限する
if ( isset( $options['login_limit_3'] ) && $options['login_limit_3'] == '1' ) {
	// ログイン試行前に試行回数をチェック
	add_action( 'login_init', array( $this->functions_security, 'limit_login_attempts' ) );

	// ログイン失敗時に試行回数を記録
	add_action( 'wp_login_failed', array( $this->functions_security, 'track_login_attempts' ) );
}


// ================================
// OGP
// ===============================
// OGP画像設定
if ( isset( $options['ogp_img'] ) && $options['ogp_img'] == '1' ) {
	add_action( 'wp_head', array( $this->functions_ogp, 'set_featured_image_as_ogp' ) );
}

// 最低限のTwitterカードタグを出力する
if ( isset( $options['ogp_meta_tag'] ) && $options['ogp_meta_tag'] == '1' ) {
	add_action( 'wp_head', array( $this->functions_ogp, 'add_meta_tags' ), 1 );
}
// 最低限のTwitterカードタグを出力する
if ( isset( $options['twitter_card'] ) && $options['twitter_card'] == '1' ) {
	add_action( 'wp_head', array( $this->functions_ogp, 'add_twitter_card_meta_tags' ) );
}
// Twitter IDのサニタイズとデバッグ
if ( isset( $options['twitter_id'] ) ) {
	$options['twitter_id'] = sanitize_text_field( $options['twitter_id'] );
	// error_log('Sanitized Twitter ID: ' . $options['twitter_id']);
	add_action( 'wp_head', array( $this->functions_ogp, 'add_twitter_card_id' ) );
} else {
	// error_log('Twitter ID not set');
}

// Twitter Card Typeのサニタイズとデバッグ
if ( isset( $options['twitter_card_type'] ) && in_array( $options['twitter_card_type'], array( 'summary_large_image', 'summary' ) ) ) {
	$options['twitter_card_type'] = $options['twitter_card_type'];
	add_action( 'wp_head', array( $this->functions_ogp, 'select_twitter_card_type' ), 5 );

} else {
	$options['twitter_card_type'] = 'summary_large_image'; // デフォルト値
	// error_log('Default Twitter Card Type used');
}




// 更新されたオプションをデータベースに保存
update_option( 'add_functions_php_settings', $options );





// ================================
// 拡張機能
// ===============================
// 特定ユーザー以外への更新通知を非表示
if ( isset( $options['hide_update_notices'] ) && $options['hide_update_notices'] == '1' ) {
	add_action( 'admin_init', array( $this->functions_extensions, 'update_message_hidden' ) );
}

// メジャーアップデートの実行許可
if ( isset( $options['enable_auto_updates'] ) && $options['enable_auto_updates'] == '1' ) {
	// 自動更新を有効化するアクションフック
	add_action( 'enable_auto_updates', array( $this->functions_extensions, 'enable_auto_updates' ) );
	// 手動更新を無効化するアクションフック
	add_action( 'disable_manual_updates', array( $this->functions_extensions, 'disable_manual_updates' ) );
} else {
	// 自動更新を無効化するフック
	add_filter( 'auto_update_plugin', '__return_false' );
	add_filter( 'auto_update_theme', '__return_false' );

	// 手動更新を有効化するフック
	add_filter( 'plugins_auto_update_enabled', '__return_false' );
	add_filter( 'themes_auto_update_enabled', '__return_false' );
}
// デフォルトウィジェットの全削除
if ( isset( $options['remove_default_widgets'] ) && $options['remove_default_widgets'] == '1' ) {
	add_action( 'wp_dashboard_setup', array( $this->functions_extensions, 'remove_dashboard_widgets' ) );
}

// メンテナンスモードの実行
if ( isset( $options['maintenance_mode'] ) && $options['maintenance_mode'] == '1' ) {
	add_action( 'get_header', array( $this->functions_extensions, 'maintenance_mode' ) );
}
// 検索結果が1件しかない場合はそのページにリダイレクトさせる
if ( isset( $options['redirect_single_search_result'] ) && $options['redirect_single_search_result'] == '1' ) {
	add_action( 'template_redirect', array( $this->functions_extensions, 'redirect_to_only_search_post' ) );
}
// FontAwesome
if ( isset( $options['fontawesome_v6'] ) && $options['fontawesome_v6'] === '1' ) {
	add_action( 'wp_enqueue_scripts', array( $this->functions_extensions, 'enqueue_fontawesome' ) );
}

// WordPressで大きな画像をアップする時に強制縮小させないようにする
if ( isset( $options['img_resize_cancel'] ) && $options['img_resize_cancel'] == '1' ) {
		add_filter( 'big_image_size_threshold', '__return_false' );
}

// SVGをアップロードできるようにする
if ( isset( $options['svg_upload'] ) && $options['svg_upload'] == '1' ) {
	add_filter( 'upload_mimes', array( $this->functions_extensions, 'svg_uploader' ) );
}
// PDFをアップロードできるようにする
if ( isset( $options['pdf_upload'] ) && $options['pdf_upload'] == '1' ) {
	add_filter( 'upload_mimes', array( $this->functions_extensions, 'pdf_uploader' ) );
}
// 空のpタグを自動削除する
if ( isset( $options['remove_blank_p'] ) && $options['remove_blank_p'] == '1' ) {
	add_filter( 'the_content', array( $this->functions_extensions, 'remove_empty_p_tags' ) );
}

// ================================
// スピード
// ===============================
// WordPress純正jQueryをGoogle CDNに変更して<head>内部で読み込む
if ( isset( $options['jquery_head'] ) && $options['jquery_head'] === '1' ) {
	add_action( 'wp_enqueue_scripts', array( $this->functions_speed, 'replace_jquery_head' ) );

}
// WordPress純正jQueryをGoogle CDNに変更して</body>直前で読み込む
if ( isset( $options['jquery_body'] ) && $options['jquery_body'] === '1' ) {
	add_action( 'wp_enqueue_scripts', array( $this->functions_speed, 'replace_jquery_before_body' ) );
}

// jQuery Migrateを読み込まない
if ( isset( $options['jquery_miragrate'] ) && $options['jquery_miragrate'] === '1' ) {
	add_action( 'wp_default_scripts', array( $this->functions_speed, 'remove_jquery_migrate' ) );
}
// lazysizes.jsでimg, video, iframeを遅延読み込み
if ( isset( $options['use_lazysizes'] ) && $options['use_lazysizes'] == '1' ) {
	add_action( 'wp_enqueue_scripts', array( $this->functions_speed, 'load_custom_scripts' ) );
	add_filter( 'the_content', array( $this->functions_speed, 'filter_the_content_for_lazyload' ) );
}

// imgにloading = "lazy”を追加して画像の読み込みを遅らせる
if ( isset( $options['add_lazy'] ) && $options['add_lazy'] == '1' ) {
	add_filter( 'the_content', array( $this->functions_speed, 'add_lazy_to_img' ) );
}

// JQueryにdeferを付与
if ( isset( $options['add_defer_to_jquery'] ) && $options['add_defer_to_jquery'] == '1' ) {
	add_filter( 'script_loader_tag', array( $this->functions_speed, 'add_defer_attribute' ), 10, 3 );
}
// JQueryにasyncを付与
if ( isset( $options['add_async_to_jquery'] ) && $options['add_async_to_jquery'] == '1' ) {
	add_filter( 'script_loader_tag', array( $this->functions_speed, 'add_async_attribute' ), 10, 3 );
}
// SEO設定
if ( isset( $options['seo_setting'] ) && $options['seo_setting'] == '1' ) {
	add_action( 'admin_menu', array( $this->functions_speed, 'add_custom_fields' ) );
}
// ================================
// <head></head>
// ===============================
// セルフピンバックの無効化
if ( isset( $options['self_pinback_invalid'] ) && $options['self_pinback_invalid'] === '1' ) {
	add_action( 'pre_ping', 'disable_self_pings' );
}
// 短縮URLの非表示
if ( isset( $options['shorten_url'] ) && $options['shorten_url'] === '1' ) {
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 ); // 追加して短縮URLに関するすべてのヘッダーを削除
}
// shortlink削除
if ( isset( $options['remove_link_short'] ) && $options['remove_link_short'] == '1' ) {
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
// バージョン表示削除
if ( isset( $options['wp_version'] ) && $options['wp_version'] == '1' ) {
	remove_action( 'wp_head', 'wp_generator' );
}
// rel="prev/next"を出力しない
if ( isset( $options['remove_prev_next'] ) && $options['remove_prev_next'] == '1' ) {
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
}
// EditURIを削除
if ( isset( $options['edt_uri'] ) && $options['edt_uri'] == '1' ) {
	remove_action( 'wp_head', 'rsd_link' );
}
// RSSフィード/コメントフィードを非表示にする
if ( isset( $options['none_rss'] ) && $options['none_rss'] == '1' ) {
	remove_action( 'wp_head', 'feed_links', 2 );
}

// rel="next" rel="prev" を非表示にする
if ( isset( $options['remove_prev_next'] ) && $options['remove_prev_next'] == '1' ) {
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
}

// wlwmanifestを非表示にする
if ( isset( $options['remove_windows'] ) && $options['remove_windows'] == '1' ) {
	remove_action( 'wp_head', 'wlwmanifest_link' );
}
// WordPress 標準のサイトマップ出力を停止
if ( isset( $options['stop_standard_sitemap_output'] ) && $options['stop_standard_sitemap_output'] == '1' ) {
	add_filter( 'wp_sitemaps_enabled', '__return_false' );
}
// REST API用linkタグを出力しない

if ( isset( $options['no_output_rest_api'] ) && $options['no_output_rest_api'] == '1' ) {
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
}
// “srcset”の出力を停止
if ( isset( $options['not_output_srcset'] ) && $options['not_output_srcset'] == '1' ) {
		add_filter( 'wp_calculate_image_srcset', array( $this->functions_extensions, 'disable_srcset' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this->functions_extensions, 'remove_srcset_attributes' ), 10, 1 );
}
// テンプレートファイル名出力
if ( isset( $options['template_file'] ) && $options['template_file'] == '1' ) {
	add_action( 'admin_bar_menu', array( $this->functions_extensions, 'show_current_template' ), 9999 );
}

// 絵文字機能削除
if ( isset( $options['remove_emoji'] ) && $options['remove_emoji'] == '1' ) {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
