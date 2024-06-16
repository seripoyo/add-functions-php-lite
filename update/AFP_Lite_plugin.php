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
define( 'AFP_PLUGIN_LICENSE_PAGE', 'add-functions-php-license' );

// the name of the settings page for the license input to be displayed
define( 'AFP_PLUGIN_BASE_NAME', 'add-functions-php-lite/add-functions-php-lite.php' );
// the name of the settings page for the license input to be displayed
define( 'AFP_PLUGIN_lite_SLUG', 'add-functions-php-lite' );

if ( ! class_exists( 'AFP_Plugin_Updater' ) ) {
	// load our custom updater
	$site_url = home_url();
	include 'AFP__Lite_Plugin_Updater.php';
}

add_action( 'admin_init', 'AFP_auto_activate_license' );
add_action( 'admin_init', 'AFP_set_license_key' );
add_action( 'init', 'AFP__updater' );
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
 * 関数：AFP_auto_activate_license
 * 概要：ライセンスキーを自動的に有効化する
 *
 * 詳細：プラグインの初期化時にライセンスキーを自動的に有効化し、ライセンスステータスを更新する。
 **/
function AFP_auto_activate_license() {
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
		error_log( 'ライセンスが正常に有効化されました。' );
	} else {
		error_log( 'ライセンスの有効化に失敗しました。エラー: ' . $license_data->error );
	}
}
add_action( 'admin_init', 'AFP_auto_activate_license' );

/**
 * 関数：AFP__updater
 * 概要：プラグインのアップデータを初期化する
 *
 * 詳細：プラグインのアップデートを管理するための設定を行う。管理者権限を持つユーザーまたはcronジョブからのみ実行される。ライセンスキーをデータベースから取得し、アップデータを初期化する。
 **/
function AFP__updater() {
	global $edd_updater;

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = get_option( 'AFP_license_key', 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4' );

	// setup the updater
	$edd_updater = new AFP_Plugin_Updater(
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

	error_log( 'AFP__updaterが初期化されました。' );
}
add_action( 'init', 'AFP__updater' );


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
 * Adds the plugin license page to the admin menu.
 *
 * @return void
 */
function AFP_license_page() {
	add_settings_section(
		'AFP_license',
		__( 'Plugin License', 'add-functions-php' ),
		'AFP_license_key_settings_section',
		AFP_PLUGIN_LICENSE_PAGE
	);
	add_settings_field(
		'AFP_license_key',
		'<label for="AFP_license_key">' . __( 'License Key', 'add-functions-php' ) . '</label>',
		'AFP_license_key_settings_field',
		AFP_PLUGIN_LICENSE_PAGE,
		'AFP_license'
	);
	?>
	<div class="wrap">
		
		<form method="post" action="options.php">

			<?php
			do_settings_sections( AFP_PLUGIN_LICENSE_PAGE );
			$status = get_option( 'AFP_license_status' );
			if ( 'valid' === $status ) {
				echo '<p>' . esc_html__( 'ライセンスが認証されています', 'add-functions-php' ) . '</p>';
			}
			settings_fields( 'AFP_license' );
			submit_button();
			?>

		</form>

	</div>
	<?php
}


/**
 * 関数：AFP_license_key_settings_field
 * 概要：ライセンスキー設定フィールドを表示する
 *
 * 詳細：ライセンスキーの入力フィールドを表示し、現在のライセンスキーとステータスに応じてアクティベートまたはデアクティベートボタンを表示する。
 **/
function AFP_license_key_settings_field() {
	$license_key = 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4';
	$status      = get_option( 'AFP_license_status' );

	?>
	<p class="description"><?php esc_html_e( 'Enter your license key.', 'add-functions-php' ); ?></p>
	<?php
	printf(
		'<input type="text" class="regular-text" id="AFP_license_key" name="AFP_license_key" value="%s" />',
		esc_attr( $license_key )
	);
	$button = array(
		'name'  => 'edd_license_deactivate',
		'label' => __( 'Deactivate License', 'add-functions-php' ),
	);
	if ( 'valid' !== $status ) {
		$button = array(
			'name'  => 'edd_license_activate',
			'label' => __( 'Activate License', 'add-functions-php' ),
		);
	}
	wp_nonce_field( 'AFP_nonce', 'AFP_nonce' );
	?>
	<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>"/>
	<?php
}

/**
 * 関数：AFP_register_option
 * 概要：ライセンスキーの設定をオプション表に登録する
 *
 * 詳細：ライセンスキーの設定をWordPressのオプション表に登録し、サニタイズ用のコールバック関数を指定する。
 **/
function AFP_register_option() {
	register_setting( 'AFP_license', 'AFP_license_key', 'edd_sanitize_license' );
}
add_action( 'admin_init', 'AFP_register_option' );

/**
 * 関数：edd_sanitize_license
 * 概要：ライセンスキーをサニタイズする
 *
 * @param string - $new: 新しいライセンスキー
 * @return string - サニタイズ後のライセンスキー
 *
 * 詳細：入力されたライセンスキーをサニタイズし、以前のライセンスキーと異なる場合はライセンスステータスを削除する。
 **/
function edd_sanitize_license( $new ) {
	$old = get_option( 'AFP_license_key' );
	if ( $old && $old !== $new ) {
		delete_option( 'AFP_license_status' ); // new license has been entered, so must reactivate
	}

	return sanitize_text_field( $new );
}
/**
 * Adds content to the settings section.
 *
 * @return void
 */
function AFP_license_key_settings_section() {
	// esc_html_e( 'This is where you enter your license key.' );
}
/**
 * 関数：AFP_activate_license
 * 概要：ライセンスキーを認証する
 *
 * 詳細：ライセンス認証ボタンが押された場合、ライセンスキーを取得し、リモートサーバーに認証リクエストを送信する。認証結果に応じてメッセージを表示し、ライセンスステータスを更新する。
 **/
function AFP_activate_license() {
	// listen for our activate button to be clicked
	if ( ! isset( $_POST['edd_license_activate'] ) ) {
		return;
	}

	// run a quick security check
	if ( ! check_admin_referer( 'AFP_nonce', 'AFP_nonce' ) ) {
		return; // get out if we didn't click the Activate button
	}

	// retrieve the license from the database
	$license_key = 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4';
	if ( ! $license_key ) {
		$license_key = ! empty( $_POST['AFP_license_key'] ) ? sanitize_text_field( $_POST['AFP_license_key'] ) : '';
	}
	if ( ! $license_key ) {
		return;
	}

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

	// AFP_activate_license 関数内の wp_remote_post の後に以下のコードを追加
	if ( is_wp_error( $response ) ) {
		// error_log( 'ライセンス認証APIリクエストエラー: ' . $response->get_error_message() );
	} else {
		// error_log( 'ライセンス認証APIレスポンスコード: ' . wp_remote_retrieve_response_code( $response ) );
		// error_log( 'ライセンス認証APIレスポンスボディ: ' . wp_remote_retrieve_body( $response ) );
	}

	// AFP_deactivate_license 関数内の wp_remote_post の後に以下のコードを追加
	if ( is_wp_error( $response ) ) {
		// error_log( 'ライセンス非認証APIリクエストエラー: ' . $response->get_error_message() );
	} else {
		// error_log( 'ライセンス非認証APIレスポンスコード: ' . wp_remote_retrieve_response_code( $response ) );
		// error_log( 'ライセンス非認証APIレスポンスボディ: ' . wp_remote_retrieve_body( $response ) );
	}

	// AFP_get_version_info 関数内の wp_remote_post の後に以下のコードを追加
	if ( is_wp_error( $response ) ) {
		// error_log( 'APIリクエストエラー: ' . $response->get_error_message() );
	} else {
		// error_log( 'APIレスポンスコード: ' . wp_remote_retrieve_response_code( $response ) );
		// error_log( 'APIレスポンスボディ: ' . wp_remote_retrieve_body( $response ) );
	}

		// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' );
		}
	} else {

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {

			switch ( $license_data->error ) {

				case 'expired':
					$message = sprintf(
						/* translators: the license key expiration date */
						__( 'Your license key expired on %s.', 'edd-sample-plugin', 'add-functions-php' ),
						date_i18n( get_option( 'date_format', 'add-functions-php' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled':
				case 'revoked':
					$message = __( 'Your license key has been disabled.', 'add-functions-php' );
					break;

				case 'missing':
					$message = __( 'Invalid license.', 'edd-sample-plugin' );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'add-functions-php' );
					break;

				case 'item_name_mismatch':
					/* translators: the plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'add-functions-php' ), AFP_ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'add-functions-php' );
					break;

				default:
					$message = __( 'An error occurred, please try again.', 'add-functions-php' );
					break;
			}
		}
	}

		// Check if anything passed on a message constituting a failure
	if ( ! empty( $message ) ) {
		$redirect = add_query_arg(
			array(
				'page'          => AFP_PLUGIN_LICENSE_PAGE,
				'sl_activation' => 'false',
				'message'       => rawurlencode( $message ),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );
		exit();
	}

	// $license_data->license will be either "valid" or "invalid"
	if ( 'valid' === $license_data->license ) {
		update_option( 'AFP_license_key', $license_key );
	}
	update_option( 'AFP_license_status', $license_data->license );
	wp_safe_redirect( admin_url( 'admin.php?page=' . AFP_PLUGIN_LICENSE_PAGE ) );
	exit();
}
add_action( 'admin_init', 'AFP_activate_license' );

/**
 * 関数：AFP_check_license
 * 概要：ライセンスキーの有効性を確認する
 *
 * 詳細：ライセンスキーを取得し、リモートサーバーにライセンス確認リクエストを送信する。ライセンスが有効な場合はライセンス情報をJSON形式で出力し、無効な場合はエラーメッセージをJSON形式で出力する。
 **/
function AFP_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'AFP_nonce', 'AFP_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license_key = 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4';

		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $license_key,
			'item_id'     => AFP_ITEM_ID,
			'item_name'   => rawurlencode( AFP_ITEM_NAME ),
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		$response = wp_remote_post(
			AFP_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( 'deactivated' === $license_data->license ) {
				delete_option( 'AFP_license_status' );
			}
		}

		$redirect = add_query_arg(
			array(
				'page'          => AFP_PLUGIN_LICENSE_PAGE,
				'sl_activation' => 'false',
				'message'       => rawurlencode( $message ),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );
		exit();
	}
}
add_action( 'admin_init', 'AFP_deactivate_license' );
/**
 * 関数：AFP_check_license
 * 概要：ライセンスキーの有効性を確認する
 *
 * 詳細：ライセンスキーを取得し、リモートサーバーにライセンス確認リクエストを送信する。ライセンスが有効な場合はライセンス情報をJSON形式で出力し、無効な場合はエラーメッセージをJSON形式で出力する。
 **/
function AFP_check_license() {

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
add_action( 'wp_ajax_AFP_check_license', 'AFP_check_license' );



/**
 * 関数：AFP_get_version_info
 * 概要：プラグインの最新バージョン情報を取得する
 *
 * @param string - $license_key: ライセンスキー
 * @return object|false - 最新バージョン情報のオブジェクト、または取得失敗時はfalse
 *
 * 詳細：指定されたライセンスキーを使用して、プラグインの最新バージョン情報をリモートサーバーから取得する。取得に成功した場合はバージョン情報のオブジェクトを返し、失敗した場合はfalseを返す。
 **/
function AFP_get_version_info( $license_key ) {
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

	error_log( '最新バージョン: ' . $version_info->new_version );

	return $version_info;
}



/**
 * 関数：AFP_add_settings_link
 * 概要：プラグインのアクションリンクに設定ページのリンクを追加する
 *
 * 詳細：プラグインの管理画面でプラグインの下に表示されるアクションリンクに、設定ページへのリンクを追加する。
 **/
function AFP_add_settings_link() {
	// プラグインのベース名を取得
	$plugin_basename = 'add-functions-php-lite/add-functions-php-lite.php';

	// サイトのURLを取得
	$site_url = home_url();

	// 設定ページのURLを生成
	$settings_link = '<a href="' . esc_url( $site_url . '/wp-admin/admin.php?page=sidebar-menu-customizer' ) . '">' . __( 'Settings', 'add-functions-php' ) . '</a>';

	// プラグインのアクションリンクに設定ページのリンクを追加
	add_filter(
		'plugin_action_links_' . $plugin_basename,
		function ( $links ) use ( $settings_link ) {
			array_unshift( $links, $settings_link );
			return $links;
		}
	);
}
add_action( 'admin_init', 'AFP_add_settings_link' );


/**
 * 関数：AFP_set_license_key
 * 概要：ライセンスキーをデータベースに設定する
 *
 * 詳細：プラグインの初期化時にライセンスキーをデータベースに設定する。
 **/
function AFP_set_license_key() {
	$license_key = 'S3FXSGGTFHV95QMH0TRTAZDH4X7CCJV4';
	update_option( 'AFP_license_key', $license_key );
}
add_action( 'admin_init', 'AFP_set_license_key' );