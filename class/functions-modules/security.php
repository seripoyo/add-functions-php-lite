<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_Security {

	/**
	 * WordPressのログインエラー情報を非表示にする
	 */
	public function custom_login_errors( $errors, $redirect_to ) {
		// メールアドレスが不正
		if ( isset( $errors->errors['invalid_email'] ) ) {
			$errors->remove( 'invalid_email' );
			$errors->add( 'invalid_email', 'ユーザー名 / メールアドレス、またはパスワードが違います。' );
		}

		// ユーザー名が不正
		if ( isset( $errors->errors['invalid_username'] ) ) {
			$errors->remove( 'invalid_username' );
			$errors->add( 'invalid_username', 'ユーザー名 / メールアドレス、またはパスワードが違います。' );
		}

		// パスワードが不正
		if ( isset( $errors->errors['incorrect_password'] ) ) {
			$errors->remove( 'incorrect_password' );
			$errors->add( 'incorrect_password', 'ユーザー名 / メールアドレス、またはパスワードが違います。' );
		}

		// ユーザー名が空
		if ( isset( $errors->errors['empty_username'] ) ) {
			$errors->remove( 'empty_username' );
			$errors->add( 'empty_username', 'ユーザー名、またはメールアドレスを入力してください。' );
		}

		// パスワードが空
		if ( isset( $errors->errors['empty_password'] ) ) {
			$errors->remove( 'empty_password' );
			$errors->add( 'empty_password', 'パスワードを入力してください。' );
		}

		// パスワード再設定用メール送信
		if ( isset( $errors->errors['confirm'] ) ) {
			$errors->remove( 'confirm' );
			$errors->add( 'confirm', 'パスワード再設定用のリンクをメールで送信しました。', 'message' );
		}

		return $errors;
	}
	/**
	 * 投稿者アーカイブを無効化
	 */
	public function disable_author_archive() {
		if ( preg_match( '#/author/.+#', $_SERVER['REQUEST_URI'] ) ) {
			wp_redirect( esc_url( home_url( '/404.php' ) ) );
			exit;
		}
	}
	/**
	 * ログイン試行回数を3回までに制限する
	 */
	public function limit_login_attempts() {
		// 試行回数の上限
		$max_attempts = 3;

		// ユーザーのIPアドレスを取得
		$user_ip = $_SERVER['REMOTE_ADDR'];

		// 試行回数を保存するためのトランジェント名を生成
		$transient_name = 'attempt_' . md5( $user_ip );

		// 現在の試行回数を取得（存在しない場合は0を返す）
		$attempts = (int) get_transient( $transient_name );

		if ( $attempts >= $max_attempts ) {
			// 試行回数が上限に達している場合はログインを阻止
			wp_die( 'ログイン試行回数の上限に達しました。しばらくしてから再試行してください。' );
		}
	}
	/**
	 *  ログイン施行回数を3回までにする
	 */
	public function track_login_attempts() {
		// 試行回数の上限をここで定義
		$max_attempts = 3;

		// ユーザーのIPアドレスを取得
		$user_ip = $_SERVER['REMOTE_ADDR'];

		// 試行回数を保存するためのトランジェント名を生成
		$transient_name = 'attempt_' . md5( $user_ip );

		// 現在の試行回数を取得（存在しない場合は0を返す）
		$attempts = (int) get_transient( $transient_name );

		// 試行回数を1増やす
		++$attempts;

		// 試行回数を20分間保存
		set_transient( $transient_name, $attempts, 20 * MINUTE_IN_SECONDS );

		// 残りの試行可能回数を計算
		$remaining_attempts = $max_attempts - $attempts;

		// エラーメッセージを設定
		global $error;
		$error = "ログインに失敗しました。残りの試行可能回数：{$remaining_attempts}回";
	}

	/**
	 * 日本以外からのコメント投稿を拒否する
	 */
	public function reject_non_japanese_comments( $commentdata ) {
		// IPアドレスを取得
		$ip = $_SERVER['REMOTE_ADDR'];

		// IPアドレスから国を判定するAPIのURL
		$url = "http://ip-api.com/json/{$ip}?fields=countryCode";

		// APIを使用して国コードを取得
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return $commentdata; // APIリクエストに失敗した場合は通常通り処理を続ける
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );

		// 国コードがJP以外の場合はコメントを拒否
		if ( $data->countryCode !== 'JP' ) {
			wp_die( '日本以外からのコメント投稿はお控えください。' );
		}

		return $commentdata;
	}


	/**
	 *  日本からのみログインを許可
	 */
	public function restrict_login_by_country() {
		// IPアドレスを取得
		$ip = $_SERVER['REMOTE_ADDR'];

		// IPアドレスから国を判定するAPIのURL
		$url = "http://ip-api.com/json/{$ip}?fields=countryCode";

		// APIを使用して国コードを取得
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return; // APIリクエストに失敗した場合は何もしない
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );

		// 国コードがJP以外の場合はログインページからリダイレクト
		if ( $data->countryCode !== 'JP' ) {
			wp_redirect( home_url() ); // ホームページにリダイレクト
			exit;
		}
	}
	/**
	 *  REST API（サイトURL/wp-json/wp/v2/users）でユーザー名を確認できないようにする
	 */
	public function my_filter_rest_endpoints( $endpoints ) {
		if ( isset( $endpoints['/wp/v2/users'] ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}
		if ( isset( $endpoints['/wp/v2/users/(?P[\d]+)'] ) ) {
			unset( $endpoints['/wp/v2/users/(?P[\d]+)'] );
		}
		return $endpoints;
	}
}
