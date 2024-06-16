<?php
namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_head {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'output_additional_code' ) );
		add_action( 'wp_footer', array( $this, 'output_body_after_code' ) );
		add_action( 'wp_body_open', array( $this, 'output_body_start_code' ) );
	}
	/**
	 * 関数：disable_self_pings
	 * 概要：セルフピンバックを無効化する
	 *
	 * @param array $links - ピンバックのリンク配列
	 **/
	public function disable_self_pings( &$links ) {
		$home = home_url();
		foreach ( $links as $l => $link ) {
			if ( 0 === strpos( $link, $home ) ) {
				unset( $links[ $l ] );
			}
		}
	}

	/**
	 * 関数：output_additional_code
	 * 概要：追加のコードを出力する
	 *
	 * 詳細：<head>タグ内に設定されたコードを出力
	 **/
	public function output_additional_code() {
		$options = get_option( 'add_functions_php_settings' );

		// <head>タグ内に出力するコード
		if ( ! empty( $options['add_head'] ) ) {
			echo $options['add_head'];
		}
	}
	/**
	 * 関数：output_body_start_code
	 * 概要：<body>タグ開始後のコードを出力する
	 *
	 * 詳細：<body>タグ開始後に設定されたコードを出力
	 **/
	public function output_body_start_code() {
		$options = get_option( 'add_functions_php_settings' );

		// <body>タグ開始後に出力するコード
		if ( ! empty( $options['body_start'] ) ) {
			echo $options['body_start'];
		}
	}
	/**
	 * 関数：output_body_after_code
	 * 概要：</body>タグ終了前のコードを出力する
	 *
	 * 詳細：</body>タグ終了前に設定されたコードを出力
	 **/
	public function output_body_after_code() {
		$options = get_option( 'add_functions_php_settings' );

		// </body>タグ終了前に出力するコード
		if ( ! empty( $options['body_after'] ) ) {
			echo $options['body_after'];
		}
	}
}
