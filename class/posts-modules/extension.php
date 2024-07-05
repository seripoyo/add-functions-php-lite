<?php

namespace Add_function_PHP\Posts;

use function add_meta_box;
defined( 'ABSPATH' ) || exit;

class Posts_Extensions {



	/**
	 * 関数：auto_slug
	 * 概要：スラッグ名が日本語の時、自動的にidに変更
	 *
	 * 詳細：スラッグ名が日本語の場合、投稿タイプとIDを組み合わせた形式に変更する
	 *
	 * @param string - $slug: 変更前のスラッグ名
	 * @param int -    $post_ID: 投稿ID
	 * @param string - $post_status: 投稿ステータス
	 * @param string - $post_type: 投稿タイプ
	 * @return string - 変更後のスラッグ名
	 **/
	public function auto_slug( $slug, $post_ID, $post_status, $post_type ) {
		if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) ) {
			$slug = utf8_uri_encode( $post_type ) . '-' . $post_ID;
		}
		return $slug;
	}

	/**
	 * 関数：add_featured_image_columns
	 * 概要：投稿一覧にアイキャッチ画像の列を追加
	 *
	 * 詳細：各投稿タイプの一覧ページにアイキャッチ画像の列を追加し、アイキャッチ画像を表示する
	 **/
	public function add_featured_image_columns() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' ); // 'objects' を指定してオブジェクトのリストを取得
		foreach ( $post_types as $post_type ) {
			add_filter(
				'manage_' . $post_type->name . '_posts_columns', // $post_type->name で投稿タイプのスラッグを取得
				function ( $columns ) {
					$new_columns = array();
					$after       = 'cb'; // 'cb' is the column for checkboxes

					foreach ( $columns as $key => $title ) {
						if ( $key === $after ) {
							$new_columns[ $key ]           = $title;
							$new_columns['featured_image'] = __( 'Eyecatch Image', 'add-functions-php' );
						} else {
							$new_columns[ $key ] = $title;
						}
					}
					return $new_columns;
				}
			);

			add_action(
				'manage_' . $post_type->name . '_posts_custom_column', // $post_type->name で投稿タイプのスラッグを取得
				function ( $column, $post_id ) {
					if ( $column == 'featured_image' ) {
						$thumbnail = get_the_post_thumbnail( $post_id, 'medium', array( 'style' => 'max-width:192px;max-height:108px;width:100%;height:100%;object-fit:contain;' ) );

						if ( ! empty( $thumbnail ) ) {
							echo $thumbnail;
						} else {
							echo '<img src="' . PLUGIN_PATH . 'assets/img/no_image.webp" alt="No Image" style="max-width:192px;max-height:108px;width:100%;height:100%;object-fit:contain;" class="lazyload">';
						}
					}
				},
				10,
				2
			);
		}
	}

	/**
	 * 関数：auto_set_image_title_alt
	 * 概要：画像が追加されたら自動でタイトルとALTに画像ファイル名を追加
	 *
	 * 詳細：メディアライブラリに画像が追加された際、画像のファイル名をタイトルとALTに自動で設定する
	 *
	 * @param int - $attachment_id: 追加された画像の添付ファイルID
	 **/
	public function auto_set_image_title_alt( $attachment_id ) {
		$attachment = get_post( $attachment_id );
		if ( $attachment ) {
			$title = pathinfo( $attachment->post_title, PATHINFO_FILENAME );
			$alt   = $title;

			update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

			$attachment_data = array(
				'ID'         => $attachment_id,
				'post_title' => $title,
			);
			wp_update_post( $attachment_data );
		}
	}
	/**
	 * 関数：change_post_tag_to_checkbox
	 * 概要：投稿編集画面でのタグをチェックボックスで出力
	 *
	 * 詳細：投稿編集画面のタグ入力欄をチェックボックス形式に変更する
	 **/
	public function change_post_tag_to_checkbox() {
		$args               = get_taxonomy( 'post_tag' );
		$args->hierarchical = true; // Gutenberg用
		$args->meta_box_cb  = 'post_categories_meta_box'; // Classicエディタ用
		register_taxonomy( 'post_tag', 'post', $args );
	}
	/**
	 * 関数：add_columns_postid
	 * 概要：投稿一覧にIDの列を追加
	 *
	 * 詳細：投稿一覧ページにIDの列を追加する
	 *
	 * @param array - $columns: 投稿一覧の列情報
	 * @return array - IDの列を追加した投稿一覧の列情報
	 **/
	public function add_columns_postid( $columns ) {
		$columns['postid'] = 'ID';
		echo '';
		return $columns;
	}
	/**
	 * 関数：add_postid
	 * 概要：投稿一覧のID列に投稿IDを表示
	 *
	 * 詳細：投稿一覧ページのID列に投稿IDを表示する
	 *
	 * @param string - $column_name: 列名
	 * @param int -    $post_id: 投稿ID
	 **/
	public function add_postid( $column_name, $post_id ) {
		if ( 'postid' == $column_name ) {
			echo $post_id;
		}
	}
	/**
	 * 関数：sort_postid
	 * 概要：投稿一覧のID列をソート可能にする
	 *
	 * 詳細：投稿一覧ページのID列をソート可能な状態にする
	 *
	 * @param array - $columns: 投稿一覧の列情報
	 * @return array - ID列をソート可能にした投稿一覧の列情報
	 **/
	public function sort_postid( $columns ) {
		$columns['postid'] = 'ID';
		return $columns;
	}
	/**
	 * 関数：add_column_last_updated
	 * 概要：投稿一覧に最終更新日の列を追加
	 *
	 * 詳細：投稿一覧ページに最終更新日の列を追加する
	 *
	 * @param array - $columns: 投稿一覧の列情報
	 * @return array - 最終更新日の列を追加した投稿一覧の列情報
	 **/
	public function add_column_last_updated( $columns ) {
		$columns['last_updated'] = '最終更新日';
		return $columns;
	}
	/**
	 * 関数：add_last_updated
	 * 概要：投稿一覧の最終更新日列に更新日を表示
	 *
	 * 詳細：投稿一覧ページの最終更新日列に更新日を表示する
	 *
	 * @param string - $column_name: 列名
	 * @param int -    $post_id: 投稿ID
	 **/
	public function add_last_updated( $column_name, $post_id ) {
		if ( 'last_updated' != $column_name ) {
			return;
		}
		$modified_date = get_the_modified_date( 'Y年Md日 Ag:i', $post_id );
		echo $modified_date;
	}
	/**
	 * 関数：sort_columns_last_updated
	 * 概要：投稿一覧の最終更新日列をソート可能にする
	 *
	 * 詳細：投稿一覧ページの最終更新日列をソート可能な状態にする
	 *
	 * @param array - $columns: 投稿一覧の列情報
	 * @return array - 最終更新日列をソート可能にした投稿一覧の列情報
	 **/
	public function sort_columns_last_updated( $columns ) {
		$columns['last_updated'] = 'modified';
		return $columns;
	}
	/**
	 * 関数：add_duplicate_page
	 * 概要：ページを複製する機能を追加
	 *
	 * 詳細：ページを複製するためのアクションとフィルターを追加する
	 **/
	public function add_duplicate_page() {
		add_action( 'admin_action_duplicate_page', array( $this, 'duplicate_page' ) );
		add_filter( 'post_row_actions', array( $this, 'add_duplicate_link' ), 10, 2 );
	}
	/**
	 * 関数：duplicate_page
	 * 概要：ページを複製する処理
	 *
	 * 詳細：指定されたページを複製し、新しいページを作成する
	 **/
	public function duplicate_page() {
		global $wpdb;
		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'duplicate_page' == $_REQUEST['action'] ) ) ) {
			wp_die( 'No post to duplicate has been supplied!' );
		}

		$post_id = ( isset( $_GET['post'] ) ? $_GET['post'] : $_POST['post'] );
		$post    = get_post( $post_id );

		$new_page = array(
			'post_title'   => $post->post_title . ' (Copy)',
			'post_content' => $post->post_content,
			'post_status'  => 'draft',
			'post_date'    => current_time( 'mysql' ),
			'post_author'  => $post->post_author,
			'post_type'    => $post->post_type,
		);

		$new_page_id = wp_insert_post( $new_page );

		$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
		if ( count( $post_meta_infos ) != 0 ) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ( $post_meta_infos as $meta_info ) {
				$meta_key        = $meta_info->meta_key;
				$meta_value      = addslashes( $meta_info->meta_value );
				$sql_query_sel[] = "SELECT $new_page_id, '$meta_key', '$meta_value'";
			}
			$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
			$wpdb->query( $sql_query );
		}

		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_page_id ) );
		exit;
	}
	/**
	 * 関数：add_duplicate_link
	 * 概要：ページ一覧に複製リンクを追加
	 *
	 * 詳細：ページ一覧の各ページに複製リンクを追加する
	 *
	 * @param array -  $actions: ページの操作リンク
	 * @param object - $post: 投稿オブジェクト
	 * @return array - 複製リンクを追加したページの操作リンク
	 **/
	public function add_duplicate_link( $actions, $post ) {
		$allowed_post_types = array( 'post', 'page', 'wp_block', 'custom-css-js', 'test_customposts' );
		if ( in_array( $post->post_type, $allowed_post_types ) && current_user_can( 'edit_posts', $post->ID ) ) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=duplicate_page&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">このページを複製する</a>';
		}
		return $actions;
	}
}
