<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'AFP_STORE_URL', 'https://add-functions-php.seripoyo.work' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the download ID for the product in Easy Digital Downloads
define( 'AFP_VERSION', '1.0.0' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the download ID for the product in Easy Digital Downloads
define( 'AFP_ITEM_ID', 1078 ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the product in Easy Digital Downloads
define( 'AFP_ITEM_NAME', 'Add functions PHP Lite' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file


// the name of the settings page for the license input to be displayed
define( 'AFP_PLUGIN_BASE_NAME', 'add-functions-php-lite/add-functions-php-lite.php' );
// the name of the settings page for the license input to be displayed
define( 'AFP_PLUGIN_lite_SLUG', 'add-functions-php-lite' );

if ( ! class_exists( 'AFP_Plugin_Updater' ) ) {
	// load our custom updater
	$site_url = home_url();
	include 'AFP__Lite_Plugin_Updater.php';
}

add_action( 'admin_init', 'AFP_LITE_auto_activate_license' );
add_action( 'init', 'AFP__Lite_updater' );
add_filter(
	'pre_set_site_transient_update_plugins',
	function ( $transient ) {
		if ( isset( $transient->response[ AFP_PLUGIN_BASE_NAME ] ) ) {
			// error_log( 'プラグインの最新バージョン情報が $transient に設定されています: ' . print_r( $transient->response[ AFP_PLUGIN_BASE_NAME ], true ) );
		} else {
			// error_log( 'プラグインの最新バージョン情報が $transient に設定されていません。' );
		}
		return $transient;
	}
);
/**
 * 関数：AFP_LITE_auto_activate_license
 * 概要：ライセンスキーを自動的に有効化する
 *
 * 詳細：プラグインの初期化時にライセンスキーを自動的に有効化し、ライセンスステータスを更新する。
 **/
function AFP_LITE_auto_activate_license() {
	$license_key = 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4';

	// data to send in our API request
	$api_params = array(
		'edd_action'  => 'activate_license',
		'license'     => $license_key,
		'item_id'     => AFP_ITEM_ID,
		'item_name'   => rawurlencode( AFP_ITEM_NAME ), // the name of our product in EDD
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		AFP_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	// Check for errors in the API request
	if ( is_wp_error( $response ) ) {
		error_log( 'ライセンス認証APIリクエストエラー: ' . $response->get_error_message() );
		return;
	}

	// Parse the response
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// Check if the license activation was successful
	if ( 'valid' === $license_data->license ) {
		update_option( 'AFP_license_key', $license_key );
		update_option( 'AFP_license_status', $license_data->license );
		// error_log( 'ライセンスが正常に有効化されました。' );
	} else {
		error_log( 'ライセンスの有効化に失敗しました。エラー: ' . $license_data->error );
	}
}
add_action( 'admin_init', 'AFP_LITE_auto_activate_license' );

/**
 * AFP__Lite_updater
 * 概要：プラグインのアップデータを初期化する
 *
 * 詳細：プラグインのアップデートを管理するための設定を行う。管理者権限を持つユーザーまたはcronジョブからのみ実行される。ライセンスキーをデータベースから取得し、アップデータを初期化する。
 **/
function AFP__Lite_updater() {
	global $edd_updater;

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}
	// 無料版のアップデート関連の関数が定義されている場合は、PRO版の関数を定義しない
	if ( function_exists( 'AFP__updater' ) ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = get_option( 'AFP_license_key', 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4' );

	// setup the updater
	$edd_updater = new AFP_LITE_Plugin_Updater(
		AFP_STORE_URL,
		__FILE__,
		array(
			'version' => AFP_VERSION,                    // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => AFP_ITEM_ID,       // ID of the product
			'author'  => 'Seripoyo', // author of this plugin
			'beta'    => true,
		)
	);

	// error_log( 'AFP__Lite_updaterが初期化されました。' );
}
add_action( 'init', 'AFP__Lite_updater' );



add_filter(
	'pre_set_site_transient_update_plugins',
	function ( $transient ) {
		global $edd_updater;
		if ( $edd_updater ) {
			// error_log( 'edd_updaterが存在します。check_updateを呼び出します。' );
			return $edd_updater->check_update( $transient );
		} else {
			// error_log( 'edd_updaterが存在しません。' );
		}
		return $transient;
	}
);

add_action(
	'init',
	function () {
		// error_log( 'init アクションが呼び出されました。' );
		// error_log( '現在のページ: ' . $GLOBALS['pagenow'] );
	},
	9999
);

add_action(
	'admin_init',
	function () {
		// error_log( 'admin_init アクションが呼び出されました。' );
		// error_log( '現在のページ: ' . $GLOBALS['pagenow'] );
	},
	9999
);

add_action(
	'current_screen',
	function () {
		// error_log( 'current_screen アクションが呼び出されました。' );
		// error_log( '現在のページ: ' . $GLOBALS['pagenow'] );
	},
	9999
);

/************************************
 * 以下のコードは標準的な
 * オプションページで代用してください。
 * あなた自身のものに置き換えてください。
 *************************************/



/**
 * Adds content to the settings section.
 *
 * @return void
 */
function AFP_LITE_license_key_settings_section() {
	// esc_html_e( 'This is where you enter your license key.' );
}


/**
 * 関数：AFP_LITE_check_license
 * 概要：ライセンスキーの有効性を確認する
 *
 * 詳細：ライセンスキーを取得し、リモートサーバーにライセンス確認リクエストを送信する。ライセンスが有効な場合はライセンス情報をJSON形式で出力し、無効な場合はエラーメッセージをJSON形式で出力する。
 **/
function AFP_LITE_check_license() {

	$license_key = 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4';

	$api_params = array(
		'edd_action'  => 'check_license',
		'license'     => $license_key,
		'item_id'     => AFP_ITEM_ID,
		'item_name'   => rawurlencode( AFP_ITEM_NAME ),
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// Call the custom API.
	$response = wp_remote_post(
		AFP_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( 'valid' === $license_data->license ) {
		// ライセンスが有効な場合、JSON オブジェクトを出力
		echo wp_json_encode( $license_data );
		exit;
	} else {
		// ライセンスが無効な場合、エラーメッセージを出力
		echo wp_json_encode( array( 'error' => 'invalid_license' ) );
		exit;
	}
}
add_action( 'wp_ajax_AFP_check_license', 'AFP_LITE_check_license' );


/**
 * 関数：AFP_LITE_admin_notices
 * 概要：管理画面にライセンス関連の通知を表示する
 *
 * 詳細：ライセンス認証の結果に応じて、管理画面にメッセージを表示する。
 **/
function AFP_LITE_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch ( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'AFP_LITE_admin_notices' );

/**
 * 関数：AFP_LITE_get_version_info
 * 概要：プラグインの最新バージョン情報を取得する
 *
 * @param string - $license_key: ライセンスキー
 * @return object|false - 最新バージョン情報のオブジェクト、または取得失敗時はfalse
 *
 * 詳細：指定されたライセンスキーを使用して、プラグインの最新バージョン情報をリモートサーバーから取得する。取得に成功した場合はバージョン情報のオブジェクトを返し、失敗した場合はfalseを返す。
 **/
function AFP_LITE_get_version_info( $license_key ) {
	// error_log( 'AFP_get_version_info 関数が呼び出されました。' );

	$api_params = array(
		'edd_action'  => 'get_version',
		'license'     => $license_key,
		'item_id'     => AFP_ITEM_ID,
		'item_name'   => rawurlencode( AFP_ITEM_NAME ),
		'url'         => home_url(),
		'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	);

	// // error_log( 'APIリクエストパラメータ: ' . print_r( $api_params, true ) );

	$response = wp_remote_post(
		AFP_STORE_URL,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	if ( is_wp_error( $response ) ) {
		// error_log( 'APIリクエストエラー: ' . $response->get_error_message() );
		// error_log( 'APIリクエストエラーです' );
		return false;
	}

	// // error_log( 'APIレスポンス: ' . print_r( $response, true ) );

	$version_info = json_decode( wp_remote_retrieve_body( $response ) );

	if ( ! is_object( $version_info ) || ! isset( $version_info->new_version ) ) {
		// error_log( 'バージョン情報の取得に失敗しました。' );
		return false;
	}

	// error_log( '最新バージョン: ' . $version_info->new_version );

	return $version_info;
}



/**
 * 関数：AFP_LITE_show_update_notification
 * 概要：プラグイン一覧ページにアップデート通知を表示する
 *
 * @param array -  $plugin_data: プラグインのメタデータ
 * @param string - $plugin_file: プラグインのファイルパス
 *
 * 詳細：プラグインのファイルパスが一致し、ライセンスキーが設定されている場合、最新バージョン情報を取得する。現在のバージョンと最新バージョンを比較し、新しいバージョンが利用可能な場合にアップデート通知を表示する。
 **/
function AFP_LITE_show_update_notification( $plugin_data, $plugin_file ) {
	// error_log( 'AFP_LITE_show_update_notification 関数が呼び出されました。' );
	// error_log( '現在のページ: ' . $GLOBALS['pagenow'] );

	// プラグインファイルのパスが一致するかチェック
	if ( $plugin_file !== AFP_PLUGIN_BASE_NAME ) {
		// error_log( 'このプラグインは対象外です。プラグインファイル: ' . $plugin_file );
		return;
	}
	// error_log( 'このプラグインは対象です。プラグインファイル: ' . $plugin_file );

	// ライセンスキーが設定されているかチェック
	$license_key = trim( get_option( 'AFP_license_key' ) );
	if ( empty( $license_key ) ) {
		// error_log( 'ライセンスキーが設定されていません。' );
		return;
	}
	// error_log( 'ライセンスキーが設定されています: ' . $license_key );

	// 最新バージョン情報を取得
	$version_info = AFP_LITE_get_version_info( $license_key );
	if ( ! is_object( $version_info ) || ! isset( $version_info->new_version ) ) {
		// error_log( '最新バージョン情報が取得できませんでした。' );
		// error_log( 'バージョン情報: ' . print_r( $version_info, true ) );
		return;
	}
	// error_log( '最新バージョン情報が取得できました: ' . $version_info->new_version );

	// 現在のバージョンと最新バージョンを比較
	$current_version = AFP_VERSION;
	if ( version_compare( $current_version, $version_info->new_version, '>=' ) ) {
		// error_log( '現在のバージョンが最新です。現在のバージョン: ' . $current_version . ', 最新バージョン: ' . $version_info->new_version );
		return;
	}
	// error_log( '新しいバージョンが利用可能です。現在のバージョン: ' . $current_version . ', 最新バージョン: ' . $version_info->new_version );

	// error_log( '新しいバージョンが利用可能です。' );
}

/**
 * 関数：AFP_LITE_plugin_row_meta
 * 概要：プラグインの追加情報を表示する
 *
 * @param array -  $links: プラグインの情報リンク
 * @param string - $file: プラグインのファイルパス
 * @return array - 変更後のプラグインの情報リンク
 *
 * 詳細：プラグインのファイルパスが一致する場合、アップデート通知を表示する。
 **/
function AFP_LITE_plugin_row_meta( $links, $file ) {
	// error_log( 'AFP_LITE_plugin_row_meta 関数が呼び出されました。' );
	// プラグインファイルのパスが一致するかチェック
	if ( $file === AFP_PLUGIN_BASE_NAME ) {
		// error_log( 'このプラグインは対象です。プラグインファイル: ' . $file );
		$plugin_data = get_plugin_data( __FILE__ );
		AFP_LITE_show_update_notification( $plugin_data, $file );
	} else {
		// error_log( 'このプラグインは対象外です。プラグインファイル: ' . $file );
		// error_log( 'このプラグインは対象外です。プラグインファイルのパス: ' . AFP_PLUGIN_BASE_NAME );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'AFP_LITE_plugin_row_meta', 10, 2 );


/**
 * 関数：AFP_LITE_add_settings_link
 * 概要：プラグインのアクションリンクに設定ページのリンクを追加する
 *
 * 詳細：プラグインの管理画面でプラグインの下に表示されるアクションリンクに、設定ページへのリンクを追加する。
 **/
function AFP_LITE_add_settings_link() {
	// プラグインのベース名を取得
	$plugin_basename = 'add-functions-php-lite/add-functions-php-lite.php';

	// サイトのURLを取得
	$site_url = home_url();

	// 設定ページのURLを生成
	$settings_link = '<a href="' . esc_url( $site_url . '/wp-admin/admin.php?page=sidebar-menu-customizer' ) . '">' . __( 'Side Menu Setting', 'add-functions-php' ) . '</a>';

	// プラグインのアクションリンクに設定ページのリンクを追加
	add_filter(
		'plugin_action_links_' . $plugin_basename,
		function ( $links ) use ( $settings_link ) {
			array_unshift( $links, $settings_link );
			return $links;
		}
	);
}
add_action( 'admin_init', 'AFP_LITE_add_settings_link' );


/**
 * 関数:AFP_LITE_deactivate_license_on_plugin_deactivation
 * 概要:プラグインの無効化時にライセンスの認証を解除する
 *
 * 詳細:プラグインが無効化されたときに、ライセンスキーを使用してリモートサーバーにライセンス認証解除リクエストを送信する。
 **/
function AFP_LITE_deactivate_license_on_plugin_deactivation() {
    $license_key = trim( get_option( 'AFP_license_key' ) );

    if ( $license_key === 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4' ) {
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $license_key,
            'item_id'    => AFP_ITEM_ID,
            'item_name'  => rawurlencode( AFP_ITEM_NAME ),
            'url'        => home_url(),
        );

        $response = wp_remote_post(
            AFP_STORE_URL,
            array(
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params,
            )
        );

        if ( ! is_wp_error( $response ) ) {
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if ( 'deactivated' === $license_data->license ) {
                delete_option( 'AFP_license_status', $license_data->license );
                delete_option( 'AFP_license_key' );
            }
        }
    }
}
register_deactivation_hook( __FILE__, 'AFP_LITE_deactivate_license_on_plugin_deactivation' );
