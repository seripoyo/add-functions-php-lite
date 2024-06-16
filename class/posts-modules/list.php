<?php
/**
 * このファイルは設定可能な機能のリストを返します。
 */
defined( 'ABSPATH' ) || exit;
return array(
	'duplicate_post_button' => __( 'Add a button to duplicate each post', 'add-functions-php' ),
	'add_post_id'           => __( 'Display the post ID on each post list page', 'add-functions-php' ),
	'add_last_updated'      => __( 'Display the last updated date on each post list page', 'add-functions-php' ),
	'show_featured_image'   => __( 'Display the featured image on each post list page', 'add-functions-php' ),
	'tag_checkbox'          => __( 'Display the tag list as checkboxes on the post editing screen', 'add-functions-php' ),
	'add_alt'               => __( 'Automatically add the image file name to the title and ALT of images', 'add-functions-php' ),
	'slug_ja_to_en'         => __( 'Automatically change the slug to id when the slug is in Japanese', 'add-functions-php' ),
);