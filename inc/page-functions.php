<?php

defined( 'ABSPATH' ) || exit;

$settings       = new \Add_function_PHP\Functions\AFP_Functions();
$functions_list = $settings->get_functions_list();
$options        = get_option( 'Add_functions_php_Settings' );
// 必要なファイルを読み込む。



?>
<h2 class="ssp-page__title"><?php _e( 'added to functions.php', 'add-functions-php' ); ?></h2>
<div class="page-functions-wrapper">

<ul class="tab">
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'head_tags' ) ? 'active' : ''; ?>">
		<a href="#head_tags"><?php _e( ' &lt;head&gt; tag', 'add-functions-php' ); ?></a>
	</li>
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'security' ) ? 'active' : ''; ?>">
		<a href="#security"><?php _e( 'Security measures', 'add-functions-php' ); ?></a>
	</li>
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'extensions' ) ? 'active' : ''; ?>">
		<a href="#extensions"><?php _e( 'extensions', 'add-functions-php' ); ?></a>
	</li>
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'seo_optimization' ) ? 'active' : ''; ?>">
		<a href="#seo_optimization"><?php _e( 'SEO and Accelerate', 'add-functions-php' ); ?></a>
	</li>
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'ogp_setting' ) ? 'active' : ''; ?>">
		<a href="#ogp_setting"><?php _e( 'OGP Settings', 'add-functions-php' ); ?></a>
	</li>
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'widgets_dashboard' ) ? 'active' : ''; ?>">
		<a href="#widgets_dashboard"><?php _e( 'Adding a dashboard widget', 'add-functions-php' ); ?></a>
	</li>
	<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'appearance' ) ? 'active' : ''; ?>">
		<a href="#appearance"><?php _e( 'Appearance of the Admin screen', 'add-functions-php' ); ?></a>
	</li>
</ul>

<form method="post" action="options.php">
	<?php
		settings_fields( 'add_functions_php_settings' );
		do_settings_sections( 'add_functions_php_settings' );
	if ( ! defined( 'FUNCTION_TEMPLATE_PATH' ) ) {
		define( 'FUNCTION_TEMPLATE_PATH', SERVER_PATH . 'inc/functions-template/' );

	}
	?>
		<input type="hidden" name="add_functions_php_settings[current_tab]" id="current_tab" value="">
	<div id="head_tags" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'head_tags' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'head-tags.php'; ?>
	</div>
	<div id="security" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'security' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'security.php'; ?>
	</div>
	<div id="extensions" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'extensions' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'extensions.php'; ?>
	</div>
	<div id="seo_optimization" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'seo_optimization' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'seo-optimization.php'; ?>
	</div>
	<div id="ogp_setting" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'ogp_setting' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'ogp-setting.php'; ?>
	</div>
	<div id="additional_code" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'additional_code' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'additional-code.php'; ?>
	</div>
	<div id="appearance" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'appearance' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'appearance.php'; ?>
	</div>
	<div id="widgets_dashboard" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'widgets_dashboard' ) ? 'is-active' : ''; ?>">
		<?php require_once FUNCTION_TEMPLATE_PATH . 'widgets-dashboard.php'; ?>
	</div>
	<?php
		$button_text = __( 'Save settings', 'add-functions-php' );
		submit_button( $button_text );
	?>
</form>

</div>



<script>
jQuery(document).ready(function($) {
	function GethashID(hashIDName) {
		if (hashIDName) {
			$(".tab li").removeClass("active");
			$(".area").removeClass("is-active");
			$(".tab li").find("a").each(function() {
				var idName = $(this).attr("href");
				if (idName == hashIDName) {
					var parentElm = $(this).parent();
					$(parentElm).addClass("active");
					$(hashIDName).addClass("is-active");
				}
			});
		}
	}

	// ページ読み込み時にタブを切り替える
	var currentTab = window.location.hash || "#head_tags";
	GethashID(currentTab);

	// タブクリック時にタブを切り替え、URLにハッシュを追加
	$(".tab a").on("click", function() {
		var idName = $(this).attr("href");
		GethashID(idName);
		history.pushState(null, null, idName);
		return false;
	});

	// ブラウザの戻る/進むボタンが押された時にタブを切り替える
	$(window).on("popstate", function() {
		var hashName = window.location.hash;
		if (hashName) {
			GethashID(hashName);
		} else {
			// ハッシュがない場合、デフォルトのタブを設定
			$(".tab li:first").addClass("active");
			$(".area:first").addClass("is-active");
		}
	});

	// フォーム送信時に現在のタブのハッシュをhiddenフィールドに設定
	$('form').on('submit', function() {
		var currentTab = $(".tab li.active a").attr("href").substring(1);
		$('#current_tab').val(currentTab);
	});
});
</script>
