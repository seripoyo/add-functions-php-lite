<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows plugins to use their own update API.
 *
 * @author Easy Digital Downloads
 * @version 1.9.2
 */
class AFP_LITE_Plugin_Updater {

	private $api_url     = '';
	private $api_data    = array();
	private $plugin_file = '';
	private $name        = '';
	private $slug        = '';
	private $version     = '';
	private $wp_override = false;
	private $beta        = false;
	private $failed_request_cache_key;


	/**
	 * 関数：__construct
	 * 概要：クラスのコンストラクタ（初期化処理）
	 *
	 * 詳細：プラグインのアップデート情報を管理するための各種プロパティを設定し、必要なアクションフックを登録する。
	 *
	 * @param string - $_api_url: カスタムAPIエンドポイントのURL
	 * @param string - $_plugin_file: プラグインファイルのパス
	 * @param array -  $_api_data: APIリクエストに含めるオプションのデータ
	 * @var string - $api_url: カスタムAPIエンドポイントのURL
	 * @var array - $api_data: APIリクエストに含めるデータ
	 * @var string - $plugin_file: プラグインファイルのパス
	 * @var string - $name: プラグインの名前
	 * @var string - $slug: プラグインのスラッグ（識別子）
	 * @var string - $version: プラグインのバージョン
	 * @var bool - $wp_override: WordPressのアップデート情報を上書きするかどうか
	 * @var bool - $beta: ベータ版を含めるかどうか
	 * @var string - $failed_request_cache_key: 失敗したリクエストのキャッシュキー
	 **/
	public function __construct( $_api_url, $_plugin_file, $_api_data = null ) {

		global $edd_plugin_data;

		$this->api_url                  = trailingslashit( $_api_url );
		$this->api_data                 = $_api_data;
		$this->plugin_file              = $_plugin_file;
		$this->name                     = 'add-functions-php-lite/add-functions-php-lite.php';
		$this->slug                     = 'add-functions-php-lite';
		$this->version                  = $_api_data['version'];
		$this->wp_override              = isset( $_api_data['wp_override'] ) ? (bool) $_api_data['wp_override'] : false;
		$this->beta                     = ! empty( $this->api_data['beta'] ) ? true : false;
		$this->failed_request_cache_key = 'AFP_failed_http_' . md5( $this->api_url );

		$edd_plugin_data[ $this->slug ] = $this->api_data;

		/**
		 * Fires after the $edd_plugin_data is setup.
		 *
		 * @since x.x.x
		 *
		 * @param array $edd_plugin_data Array of EDD SL plugin data.
		 */
		do_action( 'post_AFP_plugin_updater_setup', $edd_plugin_data );

		// Set up hooks.
		$this->init();
	}

	/**
	 * 関数：init
	 * 概要：WordPressのアップデートプロセスにフックを設定
	 *
	 * 詳細：プラグインのアップデートチェック、プラグイン情報の取得、アップデート通知の表示、変更ログの表示などのアクションフックを登録する。
	 *
	 * @return void
	 **/
	public function init() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_action( 'after_plugin_row', array( $this, 'show_update_notification' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
		add_action( 'admin_init', array( $this, 'force_check_update' ) ); // 追加
	}
	/**
	 * 関数：force_check_update
	 * 概要：プラグインページでの強制アップデートチェック
	 *
	 * 詳細：プラグインページにアクセスした際に、プラグインのアップデート情報を強制的にチェックし、更新する。
	 **/
	public function force_check_update() {
		global $pagenow;

		if ( 'plugins.php' === $pagenow ) {
			// error_log( 'force_check_update メソッドが呼び出されました。' );
			$transient = get_site_transient( 'update_plugins' );
			$transient = $this->check_update( $transient );
			set_site_transient( 'update_plugins', $transient );
		}
	}
	/**
	 * 関数：check_update
	 * 概要：アップデートチェックを行い、アップデート情報を変更
	 *
	 * 詳細：WordPressのアップデート情報配列を受け取り、カスタムAPIエンドポイントからプラグインの最新情報を取得して、アップデート情報を変更する。
	 *
	 * @param object - $_transient_data: WordPressのアップデート情報配列
	 * @return object - 変更後のアップデート情報配列
	 **/
	public function check_update( $_transient_data ) {
		global $pagenow;

		// error_log( 'check_update メソッドが呼び出されました。' );
		// error_log( '現在のページ: ' . $pagenow );

		if ( ! is_object( $_transient_data ) ) {
			// error_log( '$_transient_data はオブジェクトではありません。新しい stdClass オブジェクトを作成します。' );
			$_transient_data = new stdClass();
		} else {
			// error_log( '$_transient_data はオブジェクトです。' );
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			// error_log( '$_transient_data->response が存在し、このプラグインの情報が含まれており、wp_override が false です。早期リターンします。' );
			return $_transient_data;
		}

		// error_log( 'get_version_from_remote 関数を呼び出します。' );
		$version_info = $this->get_version_from_remote();

		if ( false === $version_info ) {
			// error_log( 'get_version_from_remote 関数が false を返しました。' );
		} else {
			// error_log( 'get_version_from_remote 関数が以下の情報を返しました: ' . print_r( $version_info, true ) );
		}

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
			// error_log( '新しいバージョン情報が取得できました。現在のバージョンと比較します。' );
			// error_log( '現在のバージョン: ' . $this->version );
			// error_log( '新しいバージョン: ' . $version_info->new_version );

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
				// error_log( '新しいバージョンが利用可能です。$_transient_data->response を更新します。' );
				$_transient_data->response[ $this->name ] = (object) array(
					'id'          => $this->name,
					'slug'        => $this->slug,
					'plugin'      => $this->name,
					'new_version' => $version_info->new_version,
					'package'     => $version_info->package,
					'url'         => isset( $version_info->url ) ? $version_info->url : '', // 修正箇所
				);
			} else {
				// error_log( '現在のバージョンが最新です。$_transient_data->no_update を更新します。' );
				$_transient_data->no_update[ $this->name ] = (object) array(
					'id'          => $this->name,
					'slug'        => $this->slug,
					'plugin'      => $this->name,
					'new_version' => $version_info->new_version,
					'url'         => isset( $version_info->url ) ? $version_info->url : '', // 修正箇所
				);
			}
		} else {
			error_log( '新しいバージョン情報が取得できませんでした。' );
		}

		$_transient_data->last_checked           = time();
		$_transient_data->checked[ $this->name ] = $this->version;

		// error_log( '最終的な $_transient_data の内容: ' . print_r( $_transient_data, true ) );

		return $_transient_data;
	}
	/**
	 * 関数：get_repo_api_data
	 * 概要：リポジトリのAPIデータを取得し、キャッシュに保存
	 *
	 * @return object - リポジトリのAPIデータ
	 **/
	public function get_repo_api_data() {
		$version_info = $this->get_cached_version_info();

		if ( false === $version_info ) {
			$version_info = $this->api_request(
				'plugin_latest_version',
				array(
					'slug' => $this->slug,
					'beta' => $this->beta,
				)
			);
			if ( ! $version_info ) {
				return false;
			}

			// This is required for your plugin to support auto-updates in WordPress 5.5.
			$version_info->plugin = $this->name;
			$version_info->id     = $this->name;
			$version_info->tested = $this->get_tested_version( $version_info );

			$this->set_version_info_cache( $version_info );
		}

		return $version_info;
	}

	/**
	 * 関数：get_tested_version
	 * 概要：プラグインのテスト済みバージョンを取得
	 *
	 * @param object - $version_info: バージョン情報オブジェクト
	 * @return string|null - テスト済みバージョン、または取得できない場合はnull
	 **/
	private function get_tested_version( $version_info ) {

		// There is no tested version.
		if ( empty( $version_info->tested ) ) {
			return null;
		}

		// Strip off extra version data so the result is x.y or x.y.z.
		list( $current_wp_version ) = explode( '-', get_bloginfo( 'version' ) );

		// The tested version is greater than or equal to the current WP version, no need to do anything.
		if ( version_compare( $version_info->tested, $current_wp_version, '>=' ) ) {
			return $version_info->tested;
		}
		$current_version_parts = explode( '.', $current_wp_version );
		$tested_parts          = explode( '.', $version_info->tested );

		// The current WordPress version is x.y.z, so update the tested version to match it.
		if ( isset( $current_version_parts[2] ) && $current_version_parts[0] === $tested_parts[0] && $current_version_parts[1] === $tested_parts[1] ) {
			$tested_parts[2] = $current_version_parts[2];
		}

		return implode( '.', $tested_parts );
	}

	/**
	 * プラグインの更新通知をマルチサイトのサブサイトに表示する関数
	 *
	 * @param string $file プラグインファイルのパス
	 * @param array  $plugin プラグインの情報
	 */
	public function show_update_notification( $file, $plugin ) {

		// ネットワーク管理画面にいる場合、またはマルチサイトインストールでない場合は早期に終了
		if ( is_network_admin() || ! is_multisite() ) {
			return;
		}

		// シングルサイトの管理者が更新が利用可能であることを確認できるようにする
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// 現在のプラグインファイルがこのプラグインでない場合は早期に終了
		if ( $this->name !== $file ) {
			return;
		}

		// 更新が存在しない場合はメッセージを表示しない
		$update_cache = get_site_transient( 'update_plugins' );

		// 更新キャッシュがオブジェクトでない場合、新しいオブジェクトを作成
		if ( ! isset( $update_cache->response[ $this->name ] ) ) {
			if ( ! is_object( $update_cache ) ) {
				$update_cache = new stdClass();
			}
			// リポジトリから最新のプラグインデータを取得してキャッシュに保存
			$update_cache->response[ $this->name ] = $this->get_repo_api_data();
		}

		// プラグインが更新キャッシュに存在しない場合、または現在のバージョンが最新バージョン以上の場合は早期に終了
		if ( empty( $update_cache->response[ $this->name ] ) || version_compare( $this->version, $update_cache->response[ $this->name ]->new_version, '>=' ) ) {
			return;
		}

		// 更新通知のHTMLを出力
		printf(
			'<tr class="plugin-update-tr %3$s" id="%1$s-update" data-slug="%1$s" data-plugin="%2$s">',
			$this->slug,
			$file,
			in_array( $this->name, $this->get_active_plugins(), true ) ? 'active' : 'inactive'
		);

		echo '<td colspan="3" class="plugin-update colspanchange">';
		echo '<div class="update-message notice inline notice-warning notice-alt"><p>';

		// 変更ログのリンクを生成
		$changelog_link = '';
		if ( ! empty( $update_cache->response[ $this->name ]->sections->changelog ) ) {
			$changelog_link = add_query_arg(
				array(
					'edd_sl_action' => 'view_plugin_changelog',
					'plugin'        => urlencode( $this->name ),
					'slug'          => urlencode( $this->slug ),
					'TB_iframe'     => 'true',
					'width'         => 77,
					'height'        => 911,
				),
				self_admin_url( 'index.php' )
			);
		}

		// 更新リンクを生成
		$update_link = add_query_arg(
			array(
				'action' => 'upgrade-plugin',
				'plugin' => urlencode( $this->name ),
			),
			self_admin_url( 'update.php' )
		);

		// 更新通知メッセージを表示
		printf(
		/* translators: the plugin name. */
			esc_html__( 'There is a new version of %1$s available.', 'easy-digital-downloads' ),
			esc_html( $plugin['Name'] )
		);

		// プラグインの更新権限がない場合のメッセージ
		if ( ! current_user_can( 'update_plugins' ) ) {
			echo ' ';
			esc_html_e( 'Contact your network administrator to install the update.', 'easy-digital-downloads' );
		} elseif ( empty( $update_cache->response[ $this->name ]->package ) && ! empty( $changelog_link ) ) {
			// 更新パッケージがない場合のメッセージ
			echo ' ';
			printf(
			/* translators: 1. opening anchor tag, do not translate 2. the new plugin version 3. closing anchor tag, do not translate. */
				__( '%1$sView version %2$s details%3$s.', 'easy-digital-downloads' ),
				'<a target="_blank" class="thickbox open-plugin-details-modal" href="' . esc_url( $changelog_link ) . '">',
				esc_html( $update_cache->response[ $this->name ]->new_version ),
				'</a>'
			);
		} elseif ( ! empty( $changelog_link ) ) {
			// 更新パッケージがある場合のメッセージ
			echo ' ';
			printf(
				__( '%1$sView version %2$s details%3$s or %4$supdate now%5$s.', 'easy-digital-downloads' ),
				'<a target="_blank" class="thickbox open-plugin-details-modal" href="' . esc_url( $changelog_link ) . '">',
				esc_html( $update_cache->response[ $this->name ]->new_version ),
				'</a>',
				'<a target="_blank" class="update-link" href="' . esc_url( wp_nonce_url( $update_link, 'upgrade-plugin_' . $file ) ) . '">',
				'</a>'
			);
		} else {
			// 更新リンクのみのメッセージ
			printf(
				' %1$s%2$s%3$s',
				'<a target="_blank" class="update-link" href="' . esc_url( wp_nonce_url( $update_link, 'upgrade-plugin_' . $file ) ) . '">',
				esc_html__( 'Update now.', 'easy-digital-downloads' ),
				'</a>'
			);
		}

		// プラグイン更新メッセージのフックを実行
		do_action( "in_plugin_update_message-{$file}", $plugin, $plugin );

		echo '</p></div></td></tr>';
	}

	/**
	 * 関数：get_active_plugins
	 * 概要：マルチサイトネットワークでアクティブなプラグインを取得
	 *
	 * @return array - アクティブなプラグインの配列
	 **/
	private function get_active_plugins() {
		$active_plugins         = (array) get_option( 'active_plugins' );
		$active_network_plugins = (array) get_site_option( 'active_sitewide_plugins' );

		return array_merge( $active_plugins, array_keys( $active_network_plugins ) );
	}

/**
 * 関数：plugins_api_filter
 * 概要：プラグイン情報ページのデータをカスタマイズ
 *
 * @param mixed -  $_data: プラグイン情報データ
 * @param string - $_action: リクエストされたアクション
 * @param object - $_args: リクエストの追加引数
 * @return object - カスタマイズされたプラグイン情報データ
 **/
public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

    if ( 'plugin_information' !== $_action ) {
        return $_data;
    }

    if ( ! isset( $_args->slug ) || ( $_args->slug !== $this->slug ) ) {
        return $_data;
    }

    // Ensure $_data is an object before assigning properties
    if ( ! is_object( $_data ) ) {
        $_data = new stdClass();
    }

    if ( ! isset( $_data->plugin ) ) {
        $_data->plugin = $this->name;
    }

    $to_send = array(
        'slug'   => $this->slug,
        'is_ssl' => is_ssl(),
        'fields' => array(
            'banners' => array(),
            'reviews' => false,
            'icons'   => array(),
        ),
    );

    // Get the transient where we store the api request for this plugin for 24 hours
    $edd_api_request_transient = $this->get_cached_version_info();

    // プラグインが無効化されたときにライセンス情報を空にする
    if ( ! is_plugin_active( 'add-functions-php-lite/add-functions-php-lite.php' ) ) {
        $_data->sections     = array();
        $_data->banners      = array();
        $_data->icons        = array();
        $_data->contributors = array();
    }

    // If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
    if ( empty( $edd_api_request_transient ) ) {
        $api_response = $this->api_request( 'plugin_information', $to_send );

        // Expires in 3 hours
        $this->set_version_info_cache( $api_response );

        if ( false !== $api_response ) {
            $_data = $api_response;
        }
    } else {
        $_data = $edd_api_request_transient;
    }

    // Convert sections into an associative array, since we're getting an object, but Core expects an array.
    if ( isset( $_data->sections ) && ! is_array( $_data->sections ) ) {
        $_data->sections = $this->convert_object_to_array( $_data->sections );
    } else {
        $_data->sections = array();
    }

    // Convert banners into an associative array, since we're getting an object, but Core expects an array.
    if ( isset( $_data->banners ) && ! is_array( $_data->banners ) ) {
        $_data->banners = $this->convert_object_to_array( $_data->banners );
    }

    // Convert icons into an associative array, since we're getting an object, but Core expects an array.
    if ( isset( $_data->icons ) && ! is_array( $_data->icons ) ) {
        $_data->icons = $this->convert_object_to_array( $_data->icons );
    }

    // Convert contributors into an associative array, since we're getting an object, but Core expects an array.
    if ( isset( $_data->contributors ) && ! is_array( $_data->contributors ) ) {
        $_data->contributors = $this->convert_object_to_array( $_data->contributors );
    }

    if ( ! isset( $_data->plugin ) ) {
        $_data->plugin = $this->name;
    }
   if ( ! isset( $_data->name ) ) {
        $_data->name = AFP_ITEM_NAME;
    }
	    if ( ! isset( $_data->plugin ) ) {
        $_data->plugin = $this->name;
    }
    // Add the current version if it's not set
    if ( ! isset( $_data->version ) ) {
        $_data->version = $this->version;
    }

    return $_data;
}

	/**
	 * 関数：convert_object_to_array
	 * 概要：オブジェクトを連想配列に変換
	 *
	 * @param object - $data: 変換対象のオブジェクト
	 * @return array - 変換後の連想配列
	 **/
	private function convert_object_to_array( $data ) {
		if ( ! is_array( $data ) && ! is_object( $data ) ) {
			return array();
		}
		$new_data = array();
		foreach ( $data as $key => $value ) {
			$new_data[ $key ] = is_object( $value ) ? $this->convert_object_to_array( $value ) : $value;
		}

		return $new_data;
	}

	/**
	 * 関数：http_request_args
	 * 概要：SSL検証を無効にしてダウンロード失敗を防ぐ
	 *
	 * @param array -  $args: HTTPリクエストの引数
	 * @param string - $url: リクエストURL
	 * @return array - 変更後のHTTPリクエストの引数
	 **/
	public function http_request_args( $args, $url ) {

		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
			$args['sslverify'] = $this->verify_ssl();
		}
		return $args;
	}

	/**
	 * 関数：api_request
	 * 概要：APIリクエストを送信し、成功した場合はAPIからのデータを返す
	 *
	 * @param string - $_action: リクエストするアクション
	 * @param array -  $_data: APIアクションのパラメータ
	 * @return object|false - APIからのデータ、または失敗した場合はfalse
	 **/
	private function api_request( $_action, $_data ) {
		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] !== $this->slug ) {
			return;
		}

		// Don't allow a plugin to ping itself
		if ( trailingslashit( home_url() ) === $this->api_url ) {
			return false;
		}

		if ( $this->request_recently_failed() ) {
			return false;
		}

		return $this->get_version_from_remote();
	}

	/**
	 * 関数：request_recently_failed
	 * 概要：最近のリクエストが失敗したかどうかを判定
	 *
	 * @return bool - 最近のリクエストが失敗した場合はtrue、そうでない場合はfalse
	 **/
	private function request_recently_failed() {
		$failed_request_details = get_option( $this->failed_request_cache_key );

		// Request has never failed.
		if ( empty( $failed_request_details ) || ! is_numeric( $failed_request_details ) ) {
			return false;
		}

		/*
		 * Request previously failed, but the timeout has expired.
		 * This means we're allowed to try again.
		 */
		if ( time() > $failed_request_details ) {
			delete_option( $this->failed_request_cache_key );

			return false;
		}

		return true;
	}

	/**
	 * 関数：log_failed_request
	 * 概要：失敗したHTTPリクエストをログに記録
	 *
	 * 詳細：このAPIURLに対する今後1時間のAPIリクエストを防ぐために、失敗したリクエストのタイムスタンプを設定する。
	 **/
	private function log_failed_request() {
		update_option( $this->failed_request_cache_key, strtotime( '+1 hour' ) );
	}

	/**
	 * 関数：show_changelog
	 * 概要：マルチサイトインストールでの変更ログを表示
	 **/
	public function show_changelog() {

		if ( empty( $_REQUEST['AFP_action'] ) || 'view_plugin_changelog' !== $_REQUEST['AFP_action'] ) {
			return;
		}

		if ( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['slug'] ) || $this->slug !== $_REQUEST['slug'] ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to install plugin updates', 'add-functions-php' ), esc_html__( 'Error', 'add-functions-php' ), array( 'response' => 403 ) );
		}

		$version_info = $this->get_repo_api_data();
		if ( isset( $version_info->sections ) ) {
			$sections = $this->convert_object_to_array( $version_info->sections );
			if ( ! empty( $sections['changelog'] ) ) {
				echo '<div style="background:#fff;padding:10px;">' . wp_kses_post( $sections['changelog'] ) . '</div>';
			}
		}

		exit;
	}

	/**
	 * 関数：get_version_from_remote
	 * 概要：リモートサイトから現在のバージョン情報を取得
	 *
	 * @return array|false - バージョン情報の配列、または取得失敗時はfalse
	 **/
	private function get_version_from_remote() {
		$api_params = array(
			'edd_action'  => 'get_version',
			'license'     => ! empty( $this->api_data['license'] ) ? $this->api_data['license'] : '',
			'item_name'   => isset( $this->api_data['item_name'] ) ? $this->api_data['item_name'] : false,
			'item_id'     => isset( $this->api_data['item_id'] ) ? $this->api_data['item_id'] : false,
			'version'     => isset( $this->api_data['version'] ) ? $this->api_data['version'] : false,
			'slug'        => $this->slug,
			'author'      => $this->api_data['author'],
			'url'         => home_url(),
			'beta'        => $this->beta,
			'php_version' => phpversion(),
			'wp_version'  => get_bloginfo( 'version' ),
		);
		// error_log( 'APIリクエストパラメータ: ' . print_r( $api_params, true ) );

		$request = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $this->verify_ssl(),
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $request ) ) {
			$this->log_failed_request();
			error_log( 'APIリクエストエラー: ' . $request->get_error_message() );
			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $request );
		if ( 200 !== $response_code ) {
			error_log( 'APIリクエストの応答コードが200ではありません。応答コード: ' . $response_code );
			$this->log_failed_request();
			return false;
		}

		$response_body = wp_remote_retrieve_body( $request );
		// error_log( 'APIレスポンスボディ: ' . $response_body );

		$version_info = json_decode( $response_body );

		if ( ! is_object( $version_info ) || ! isset( $version_info->new_version ) ) {
			error_log( 'バージョン情報の取得に失敗しました。' );
			return false;
		}

		if ( isset( $version_info->sections ) ) {
			$version_info->sections = maybe_unserialize( $version_info->sections );
		}

		if ( isset( $version_info->banners ) ) {
			$version_info->banners = maybe_unserialize( $version_info->banners );
		}

		if ( isset( $version_info->icons ) ) {
			$version_info->icons = maybe_unserialize( $version_info->icons );
		}

		if ( isset( $version_info->package ) ) {
			// error_log( '更新パッケージのURL: ' . $version_info->package );

			// 更新パッケージのURLが正しいかどうかを確認
			$package_url_check = wp_remote_get(
				$version_info->package,
				array(
					'timeout'   => 15,
					'sslverify' => $this->verify_ssl(),
				)
			);
			if ( is_wp_error( $package_url_check ) ) {
				error_log( '更新パッケージのURLが無効です。エラー: ' . $package_url_check->get_error_message() );
			} elseif ( 200 !== wp_remote_retrieve_response_code( $package_url_check ) ) {
				error_log( '更新パッケージのURLが無効です。応答コード: ' . wp_remote_retrieve_response_code( $package_url_check ) );
			} else {
				// error_log( '更新パッケージのURLは有効です。' );
			}
		} else {
			error_log( 'APIレスポンスの更新パッケージが見つかりませんでした。' );
		}

		return $version_info;
	}

	/**
	 * 関数：get_cached_version_info
	 * 概要：キャッシュからバージョン情報を取得
	 *
	 * @param string - $cache_key: キャッシュキー
	 * @return object - バージョン情報オブジェクト
	 **/
	public function get_cached_version_info( $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->get_cache_key();
		}

		$cache = get_option( $cache_key );

		// Cache is expired
		if ( empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
			return false;
		}

		// We need to turn the icons into an array, thanks to WP Core forcing these into an object at some point.
		$cache['value'] = json_decode( $cache['value'] );
		if ( ! empty( $cache['value']->icons ) ) {
			$cache['value']->icons = (array) $cache['value']->icons;
		}

		return $cache['value'];
	}

	/**
	 * 関数：set_version_info_cache
	 * 概要：バージョン情報をデータベースに追加
	 *
	 * @param string - $value: バージョン情報の値
	 * @param string - $cache_key: キャッシュキー
	 **/
	public function set_version_info_cache( $value = '', $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->get_cache_key();
		}

		$data = array(
			'timeout' => strtotime( '+3 hours', time() ),
			'value'   => wp_json_encode( $value ),
		);

		update_option( $cache_key, $data, 'no' );

		// Delete the duplicate option
		delete_option( 'edd_api_request_' . md5( serialize( $this->slug . $this->api_data['license'] . $this->beta ) ) );
	}

	/**
	 * 関数：verify_ssl
	 * 概要：ストアのSSLを検証するかどうかを返す
	 *
	 * @return bool - SSL検証が有効な場合はtrue、そうでない場合はfalse
	 **/
	private function verify_ssl() {
		return (bool) apply_filters( 'AFP_api_request_verify_ssl', true, $this );
	}

	/**
	 * 関数：get_cache_key
	 * 概要：プラグインのユニークキー（オプション名）を取得
	 *
	 * @return string - ユニークキー
	 **/
	private function get_cache_key() {
		$string = $this->slug . $this->api_data['license'] . $this->beta;

		return 'AFP_' . md5( serialize( $string ) );
	}
}
add_filter(
	'pre_set_site_transient_update_plugins',
	function ( $transient ) {
		global $edd_updater;
		return $edd_updater->check_update( $transient );
	}
);
