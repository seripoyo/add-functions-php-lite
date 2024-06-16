<?php
defined( 'ABSPATH' ) || exit;

// 必要なファイルを読み込み
require_once SERVER_PATH . 'class/class-posts.php';
$add_functions_pack_posts = new \Add_function_PHP\Posts\AFP_Posts();
$posts_functions_list     = $add_functions_pack_posts->get_posts_functions_list();
// 現在のWordPressに存在する投稿、カスタム投稿タイプを取得
$post_types = array( 'post' => get_post_type_object( 'post' ) );
// $post_types = get_post_types( array( 'public' => true ), 'objects' );

unset( $post_types['page'], $post_types['attachment'] );
if ( ! defined( 'POSTS_TEMPLATE_PATH' ) ) {
	define( 'POSTS_TEMPLATE_PATH', SERVER_PATH . 'inc/posts-templates/' );
}

// 全ての投稿タイプを取得
foreach ( $post_types as $post_type ) {
	$output = array(
		'投稿タイプ名' => $post_type->name,
		'ラベル'    => $post_type->label,
		'説明'     => $post_type->description,
		'パーマリンク' => $post_type->rewrite['slug'] ?? '',
		'アーカイブ'  => $post_type->has_archive ? 'あり' : 'なし',
	);

	// 投稿タイプが'post'の場合、カテゴリーページとタグページの有無を確認
	if ( $post_type->name === 'post' ) {
		$output['カテゴリーページ'] = taxonomy_exists( 'category' ) ? 'あり' : 'なし';
		$output['タグページ']    = taxonomy_exists( 'post_tag' ) ? 'あり' : 'なし';
	}
	// カスタム投稿タイプの場合、紐づいているタクソノミーの有無を確認
	if ( ! in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
		$taxonomies      = get_object_taxonomies( $post_type->name, 'objects' );
		$taxonomy_output = array();
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_output[ $taxonomy->label ] = taxonomy_exists( $taxonomy->name ) ? 'あり' : 'なし';
		}
		$output['タクソノミー'] = $taxonomy_output;
	}
	error_log( print_r( $output, true ) );
}

?>

<ul class="tab">
	<li class="active">
		<a href="#posts_extension"><?php _e( 'Post-related extensions', 'add-functions-php' ); ?></a>
	</li>
	<?php foreach ( $post_types as $index => $post_type ) : ?>
		<li class="<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === esc_attr( $post_type->name ) ) ? 'active' : ''; ?>">
			<a href="#<?php echo esc_attr( $post_type->name ); ?>">
				<?php echo esc_html( $post_type->name === 'post' ? __( 'post', 'add-functions-php' ) : $post_type->label ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

<form method="post" action="options.php">
	<?php
	settings_fields( 'posts_functions_options' );
	do_settings_sections( 'posts_functions_options' );
	?>
	<input type="hidden" name="posts_functions_options_post[current_tab]" id="current_tab" value="">
	<div id="posts_extension" class="area<?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === 'posts_extension' ) ? 'is-active' : ''; ?>">
		<?php require_once POSTS_TEMPLATE_PATH . 'extensions.php'; ?>
	</div>
	<?php foreach ( $post_types as $post_type ) : ?>
		<div id="<?php echo esc_attr( $post_type->name ); ?>" class="area <?php echo ( isset( $options['current_tab'] ) && $options['current_tab'] === esc_attr( $post_type->name ) ) ? 'is-active' : ''; ?>">
			<div class="container wrapper">
				<h3><?php echo esc_html( __( $post_type->label, 'add-functions-php' ) ); ?><?php _e( 'Setup screen', 'add-functions-php' ); ?></h3>

				<?php
				$selected_options = get_option( 'posts_functions_options_' . $post_type->name, array() );
				?>

				<div class="grid_posts_container">
					<div class="grid_form_contents">
						<?php include_once POSTS_TEMPLATE_PATH . 'commons-setting.php'; ?>

						<?php if ( $post_type->name === 'post' ) : ?>
							<?php include_once POSTS_TEMPLATE_PATH . 'post-setting.php'; ?>
						<?php else : ?>
							<?php include_once POSTS_TEMPLATE_PATH . 'commons-setting.php'; ?>
							<?php include_once POSTS_TEMPLATE_PATH . 'taxonomy-setting.php'; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<?php
	$button_text = __( 'Save settings', 'add-functions-php' );
	submit_button( $button_text );
	?>
</form>

<script>
document.querySelectorAll(".number-spinner-wrap").forEach($wrap => {
	const $input = $wrap.querySelector("input");
	$wrap.querySelector(".spinner-down").onclick = () => {
		$input.stepDown();
	};
	$wrap.querySelector(".spinner-up").onclick = () => {
		$input.stepUp();
	};
});

</script>
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

	var currentTab = window.location.hash || "#posts_extension";
	GethashID(currentTab);

	$(".tab a").on("click", function() {
		var idName = $(this).attr("href");
		GethashID(idName);
		history.pushState(null, null, idName);
		return false;
	});

	$(window).on("popstate", function() {
		var hashName = window.location.hash;
		if (hashName) {
			GethashID(hashName);
		} else {
			$(".tab li:first").addClass("active");
			$(".area:first").addClass("is-active");
		}
	});

	$('form').on('submit', function() {
		var currentTab = $(".tab li.active a").attr("href").substring(1);
		$('#current_tab').val(currentTab);
	});
});
</script>