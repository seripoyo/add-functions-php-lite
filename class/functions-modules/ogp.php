<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_ogp {

	public function __construct() {

		add_action( 'wp_head', array( $this, 'output_ogp_image_meta_tag' ), 5 );
		add_action( 'template_redirect', array( $this, 'remove_first_title_tag' ), 2 );
		add_action( 'wp_footer', array( $this, 'flush_ob_start' ), 100 );
		add_action( 'wp_head', array( $this, 'output_taxonomy_description_as_meta_description' ) );
	}

	/**
	 * 関数：set_featured_image_as_ogp
	 * 概要：アイキャッチ画像をOGP画像として設定する
	 **/
	public function set_featured_image_as_ogp() {
		global $post;
		if ( isset( $post ) && has_post_thumbnail( $post->ID ) ) {
			// アイキャッチ画像のIDを取得
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			// アイキャッチ画像のURLを取得
			$thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'medium' );
			// OGP画像としてのmetaタグを出力
			if ( $thumbnail_url ) {
				echo '<meta property="og:image" content="' . esc_url( $thumbnail_url ) . '" />' . "\n";
			}
		}
	}


	/**
	 * 関数：output_ogp_image_meta_tag
	 * 概要：OGP画像のメタタグを出力する
	 *
	 * 詳細：アイキャッチ画像が存在しない場合にデフォルトのOGP画像を出力
	 **/
	public function output_ogp_image_meta_tag() {
		if ( is_category() || is_tag() || is_tax() || ! has_post_thumbnail() ) {
			$ogp_image = get_option( 'add_functions_php_settings' )['ogp_image'] ?? '';

			if ( ! empty( $ogp_image ) ) {
				// バッファリングを開始
				ob_start();
				echo '<meta property="og:image" content="' . esc_url( $ogp_image ) . '">' . "\n";
				// バッファの内容をフラッシュ
				ob_end_flush();
			}
		}
	}


	/**
	 * 関数：archive_add_ogp_image
	 * 概要：アーカイブページにOGP画像を追加する
	 **/
	public function archive_add_ogp_image() {
		// アーカイブページ、カテゴリーページ、タグページ、タクソノミーページであるかをチェック
		if ( is_archive() || is_category() || is_tag() || is_tax() ) {
			// デフォルトのOGP画像を取得
			$ogp_image = get_option( 'add_functions_php_settings' )['ogp_image'] ?? '';
			if ( ! empty( $ogp_image ) ) {
				// OGP画像のメタタグを出力
				echo '<meta property="og:image" content="' . esc_url( $ogp_image ) . '">' . "\n";
				// error_log( 'アイキャッチが存在しないため、設定されたOGP画像が出力されました。' );
			}
		}
	}
	/**
	 * 関数：add_twitter_card_id
	 * 概要：TwitterカードのIDを追加する
	 **/
	public function add_twitter_card_id() {
		$options = get_option( 'add_functions_php_settings' );
		if ( isset( $options['twitter_card'] ) && $options['twitter_card'] == '1' ) {
			// Twitter IDが設定されているか確認
			if ( ! empty( $options['twitter_id'] ) ) {
				echo '<meta name="twitter:site" content="@' . esc_attr( $options['twitter_id'] ) . '">' . "\n";
			}
		}
	}

	/**
	 * 関数：select_twitter_card_type
	 * 概要：Twitterカードのタイプを選択する
	 **/
	public function select_twitter_card_type() {
		// Twitterカードが有効化されているか確認
		$options = get_option( 'add_functions_php_settings' );
		if ( isset( $options['twitter_card'] ) && $options['twitter_card'] == '1' ) {
			// Twitterカードのタイプを確認し、対応するメタタグを出力
			if ( isset( $options['twitter_card_type'] ) && $options['twitter_card_type'] === 'summary_large_image' ) {
				echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			} elseif ( isset( $options['twitter_card_type'] ) && $options['twitter_card_type'] === 'summary' ) {
				echo '<meta name="twitter:card" content="summary">' . "\n";
			}
		} else {
		}
	}
	/**
	 * 関数：add_twitter_card_meta_tags
	 * 概要：Twitterカードのデフォルトタグを出力する
	 **/
	public function add_twitter_card_meta_tags() {
		global $post;

		$current_url         = get_pagenum_link();
		$singular_title      = wp_get_document_title();
		$twitter_title       = $singular_title;
		$twitter_description = '';

		if ( is_singular() || is_page() ) {
			$seo_title = get_post_meta( get_the_ID(), 'seo_title', true );
			if ( ! empty( $seo_title ) ) {
				$twitter_title = $seo_title;
			}
			$seo_description = get_post_meta( get_the_ID(), 'description', true );
			if ( ! empty( $seo_description ) ) {
				$twitter_description = $seo_description;
			} else {
				$twitter_description = mb_substr( strip_tags( get_the_content() ), 0, 120 );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$twitter_description = strip_tags( term_description( $term->term_id, $term->taxonomy ) );
			}
		} elseif ( is_post_type_archive() ) {
			$options             = get_option( 'posts_public functions_options_post', array() );
			$twitter_description = $options['archive_description'] ?? '';
		}

		echo '<meta name="twitter:title" content="' . esc_attr( $twitter_title ) . '">' . "\n";
		echo '<meta name="twitter:url" content="' . esc_url( $current_url ) . '">' . "\n";

		if ( ! empty( $twitter_description ) ) {
			echo '<meta name="twitter:description" content="' . esc_attr( $twitter_description ) . '">' . "\n";
		}

		if ( is_singular() && has_post_thumbnail() ) {
			$ogp_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		} else {
			$options   = get_option( 'add_functions_php_settings' );
			$ogp_image = $options['ogp_image'] ?? '';
		}

		if ( ! empty( $ogp_image ) ) {
			echo '<meta property="twitter:image" content="' . esc_url( $ogp_image ) . '">' . "\n";
		}
	}



	/**
	 * 関数：remove_first_title_tag
	 * 概要：最初の<title>タグを削除する
	 *
	 * @param string $buffer - 出力バッファの内容
	 * @return string - 修正後の出力バッファの内容
	 **/
	public function remove_first_title_tag() {
		ob_start(
			function ( $buffer ) {
				global $post;

				if ( is_single() || is_page() ) {
					$seo_title      = get_post_meta( $post->ID, 'seo_title', true );
					$singular_title = get_the_title(); // 記事の大元タイトルを取得

					// error_log('投稿ID: ' . $post->ID);
					// error_log('seo_title: ' . print_r($seo_title, true));
					// error_log('singular_title: ' . print_r($singular_title, true));

					// 正規表現を使用して<title>タグを見つける
					preg_match_all( '/<title.*?>(.*?)<\/title>/s', $buffer, $matches );

					// デバッグ: head内部の<title>タグの取得結果を出力
					// error_log('head内部の<title>タグ:');
					// error_log(print_r($matches[0], true));

					if ( count( $matches[0] ) > 1 ) {
						// <title>タグが複数ある場合
						foreach ( $matches[1] as $index => $title ) {
							// デバッグ: 各<title>タグの内容を出力
							// error_log('タイトルタグ' . ($index + 1) . ': ' . $title);

							if ( strpos( $title, $singular_title ) !== false ) {
								// 記事タイトルを含む<title>タグを削除
								$buffer = str_replace( $matches[0][ $index ], '', $buffer );

								// デバッグ: 記事タイトルを含む<title>タグの削除処理結果を出力
								// error_log('記事タイトルを含む<title>タグの削除後のbuffer:');
								// error_log($buffer);

								break;
							}
						}
					} else {
						// error_log('<title>タグの数が1つ以下です。');
					}
				} else {
					// error_log('単一の投稿ページでも投稿ページでもありません。');
				}

				return $buffer;
			}
		);
	}

	/**
	 * 関数：flush_ob_start
	 * 概要：出力バッファをフラッシュする
	 **/
	public function flush_ob_start() {
		ob_end_flush();
	}
	/**
	 * 関数：output_taxonomy_description_as_meta_description
	 * 概要：タクソノミーの説明をメタディスクリプションとして出力する
	 **/
	public function output_taxonomy_description_as_meta_description() {
		if ( is_category() || is_tag() || is_tax() ) {
			$term        = get_queried_object();
			$description = strip_tags( term_description( $term->term_id, $term->taxonomy ) );

			if ( ! empty( $description ) ) {
				echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
			}
		}
	}

	/**
	 * 関数：add_meta_tags
	 * head内にメタタグを出力
	 **/
	public function add_meta_tags() {
		global $post;

		$singular_title = wp_get_document_title();
		if ( is_single() || is_page() ) {
			$seo_title   = get_post_meta( $post->ID, 'seo_title', true );
			$title       = ! empty( $seo_title ) ? $seo_title : $singular_title;
			$keywords    = get_post_meta( $post->ID, 'keywords', true );
			$keywords    = str_replace( '、', ',', $keywords ); // 「、」を「,」に置換
			$description = get_post_meta( $post->ID, 'description', true );
			$noindex     = get_post_meta( $post->ID, 'noindex', true );
			$nofollow    = get_post_meta( $post->ID, 'nofollow', true );
		}

		$site_name = get_bloginfo( 'name' );
		$permalink = get_pagenum_link();
		if ( empty( $description ) ) {
			$description = mb_substr( strip_tags( get_the_content() ), 0, 120 );
		}
		if ( ! empty( $seo_title ) ) {
			$title = $seo_title;
		} else {
			$title = $singular_title;
		}

		// echo '<title>' . esc_attr( $title ) . '</title>' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
		if ( is_single() || is_page() ) {

			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";

			if ( $noindex == '1' && $nofollow == '1' ) {
				echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
			} elseif ( $noindex == '1' ) {
				echo '<meta name="robots" content="noindex, follow" />' . "\n";
			} elseif ( $nofollow == '1' ) {
				echo '<meta name="robots" content="nofollow" />' . "\n";
			}

			if ( ! empty( $keywords ) ) {
				echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();

			$options = get_option( 'add_functions_php_settings' );
			if ( isset( $options['taxonomy_description'] ) && $options['taxonomy_description'] == '1' ) {
				$description = strip_tags( term_description( $term->term_id, $term->taxonomy ) );
			} else {
				$description = mb_substr( strip_tags( term_description() ), 0, 120 );
			}
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
		} elseif ( is_post_type_archive() ) {
			$post_type = get_queried_object()->name;
			$options   = get_option( 'posts_public functions_options_' . $post_type, array() );

			$description = $options['archive_description'] ?? '';

			if ( empty( $description ) ) {
				$description = mb_substr( strip_tags( get_the_archive_description() ), 0, 120 );
			}

			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
		}

		echo '<meta property="og:url" content="' . esc_attr( $permalink ) . '" />' . "\n";
		if ( is_single() ) {
			echo '<meta property="og:type" content="article" />' . "\n";
		} else {
			echo '<meta property="og:type" content="website" />' . "\n";
		}

		echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
		echo '<meta property="og:locale" content="ja_JP"  />' . "\n";
	}
}
