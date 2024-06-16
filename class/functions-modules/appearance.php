<?php

namespace Add_function_PHP\Functions;

defined( 'ABSPATH' ) || exit;

class Functions_Admin_Color {

	private $Add_functions_php_Settings;
	private $admin_appearance;
	/**
	 * 関数：__construct
	 * 概要：クラスのコンストラクタ
	 *
	 * 詳細：管理画面の外観に関するアクションとフィルターを追加
	 *
	 * @var object - $admin_appearance: 管理画面の外観設定オブジェクト
	 **/
	public function __construct( $admin_functions ) {
		$this->admin_appearance = $admin_functions;
		add_action( 'admin_head', array( $this, 'apply_admin_font' ) );
		add_action( 'admin_head', array( $this, 'apply_admin_colors' ) );
		// add_action('login_head', array($this, 'apply_login_logo'));
		add_filter( 'login_headerurl', array( $this, 'apply_login_logo_url' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_uploader' ) );
	}
	/**
	 * 関数：render_admin_bg_color_field
	 * 概要：管理画面の背景色設定フィールドを出力する
	 *
	 * 詳細：管理画面の背景色設定フィールドを出力し、現在の設定値を表示
	 **/
	public function render_admin_bg_color_field() {
		$options        = get_option( 'add_functions_php_settings' );
		$admin_bg_color = isset( $options['admin_bg_color'] ) ? $options['admin_bg_color'] : '#f0f0f1';
		echo '<input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[admin_bg_color]" value="' . esc_attr( $admin_bg_color ) . '" />';
	}
	/**
	 * 関数：render_admin_text_color_field
	 * 概要：管理画面のテキスト色設定フィールドを出力する
	 *
	 * 詳細：管理画面のテキスト色設定フィールドを出力し、現在の設定値を表示
	 **/
	public function render_admin_text_color_field() {
		$options          = get_option( 'add_functions_php_settings' );
		$admin_text_color = isset( $options['admin_text_color'] ) ? $options['admin_text_color'] : '#001523';
		echo '<input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[admin_text_color]" value="' . esc_attr( $admin_text_color ) . '" />';
	}
	/**
	 * 関数：render_admin_menu_bg_color_field
	 * 概要：管理画面のメニュー背景色設定フィールドを出力する
	 *
	 * 詳細：管理画面のメニュー背景色設定フィールドを出力し、現在の設定値を表示
	 **/
	public function render_admin_menu_bg_color_field() {
		$options             = get_option( 'add_functions_php_settings' );
		$admin_menu_bg_color = isset( $options['admin_menu_bg_color'] ) ? $options['admin_menu_bg_color'] : '#1d2327';
		echo '<input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[admin_menu_bg_color]" value="' . esc_attr( $admin_menu_bg_color ) . '" />';
	}
	/**
	 * 関数：render_admin_submenu_bg_color_field
	 * 概要：管理画面のサブメニュー背景色設定フィールドを出力する
	 *
	 * 詳細：管理画面のサブメニュー背景色設定フィールドを出力し、現在の設定値を表示
	 **/
	public function render_admin_submenu_bg_color_field() {
		$options                = get_option( 'add_functions_php_settings' );
		$admin_submenu_bg_color = isset( $options['admin_submenu_bg_color'] ) ? $options['admin_submenu_bg_color'] : '#9eafb0';
		echo '<input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[admin_submenu_bg_color]" value="' . esc_attr( $admin_submenu_bg_color ) . '" />';
	}
	/**
	 * 関数：render_admin_menu_text_color_field
	 * 概要：管理画面のメニューテキスト色設定フィールドを出力する
	 *
	 * 詳細：管理画面のメニューテキスト色設定フィールドを出力し、現在の設定値を表示
	 **/
	public function render_admin_menu_text_color_field() {
		$options               = get_option( 'add_functions_php_settings' );
		$admin_menu_text_color = isset( $options['admin_menu_text_color'] ) ? $options['admin_menu_text_color'] : '#FFF';
		echo '<input type="hidden" class="alpha-color-picker" name="add_functions_php_settings[admin_menu_text_color]" value="' . esc_attr( $admin_menu_text_color ) . '" />';
	}

	/**
	 * 関数：enqueue_media_uploader
	 * 概要：メディアアップローダーのスクリプトとスタイルをエンキューする
	 *
	 * 詳細：管理画面でのみメディアアップローダーのスクリプトとスタイルをエンキューする
	 **/
	public function enqueue_media_uploader() {
		// 管理画面でのみメディアアップローダーのスクリプトとスタイルをエンキューする
		if ( is_admin() ) {
			wp_enqueue_media();
		}
	}
	/**
	 * 関数：apply_login_logo_url
	 * 概要：ログインページのロゴクリック時の遷移先を変更する
	 *
	 * @return string - サイトのURLを返す
	 **/
	public function apply_login_logo_url() {
		return get_bloginfo( 'url' );
	}
	public function sanitize_color( $color ) {
		if ( preg_match( '/^#[a-f0-9]{6}$/i', $color ) ) {
			return $color;
		}

		if ( preg_match( '/^rgba\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*[0-1](\.\d+)?\s*\)$/i', $color ) ) {
			return $color;
		}

		return '';
	}
	/**
	 * 関数：apply_admin_colors
	 * 概要：管理画面のカラーを適用する
	 *
	 * 詳細：設定されたカラーを管理画面のCSSに反映する
	 **/
	public function apply_admin_colors() {

		$options = get_option( 'add_functions_php_settings' );
		// error_log('Options in apply_admin_colors: ' . print_r($options, true));
		$admin_bg_color         = isset( $options['admin_bg_color'] ) ? $options['admin_bg_color'] : '#f0f0f1';
		$admin_text_color       = isset( $options['admin_text_color'] ) ? $options['admin_text_color'] : '#001523';
		$admin_menu_bg_color    = isset( $options['admin_menu_bg_color'] ) ? $options['admin_menu_bg_color'] : '#000000';
		$admin_submenu_bg_color = isset( $options['admin_submenu_bg_color'] ) ? $options['admin_submenu_bg_color'] : '#000000';
		$admin_menu_text_color  = isset( $options['admin_menu_text_color'] ) ? $options['admin_menu_text_color'] : '#FFF';
		// Color lightening function
		function lighten_color( $color, $percent ) {
			$color  = trim( $color, '#' );
			$length = strlen( $color );
			$rgb    = '';
			for ( $i = 0; $i < $length; $i += $length / 3 ) {
				$singleColor = hexdec( substr( $color, $i, $length / 3 ) );
				$singleColor = min( 255, intval( $singleColor * ( 100 + $percent ) / 100 ) );
				$rgb        .= str_pad( dechex( $singleColor ), 2, '0', STR_PAD_LEFT );
			}
			return '#' . $rgb;
		}

		// Color darkening function
		function darken_color( $color, $percent ) {
			$color  = trim( $color, '#' );
			$length = strlen( $color );
			$rgb    = '';
			for ( $i = 0; $i < $length; $i += $length / 3 ) {
				$singleColor = hexdec( substr( $color, $i, $length / 3 ) );
				$singleColor = max( 0, intval( $singleColor * ( 100 - $percent ) / 100 ) );
				$rgb        .= str_pad( dechex( $singleColor ), 2, '0', STR_PAD_LEFT );
			}
			return '#' . $rgb;
		}

		// Apply the lightening function
		$admin_submenu_bg_color = lighten_color( $admin_menu_bg_color, 65 );
		// Apply the darkening function
		$admin_menu_hover_bg_color = darken_color( $admin_menu_bg_color, 40 );

		echo '<style type="text/css">';
		echo '#wpwrap { background-color: ' . $admin_bg_color . '; }';
		echo '#wpadminbar .ab-top-menu>li.hover>.ab-item, #wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus, #wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item, #wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus,#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,#adminmenu li.menu-top:hover, #adminmenu li.opensub>a.menu-top, #adminmenu li>a.menu-top:focus { background-color: ' . $admin_menu_hover_bg_color . '; }';
		echo '#adminmenu .wp-submenu li:hover, #adminmenu a:hover,#wpadminbar .quicklinks .menupop ul li:hover { background-color: ' . $admin_menu_hover_bg_color . '; }';  // Apply the darkened color
		// echo '#wpcontent p, #wpcontent a, #wpcontent span, #wpcontent div, #wpcontent dl, #wpcontent dt, #wpcontent ul, #wpcontent li, #wpcontent form, #wpcontent table { color: ' . $admin_text_color . '; }';
		echo '#wpadminbar, #adminmenuwrap, #adminmenu, #adminmenuback { background-color: ' . $admin_menu_bg_color . '; }';
		echo '#adminmenu ul.wp-submenu.wp-submenu-wrap, #wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus, #wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item, #wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus, #wpadminbar .menupop .ab-sub-wrapper { background-color: ' . $admin_submenu_bg_color . '; }';
		echo '#wpcontent #wpadminbar .quicklinks ul.ab-top-menu li.menupop:hover a.ab-item span,#wpcontent #wpadminbar .quicklinks .menupop .ab-sub-wrapper ul.ab-submenu li:hover a.ab-item,#adminmenu .wp-has-current-submenu ul>li>a, .wp-menu-name, a.ab-item, #wpcontent span.text, #wpcontent span.display-name,#wpcontent div.wp-core-ui.wp-ui-notification { color: ' . $admin_menu_text_color . '; }';
		echo '#wpadminbar .ab-icon.-arkhe svg { fill: ' . $admin_menu_text_color . ' !important ; }';
		echo '</style>';
	}
	/**
	 * 関数：get_font_options
	 * 概要：フォントオプションの配列を取得する
	 *
	 * @return array - フォントオプションの配列
	 **/
	public function get_font_options() {
		return array(
			'default'          => __( 'default', 'add-functions_php' ),
			'Noto Sans JP'     => __( 'Noto Sans：sample', 'add-functions_php' ),
			'Noto Serif JP'    => __( 'Noto Serif JP：sample', 'add-functions_php' ),
			'Rounded_Mplus_1p' => __( 'Rounded M+ 1p：sample', 'add-functions_php' ),
			'logo_type_gothic' => __( 'logo type gothic：sample', 'add-functions_php' ),
		);
	}
	/**
	 * 関数：apply_admin_font
	 * 概要：管理画面のフォントを適用する
	 *
	 * 詳細：設定されたフォントを管理画面のCSSに反映する
	 **/
	public function apply_admin_font() {
		$options    = get_option( 'add_functions_php_settings' );
		$admin_font = isset( $options['admin_font'] ) ? $options['admin_font'] : 'default';

		if ( $admin_font !== 'default' ) {
			$font_family = '';

			switch ( $admin_font ) {
				case 'Noto Sans JP':
					$font_family = 'Noto Sans JP';
					break;
				case 'Noto Serif JP':
					$font_family = 'Noto Serif JP';
					break;
				case 'Rounded_Mplus_1p':
					$font_family = 'Rounded_Mplus_1p';
					break;
				case 'logo_type_gothic':
					$font_family = 'ロゴたいぷゴシック';
					break;
			}

			if ( ! empty( $font_family ) ) {
				echo '<style type="text/css">';
				echo '#adminmenu div.wp-menu-name,
                  #adminmenu .wp-submenu a,
                  span.collapse-button-label,
                  #wpadminbar .ab-label,
                  a.ab-item,
                  #footer-thankyou,
                  #wpcontent,
                  #wpfooter,
                  #wpcontent p,
                  #wpfooter p,
                  #wpcontent a,
                  #wpfooter a,
                  #wpcontent span,
                  #wpfooter span,
                  #wpcontent div,
                  #wpfooter div,
                  #wpcontent dl,
                  #wpfooter dl,
                  #wpcontent dt,
                  #wpfooter dt,
                  #wpcontent ul,
                  #wpfooter ul,
                  #wpcontent li,
                  #wpfooter li,
                  #wpcontent form,
                  #wpfooter form,
                  #wpcontent input,
                  #wpfooter input,
                  #wpcontent label,
                  #wpfooter label,
                  #wpcontent table,
                  #wpfooter table {';
				echo "font-family: '{$font_family}', sans-serif;";
				echo '}';
				echo '</style>';
			}
		}
	}
}
