<?php
namespace Add_function_PHP\Posts;

defined( 'ABSPATH' ) || exit;

class Posts_Setting {

	public function __construct() {
		add_filter( 'register_post_type_args', array( $this, 'post_has_archive' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'change_archive_order' ) );
		add_action( 'pre_get_posts', array( $this, 'change_posts_per_page' ) );
	}
	// =============================================
	// 投稿アーカイブの有効化
	// =============================================


	function post_has_archive( $args, $post_type ) {
		if ( 'post' == $post_type ) {
			$options = get_option( 'posts_functions_options_post', array() );
			// オプションが未設定の場合のデフォルト値を使用する
			$archive_slug  = isset( $options['archive_slug'] ) ? $options['archive_slug'] : '';
			$archive_label = isset( $options['archive_label'] ) && ! empty( $options['archive_label'] ) ? $options['archive_label'] : __( '投稿', 'add-functions-php' );

			$args['rewrite']     = array( 'slug' => $archive_slug );
			$args['has_archive'] = true;
			$args['label']       = $archive_label;
		}
		return $args;
	}

	// ===============================================
	// アーカイブページ表示順を変更
	// ===============================================

	function change_archive_order( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type ) {
			if ( $query->is_post_type_archive( $post_type ) || $query->is_tax( $post_type ) || ( $query->is_category() && $post_type === 'post' ) || ( $query->is_tag() && $post_type === 'post' ) ) {
				$selected_options = get_option( 'posts_functions_options_' . $post_type, array() );
				if ( isset( $selected_options['order'] ) && isset( $selected_options['orderby'] ) ) {
					$order   = $selected_options['order'];
					$orderby = $selected_options['orderby'];

					$query->set( 'order', $order );
					$query->set( 'orderby', $orderby );
				}
			}
		}
	}


	// ===============================================
	// カテゴリー・タグ・アーカイブページの出力数変更
	// ===============================================
	function change_posts_per_page( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type ) {
			if ( $query->is_post_type_archive( $post_type ) ) {
				$posts_per_page = get_option( 'posts_functions_options_' . $post_type . '_posts_per_page', '10' );
				$query->set( 'posts_per_page', $posts_per_page );
			}

			if ( $post_type === 'post' ) {
				if ( $query->is_category() ) {
					$posts_per_page = get_option( 'posts_functions_options_post_category_posts_per_page', '10' );
					$query->set( 'posts_per_page', $posts_per_page );
				}

				if ( $query->is_tag() ) {
					$posts_per_page = get_option( 'posts_functions_options_post_tag_posts_per_page', '10' );
					$query->set( 'posts_per_page', $posts_per_page );
				}
			} else {
				$taxonomies = get_object_taxonomies( $post_type, 'names' );
				foreach ( $taxonomies as $taxonomy ) {
					if ( $query->is_tax( $taxonomy ) ) {
						$posts_per_page = get_option( 'posts_functions_options_' . $post_type . '_' . $taxonomy . '_posts_per_page', '10' );
						$query->set( 'posts_per_page', $posts_per_page );
					}
				}
			}
		}
	}
}
