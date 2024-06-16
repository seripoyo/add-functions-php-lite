<?php
namespace Add_function_PHP\Posts;

defined( 'ABSPATH' ) || exit;
/**
 *  投稿関連のselected.php
 */

class Posts_Selected {
	private $posts_class_base_dir;
	private $posts_functions_list;
	private $posts_setting;
	private $posts_extensions;


	/**
	 * 関数：__construct
	 * 概要：クラスのコンストラクタ
	 *
	 * 詳細：クラスのプロパティを初期化する
	 *
	 * @param string - $posts_class_base_dir: 投稿モジュールのベースディレクトリ
	 * @param array -  $posts_functions_list: 投稿機能のリスト
	 * @param object - $posts_setting: 投稿設定オブジェクト
	 * @param object - $posts_extensions: 投稿拡張機能オブジェクト
	 **/
	
	public function __construct( $posts_class_base_dir, $posts_functions_list, $posts_setting, $posts_extensions ) {
		$this->posts_class_base_dir = $posts_class_base_dir;
		$this->posts_functions_list = $posts_functions_list;
		$this->posts_setting        = $posts_setting;
		$this->posts_extensions     = $posts_extensions;
	}

	/**
	 * 関数：apply_selected_posts_functions
	 * 概要：選択された投稿機能を適用する
	 *
	 * 詳細：各投稿タイプに対して選択された機能を適用する
	 **/
	public function apply_selected_posts_functions() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		foreach ( $post_types as $post_type ) {
			$options                 = get_option( 'posts_functions_options_' . $post_type->name, array() );
			$posts_per_page          = $options['posts_per_page'] ?? '10';
			$category_posts_per_page = $options['category_posts_per_page'] ?? '10';
			$tag_posts_per_page      = $options['tag_posts_per_page'] ?? '10';

			// error_log('投稿タイプ: ' . $post_type->name);
			// error_log('オプションの取得: ' . print_r($options, true));
			// error_log('投稿数/ページ: ' . $posts_per_page);
			// error_log('カテゴリーアーカイブの投稿数/ページ: ' . $category_posts_per_page);
			// error_log('タグアーカイブの投稿数/ページ: ' . $tag_posts_per_page);

			// タクソノミーごとの表示投稿数を取得
			$taxonomies = get_object_taxonomies( $post_type->name, 'names' );
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomy_posts_per_page = $options[ $taxonomy . '_posts_per_page' ] ?? '10';
				update_option( 'posts_functions_options_' . $post_type->name . '_' . $taxonomy . '_posts_per_page', $taxonomy_posts_per_page );
				// error_log('タクソノミー: ' . $taxonomy);
				// error_log('タクソノミーアーカイブの投稿数/ページ: ' . $taxonomy_posts_per_page);
			}

			// アーカイブページの出力数を更新
			update_option( 'posts_functions_options_' . $post_type->name . '_posts_per_page', $posts_per_page );
			update_option( 'posts_functions_options_' . $post_type->name . '_category_posts_per_page', $category_posts_per_page );
			update_option( 'posts_functions_options_' . $post_type->name . '_tag_posts_per_page', $tag_posts_per_page );

			// archive_slugとarchive_labelが存在する場合のみ実行
			if ( isset( $options['archive_slug'] ) && isset( $options['archive_label'] ) ) {
				$archive_slug  = $options['archive_slug'];
				$archive_label = $options['archive_label'];

				add_filter( 'register_post_type_args', array( $this->posts_setting, 'post_has_archive' ), 10, 2 );
				flush_rewrite_rules();
			}

			/**
			 *  投スラッグ名が日本語の時、自動的にidに変更
			 */
			if ( isset( $options['slug_ja_to_en'] ) && $options['slug_ja_to_en'] == '1' ) {
					add_action( 'init', array( $this->posts_extensions, 'convert_japanese_slugs_to_ids' ) );
					add_filter( 'wp_unique_post_slug', array( $this->posts_extensions, 'auto_slug' ), 10, 4 );
			}
			/**
			 *  アイキャッチ画像を一覧に出力
			 */
			if ( isset( $options['show_featured_image'] ) && $options['show_featured_image'] == '1' ) {
					add_action( 'admin_init', array( $this->posts_extensions, 'add_featured_image_columns' ), 1 );
			}
			/**
			 *  一覧にID出力
			 */
			if ( isset( $options['add_post_id'] ) && $options['add_post_id'] == '1' ) {
				add_filter( 'manage_posts_columns', array( $this->posts_extensions, 'add_columns_postid' ) );
				add_filter( 'manage_pages_columns', array( $this->posts_extensions, 'add_columns_postid' ) );
				add_action( 'manage_posts_custom_column', array( $this->posts_extensions, 'add_postid' ), 10, 2 );
			}
			/**
			 *  各投稿の一覧ページに最終更新日時を出力する
			 */
			if ( isset( $options['add_last_updated'] ) && $options['add_last_updated'] === '1' ) {
				add_filter( 'manage_posts_columns', array( $this->posts_extensions, 'add_column_last_updated' ) );
				add_filter( 'manage_pages_columns', array( $this->posts_extensions, 'add_column_last_updated' ) );
				add_action( 'manage_posts_custom_column', array( $this->posts_extensions, 'add_last_updated' ), 10, 2 );
				add_action( 'manage_pages_custom_column', array( $this->posts_extensions, 'add_last_updated' ), 10, 2 );
				add_filter( 'manage_edit-post_sortable_columns', array( $this->posts_extensions, 'sort_columns_last_updated' ), 10, 2 );
				add_filter( 'manage_edit-page_sortable_columns', array( $this->posts_extensions, 'sort_columns_last_updated' ), 10, 2 );

			}
			/**
			 *  各投稿での複製ボタン機能を追加
			 */
			if ( isset( $options['duplicate_post_button'] ) && $options['duplicate_post_button'] == '1' ) {
				add_action( 'admin_action_duplicate_page', array( $this->posts_extensions, 'duplicate_page' ) );
				add_filter( 'post_row_actions', array( $this->posts_extensions, 'add_duplicate_link' ), 10, 2 );
				add_filter( 'page_row_actions', array( $this->posts_extensions, 'add_duplicate_link' ), 10, 2 );
				add_filter( 'wp_block_row_actions', array( $this->posts_extensions, 'add_duplicate_link' ), 10, 2 );
				add_filter( 'custom-css-js_row_actions', array( $this->posts_extensions, 'add_duplicate_link' ), 10, 2 );
			}

			// 画像が追加されたら自動でタイトルとALTに画像ファイル名を追加する
			if ( isset( $options['add_alt'] ) && $options['add_alt'] == '1' ) {
				add_action( 'add_attachment', array( $this->posts_extensions, 'auto_set_image_title_alt' ) );
			}
		}
	}
}
