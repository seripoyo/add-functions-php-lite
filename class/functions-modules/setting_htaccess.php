<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_htaccess {

	public function __construct() {

		add_action( 'init', array( $this, 'add_https_redirect_to_htaccess' ) );
		add_action( 'init', array( $this, 'setting_ignore_etags' ) );
		add_action( 'init', array( $this, 'update_keep_alive_setting' ) );
		add_action( 'init', array( $this, 'setting_cash_img_fonts' ) );
		add_action( 'init', array( $this, 'setting_stream_webp' ) );
		add_action( 'init', array( $this, 'setting_browser_cache' ) );
		add_action( 'init', array( $this, 'update_content_compression_setting' ) );
	}

	/**
	 * 関数：add_https_redirect_to_htaccess
	 * 概要：HTTPSにリダイレクトする設定を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルにHTTPSにリダイレクトする設定を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function add_https_redirect_to_htaccess() {
		$options       = get_option( 'add_functions_php_settings' );
		$htaccess_path = ABSPATH . '.htaccess';
		$site_url      = get_site_url();
		// URLからホスト名を抽出
		$domain = parse_url( $site_url, PHP_URL_HOST );

		// 既存の.htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			return;
		}

		// HTTPSリダイレクトの設定
		$htaccess_rules = <<<EOD
# BEGIN HTTPS Redirect
<IfModule mod_rewrite.c>
  RewriteEngine on
  RewriteCond %{HTTPS} off
  RewriteRule ^(.*)$ https://$domain/$1 [R=301,QSA,L]
</IfModule>
# END HTTPS Redirect
EOD;

		// redirect_httpsが1の時は設定を追加
		if ( isset( $options['redirect_https'] ) && $options['redirect_https'] == '1' ) {
			if ( strpos( $htaccess_content, '# BEGIN HTTPS Redirect' ) === false ) {
				// 既存の設定の後にHTTPSリダイレクト設定を追加
				file_put_contents( $htaccess_path, $htaccess_content . "\n" . $htaccess_rules );
			}
		}
		// redirect_httpsが0の時は設定を削除
		else {
			$pattern          = '/# BEGIN HTTPS Redirect.*?# END HTTPS Redirect\s*/s';
			$htaccess_content = preg_replace( $pattern, '', $htaccess_content );
			file_put_contents( $htaccess_path, $htaccess_content );
		}
	}

	/**
	 * 関数：setting_ignore_etags
	 * 概要：ETagを無視する設定を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルにETagを無視する設定を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function setting_ignore_etags() {
		$options       = get_option( ( 'add_functions_php_settings' ) );
		$htaccess_path = ABSPATH . '.htaccess';

		// .htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			// error_log("No .htaccess file found.");
			return;
		}

		// ETagsを無視する設定
		$setting_ignore_etags = <<<EOD
# ETagsを無視する設定
<IfModule mod_headers.c>
  Header unset ETag
</IfModule>
FileETag None

EOD;

		// ignore_etagsが1の時はETagsを無視する
		if ( isset( $options['ignore_etags'] ) && $options['ignore_etags'] == '1' ) {
			if ( strpos( $htaccess_content, 'Header unset ETag' ) === false ) {
				file_put_contents( $htaccess_path, "\n" . $setting_ignore_etags, FILE_APPEND );
				// error_log("ETagsを無視する設定を.htaccessに追加しました");
			} else {
				// error_log("ETagsを無視する設定は既に.htaccessに存在しています。");
			}
		}
		// ignore_etagsが0の時はETagsを無視する設定を削除
		else {
			$pattern = '/# ETagsを無視する設定.*?FileETag None\s*/s';
			if ( preg_match( $pattern, $htaccess_content ) ) {
				$htaccess_content = preg_replace( $pattern, '', $htaccess_content );
				file_put_contents( $htaccess_path, $htaccess_content );
				// error_log("ETagsを無視する設定が.htaccessから削除されました");
			} else {
				// error_log("削除対象のETagsを無視する設定が.htaccessにて見つかりませんでした");
			}
		}
	}

	/**
	 * 関数：update_keep_alive_setting
	 * 概要：Enable Keep-Alive を有効にする設定を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルにEnable Keep-Alive を有効にする設定を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function update_keep_alive_setting() {
		$options       = get_option( ( 'add_functions_php_settings' ) );
		$htaccess_path = ABSPATH . '.htaccess';

		// .htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			// error_log("No .htaccess file found.");
			return;
		}

		// Keep-Alive設定
		$keep_alive_rules = <<<EOD
# Enable Keep-Alive
<IfModule mod_headers.c>
Header set Connection keep-alive
</IfModule>
EOD;

		// setting_enable_keep_aliveが1の時は設定を追加
		if ( isset( $options['setting_enable_keep_alive'] ) && $options['setting_enable_keep_alive'] == '1' ) {
			if ( strpos( $htaccess_content, $keep_alive_rules ) === false ) {
				file_put_contents( $htaccess_path, "\n" . $keep_alive_rules, FILE_APPEND );
				// error_log("Keep-Alive設定を.htaccessに追加しました");
			} else {
				// error_log("Keep-Alive設定は既に.htaccessに存在しています。");
			}
		}
		// setting_enable_keep_aliveが0の時は設定を削除
		elseif ( strpos( $htaccess_content, 'Header set Connection keep-alive' ) !== false ) {
			$htaccess_content = preg_replace( '/# Enable Keep-Alive.*?<\/IfModule>\s*/s', '', $htaccess_content );
			file_put_contents( $htaccess_path, $htaccess_content );
			// error_log("Keep-Alive設定が.htaccessから削除されました");
		} else {
			// error_log("削除対象のKeep-Alive設定が.htaccessにて見つかりませんでした");

		}
	}
	/**
	 * 関数：setting_cash_img_fonts
	 * 概要：画像とフォントをキャッシュする設定を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルに画像とフォントをキャッシュする設定を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function setting_cash_img_fonts() {
		$options       = get_option( ( 'add_functions_php_settings' ) );
		$htaccess_path = ABSPATH . '.htaccess';

		// .htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			// error_log("No .htaccess file found.");
			return;
		}

		// 画像とフォントをキャッシュする設定
		$setting_cash_img_fonts = <<<EOD
# 画像とフォントをキャッシュする
<IfModule mod_headers.c>
<FilesMatch "\.(ico|jpe?g|png|gif|svg|webp|swf|pdf|ttf|woff|otf|eot)$">
  Header set Cache-Control "max-age=604800, public"
</FilesMatch>
</IfModule>
EOD;

		// cash_img_fontsが1の時は画像とフォントをキャッシュする
		if ( isset( $options['cash_img_fonts'] ) && $options['cash_img_fonts'] == '1' ) {
			if ( strpos( $htaccess_content, 'Header set Cache-Control "max-age=604800, public"' ) === false ) {
				file_put_contents( $htaccess_path, "\n" . $setting_cash_img_fonts, FILE_APPEND );
				// error_log("画像とフォントをキャッシュする設定を.htaccessに追加しました");
			} else {
				// error_log("画像とフォントをキャッシュする設定は既に.htaccessに存在しています。");
			}
		}
		// cash_img_fontsが0の時は画像とフォントをキャッシュする設定を削除
		else {
			$pattern = '/# 画像とフォントをキャッシュする.*?<\/FilesMatch>\s*<\/IfModule>\s*/s';
			if ( preg_match( $pattern, $htaccess_content ) ) {
				$htaccess_content = preg_replace( $pattern, '', $htaccess_content );
				file_put_contents( $htaccess_path, $htaccess_content );
				// error_log("画像とフォントをキャッシュする設定が.htaccessから削除されました");
			} else {
				// error_log("削除対象の画像とフォントをキャッシュする設定が.htaccessにて見つかりませんでした");
			}
		}
	}

	/**
	 * 関数：setting_stream_webp
	 * 概要：WebP画像を優先的に配信する設定を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルに同じファイル名でもWebP画像を優先的に配信する設定を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function setting_stream_webp() {
		$options       = get_option( ( 'add_functions_php_settings' ) );
		$htaccess_path = ABSPATH . '.htaccess';

		// .htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			// error_log("No .htaccess file found.");
			return;
		}

		// 同じファイル名でも.webpを優先的に配信する設定
		$stream_webp = <<<EOD
# 同じファイル名でも.webpを優先的に配信する
<IfModule mime_module>
    AddType image/webp .webp
</IfModule>
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP_ACCEPT} image/webp
    RewriteCond %{REQUEST_FILENAME} (.*)\.(jpe?g|png)$
    RewriteCond %{REQUEST_FILENAME}\.webp -f
    RewriteRule (.+)\.(jpe?g|png)$ %{REQUEST_FILENAME}.webp [T=image/webp,E=accept:1]
</IfModule>
<IfModule mod_headers.c>
    Header append Vary Accept env=REDIRECT_accept
</IfModule>

EOD;

		// stream_webpが1の時は同じファイル名でも.webpを優先的に配信する
		if ( isset( $options['stream_webp'] ) && $options['stream_webp'] == '1' ) {
			if ( strpos( $htaccess_content, 'AddType image/webp .webp' ) === false ) {
				file_put_contents( $htaccess_path, "\n" . $stream_webp, FILE_APPEND );
				// error_log(".webpを優先的に配信する設定を.htaccessに追加しました");
			} else {
				// error_log(".webpを優先的に配信する設定は既に.htaccessに存在しています。");
			}
		}
		// stream_webpが0の時は.webpを優先的に配信する設定を削除
		else {
			$pattern = '/# 同じファイル名でも\.webpを優先的に配信する.*?Header append Vary Accept env=REDIRECT_accept\s*<\/IfModule>\s*/s';
			if ( preg_match( $pattern, $htaccess_content ) ) {
				$htaccess_content = preg_replace( $pattern, '', $htaccess_content );
				file_put_contents( $htaccess_path, $htaccess_content );
				// error_log(".webpを優先的に配信する設定が.htaccessから削除されました");
			} else {
				// error_log("削除対象の.webpを優先的に配信する設定が.htaccessにて見つかりませんでした");
			}
		}
	}
	/**
	 * 関数：setting_browser_cache
	 * 概要：ブラウザキャッシュを設定する内容を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルにブラウザキャッシュを設定する内容を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function setting_browser_cache() {
		$options       = get_option( ( 'add_functions_php_settings' ) );
		$htaccess_path = ABSPATH . '.htaccess';

		// .htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			// error_log("No .htaccess file found.");
			return;
		}

		// ブラウザキャッシュを設定
		$browser_cache_rules = <<<EOD
# ブラウザキャッシュを設定する
<IfModule mod_headers.c>
<ifModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 seconds"
  ExpiresByType text/html "access plus 1 weeks"
  ExpiresByType text/css "access plus 1 weeks"
  ExpiresByType text/js "access plus 1 weeks"
  ExpiresByType text/javascript "access plus 1 weeks"  
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType image/x-icon "access plus 1 weeks"
  ExpiresByType application/pdf "access plus 1 weeks"
  ExpiresByType application/javascript "access plus 1 weeks"  
  ExpiresByType application/x-javascript "access plus 1 weeks"
  ExpiresByType application/x-font-ttf "access plus 1 year"
  ExpiresByType application/x-font-woff "access plus 1 year"
  ExpiresByType application/x-font-opentype "access plus 1 year"
  ExpiresByType application/vnd.ms-fontobject "access plus 1 year"
</IfModule>
</IfModule>
EOD;

		// cash_browserが1の時はブラウザキャッシュを設定
		if ( isset( $options['cash_browser'] ) && $options['cash_browser'] == '1' ) {
			if ( strpos( $htaccess_content, 'mod_expires.c' ) === false ) {
				file_put_contents( $htaccess_path, "\n" . $browser_cache_rules, FILE_APPEND );
				// error_log("ブラウザキャッシュ設定を.htaccessに追加しました");
			} else {
				// error_log("ブラウザキャッシュ設定は既に.htaccessに存在しています。");
			}
		}
		// cash_browserが0の時はブラウザキャッシュ設定を削除
		elseif ( strpos( $htaccess_content, '# ブラウザキャッシュを設定する' ) !== false ) {
			$htaccess_content = preg_replace( '/# ブラウザキャッシュを設定する.*?<\/IfModule>\s*<\/IfModule>\s*/s', '', $htaccess_content );
			file_put_contents( $htaccess_path, $htaccess_content );
			// error_log("ブラウザキャッシュ設定が.htaccessから削除されました");
		} else {
			// error_log("削除対象のブラウザキャッシュ設定が.htaccessにて見つかりませんでした");

		}
	}
	/**
	 * 関数：update_content_compression_setting
	 * 概要：コンテンツ圧縮の設定を.htaccessファイルに追加または削除
	 *
	 * 詳細：.htaccessファイルにHTML、CSS、JavaScript、テキスト、XML等のコンテンツを圧縮する設定を追加または削除。設定が有効な場合は追加し、無効な場合は削除。
	 **/
	public function update_content_compression_setting() {
		$options       = get_option( ( 'add_functions_php_settings' ) );
		$htaccess_path = ABSPATH . '.htaccess';

		// .htaccessファイルの内容を取得
		if ( file_exists( $htaccess_path ) ) {
			$htaccess_content = file_get_contents( $htaccess_path );
		} else {
			// .htaccessファイルが存在しない場合は何もしない
			// error_log("No .htaccess file found.");
			return;
		}

		// コンテンツ圧縮の設定
		$compression_rules = <<<EOD
# コンテンツ圧縮の設定
<IfModule mod_deflate.c>
  SetOutputFilter DEFLATE
  BrowserMatch ^Mozilla/4\.0[678] no-gzip
  BrowserMatch ^Mozilla/4 gzip-only-text/html
  BrowserMatch \bMSI[E] !no-gzip !gzip-only-text/html
  SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png|ico)$ no-gzip dont-vary
  SetEnvIfNoCase Request_URI _\.utxt$ no-gzip
  Header append Vary Accept-Encoding env=!dont-vary
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/js
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE image/svg+xml
  AddOutputFilterByType DEFLATE image/x-icon
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/atom_xml
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/x-httpd-php
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE application/x-font-woff
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-font
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE font/ttf
</IfModule>
EOD;

		// compress_fileが1の時は設定を追加
		if ( isset( $options['compress_file'] ) && $options['compress_file'] == '1' ) {
			if ( strpos( $htaccess_content, 'mod_deflate.c' ) === false ) {
				file_put_contents( $htaccess_path, "\n" . $compression_rules, FILE_APPEND );
				// error_log("コンテンツ圧縮設定を.htaccessに追加しました");
			} else {
				// error_log("コンテンツ圧縮設定は既に.htaccessに存在しています。");
			}
		}
		// compress_fileが0の時は設定を削除
		elseif ( strpos( $htaccess_content, 'mod_deflate.c' ) !== false ) {
			$htaccess_content = preg_replace( '/# コンテンツ圧縮の設定.*?<\/IfModule>\s*/s', '', $htaccess_content );
			file_put_contents( $htaccess_path, $htaccess_content );
			// error_log("コンテンツ圧縮設定が.htaccessから削除されました");
		} else {
			// error_log("削除対象のコンテンツ圧縮設定が.htaccessにて見つかりませんでした");

		}
	}
}
