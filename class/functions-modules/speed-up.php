<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_Speed {
	public function __construct() {
	add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );
	}

/**
 * 関数：add_media_print_onload
 * 概要：CSSのCDNタグにmedia="print" onload="this.media=all"を追加
 *
 * @param string - $html: 元のHTMLタグ
 * @param string - $handle: スタイルシートのハンドル名
 * @param string - $href: スタイルシートのURL
 * @param string - $media: メディアクエリ
 * @return string - 変更後のHTMLタグ
 *
 * 詳細：管理画面以外で、外部のCSSファイルを読み込む際にmedia属性とonload属性を追加。
 **/
	public function add_media_print_onload( $html, $handle, $href, $media ) {
		if ( is_admin() ) {
			return $html;
		}

		if ( strpos( $href, 'https://' ) === 0 || strpos( $href, '//' ) === 0 ) {
			$html = str_replace( "media='{$media}'", "media='print' onload='this.media=\"all\"'", $html );
		}

		return $html;
	}

/**
 * 関数：load_custom_scripts
 * 概要：フロントエンドに遅延読み込み用のJavaScriptを追加
 *
 * 詳細：管理画面以外で、遅延読み込みを実現するためのJavaScriptファイルを読み込む。
 **/
	public function load_custom_scripts() {
		// 管理画面でない場合のみスクリプトを読み込む
		if ( ! is_admin() ) {
			// plugins_url()を使って正しいURLを取得
			// __FILE__からの相対パスを使用して、プラグインのルートディレクトリのURLを取得
		
			wp_enqueue_style( 'lazysizes-add', CSS_PATH . 'front.css' );
			// imgタグの画像とiframeが遅延読み込み可能になるlazysizes等
			wp_enqueue_script( 'lazysizes', JS_PATH . 'lazysizes.min.js', array(), null, true );

			// 背景遅延読み込み
			wp_enqueue_script( 'unveilhooks', JS_PATH . 'ls.unveilhooks.min.js', array( 'lazysizes' ), null, true );
		}
	}

/**
 * 関数：filter_the_content_for_lazyload
 * 概要：コンテンツ内の画像タグを遅延読み込み用に変更
 *
 * @param string - $content: 元のコンテンツ
 * @return string - 変更後のコンテンツ
 *
 * 詳細：コンテンツ内の画像タグを探し、遅延読み込み用のクラスとデータ属性を追加。
 **/
	public function filter_the_content_for_lazyload( $content ) {
		$content = preg_replace_callback(
			'/<img([^>]*)>/',
			function ( $matches ) {
				$match = rtrim( $matches[1], '/' );

				// classを持っているかどうか
				if ( strpos( $match, 'class=' ) !== false ) {
					// まだ'lazyload'クラスを持っていなければ追加
					if ( strpos( $match, 'lazyload' ) === false ) {
						$match = preg_replace( '/class="([^"]*)"/', 'class="$1 lazyload"', $match );
					}
				} else {
					// classがなければ、classごと追加
					$match .= 'class="lazyload" ';
				}

				// プレースホルダー画像のURL
				$placeholder = 'https://placehold.jp/ffffff/ffffff/1x1.png';
				// src属性をdata-srcに変更
				$match = str_replace( ' src=', ' src="' . $placeholder . '" data-src=', $match );

				return '<img' . $match . ' class="lazyload">';
			},
			$content
		);

		return $content;
	}

/**
 * 関数：add_lazy_to_img
 * 概要：imgタグにloading="lazy"を追加して画像の読み込みを遅らせる
 *
 * @param string - $content: 元のコンテンツ
 * @return string - 変更後のコンテンツ
 *
 * 詳細：コンテンツ内のimgタグを探し、loading属性とdecoding属性を追加。
 **/

	public function add_lazy_to_img( $content ) {
		$re_content = preg_replace( '/(<img[^>]*)\s+(?:class="([^"]*)"\s*)?(\/?>)/', '$1 class="$2" decoding="async" loading="lazy"$3', $content );
		return $re_content;
	}

/**
 * 関数：add_defer_attribute
 * 概要：jQueryのスクリプトタグにdeferを追加
 *
 * @param string - $tag: 元のスクリプトタグ
 * @param string - $handle: スクリプトのハンドル名
 * @param string - $src: スクリプトのURL
 * @return string - 変更後のスクリプトタグ
 *
 * 詳細：管理画面以外で、jQueryのスクリプトタグにdefer属性を追加。
 **/
	public function add_defer_attribute( $tag, $handle, $src ) {
		if ( ! is_admin() && strpos( $src, 'jquery' ) !== false ) {
			// defer 属性を追加
			$tag = str_replace( ' src', ' defer src', $tag );
		}
		return $tag;
	}

/**
 * 関数：add_async_attribute
 * 概要：jQueryのスクリプトタグにasyncを追加
 *
 * @param string - $tag: 元のスクリプトタグ
 * @param string - $handle: スクリプトのハンドル名
 * @param string - $src: スクリプトのURL
 * @return string - 変更後のスクリプトタグ
 *
 * 詳細：管理画面以外で、jQueryのスクリプトタグにasync属性を追加。
 **/
	public function add_async_attribute( $tag, $handle, $src ) {
		if ( ! is_admin() && strpos( $src, 'jquery' ) !== false ) {
			// async 属性を追加
			$tag = str_replace( ' src', ' async src', $tag );
		}
		return $tag;
	}

/**
 * 関数：replace_jquery_head
 * 概要：WordPress純正のjQueryをGoogle CDNのものに変更し、<head>内で読み込む
 *
 * 詳細：管理画面以外で、WordPress純正のjQueryを解除し、Google CDNのjQueryを<head>内で読み込む。
 **/
	public function replace_jquery_head() {
		if ( ! is_admin() ) { // 管理画面ではない場合にのみ実行
			// まず既存の jQuery を解除

			wp_deregister_script( 'jquery' );

			// 新しい jQuery を登録
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', false, '3.7.1' );

			// 新しい jQuery をキューに追加
			wp_enqueue_script( 'jquery' );
		}
	}

/**
 * 関数：replace_jquery_before_body
 * 概要：WordPress純正のjQueryをGoogle CDNのものに変更し、</body>直前で読み込む
 *
 * 詳細：管理画面以外で、WordPress純正のjQueryを解除し、Google CDNのjQueryを</body>直前で読み込む。
 **/
	public function replace_jquery_before_body() {
		if ( ! is_admin() ) { // 管理画面ではない場合にのみ実行

			// まず既存の jQuery を解除

			wp_deregister_script( 'jquery' );

			// 新しい jQuery を登録
			// ここで第五引数に true を設定して、スクリプトがフッターに読み込まれるようにします
			wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js', array(), '3.7.1', true );

			// 新しい jQuery をキューに追加
			wp_enqueue_script( 'jquery' );
		}
	}
/**
 * 関数：remove_jquery_migrate
 * 概要：jQuery Migrateを読み込まないようにする
 *
 * @param object - $scripts: WP_Scripts オブジェクト
 *
 * 詳細：管理画面以外で、jQueryの依存関係からjQuery Migrateを削除。
 **/
	public function remove_jquery_migrate( &$scripts ) {
		if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
			$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
		}
	}
/**
 * 関数：register_custom_meta_boxes
 * 概要：カスタムフィールドを登録
 *
 * 詳細：各投稿タイプに対してSEO用のカスタムフィールドを登録。
 **/
	public function add_custom_fields() {
		// error_log( 'add_custom_fields メソッドが呼び出されました。' );
		add_action( 'add_meta_boxes', array( $this, 'register_custom_meta_boxes' ) );
	}

	public function register_custom_meta_boxes() {
		// error_log( 'register_custom_meta_boxes メソッドが呼び出されました。' );
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $post_types as $post_type ) {
			// error_log( 'Processing post type: ' . $post_type->name );
			if ( $post_type->name !== 'attachment' ) {
				\add_meta_box( 'seo_setting', 'SEO対策', array( $this, 'seo_custom_fields' ), $post_type->name, 'normal', 'high' );
				// error_log( 'add_meta_box が呼び出されました。' );
			}
		}
	}
/**
 * 関数：seo_custom_fields
 * 概要：SEO用のカスタムフィールドを表示
 *
 * 詳細：投稿画面にSEO用のカスタムフィールドを表示。
 **/

	public function seo_custom_fields() {
		global $post;
		$title       = get_post_meta( $post->ID, 'seo_title', true );
		$keywords    = get_post_meta( $post->ID, 'keywords', true );
		$description = get_post_meta( $post->ID, 'description', true );
		$noindex     = get_post_meta( $post->ID, 'noindex', true );
		$nofollow    = get_post_meta( $post->ID, 'nofollow', true );

		include_once SERVER_PATH . 'class/functions-modules/seo-fields.php';
	}
/**
 * 関数：save_custom_fields
 * 概要：カスタムフィールドの値を保存
 *
 * @param int - $post_id: 投稿ID
 *
 * 詳細：投稿が保存された際に、SEO用のカスタムフィールドの値を保存。
 **/
	public function save_custom_fields( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'inline-save' ) {
			return $post_id;
		}

		if ( ! empty( $_POST['seo_title'] ) ) {
			update_post_meta( $post_id, 'seo_title', $_POST['seo_title'] );
		} else {
			delete_post_meta( $post_id, 'seo_title' );
		}
		if ( ! empty( $_POST['keywords'] ) ) {
			update_post_meta( $post_id, 'keywords', $_POST['keywords'] );
		} else {
			delete_post_meta( $post_id, 'keywords' );
		}
		if ( ! empty( $_POST['description'] ) ) {
			update_post_meta( $post_id, 'description', $_POST['description'] );
		} else {
			delete_post_meta( $post_id, 'description' );
		}
		if ( ! empty( $_POST['noindex'] ) ) {
			update_post_meta( $post_id, 'noindex', $_POST['noindex'] );
		} else {
			delete_post_meta( $post_id, 'noindex' );
		}
		if ( ! empty( $_POST['nofollow'] ) ) {
			update_post_meta( $post_id, 'nofollow', $_POST['nofollow'] );
		} else {
			delete_post_meta( $post_id, 'nofollow' );
		}
	}
/**
 * 関数：transition_post_status
 * 概要：投稿の状態が変更された際の処理
 *
 * @param string - $new_status: 新しい投稿ステータス
 * @param string - $old_status: 古い投稿ステータス
 * @param object - $post: 投稿オブジェクト
 * @return object - 投稿オブジェクト
 *
 * 詳細：投稿の状態が公開に変更された場合、カスタムフィールドの値を保存するアクションフックを追加。
 **/
	public function transition_post_status( $new_status, $old_status, $post ) {
		if ( ( $old_status == 'auto-draft' || $old_status == 'draft' || $old_status == 'pending' || $old_status == 'future' ) && $new_status == 'publish' ) {
			return $post;
		} else {
			add_action( 'save_post', array( $this, 'save_custom_fields' ) );
		}
	}
}
