<?php
/**
 * このファイルは設定可能な機能のリストを返します。
 */
defined( 'ABSPATH' ) || exit;
return array(

	/**
	* headタグ
	*/
	'edt_uri'                        => __( 'Remove EditURI', 'add-functions-php' ),
	'shorten_url'                    => __( 'Hide Shortened URL', 'add-functions-php' ),
	'remove_prev_next'               => __( 'Do not output rel="prev/next"', 'add-functions-php' ),
	'none_rss'                       => __( 'Turn off the feed function', 'add-functions-php' ),
	'none_comments_rss'              => __( 'Hide Comments Feed', 'add-functions-php' ),
	'remove_emoji'                   => __( 'Remove Emoji CSS/Script', 'add-functions-php' ),
	'remove_windows'                 => __( 'Remove link rel="wlwmanifest"', 'add-functions-php' ),
	'wp_version'                     => __( 'Remove WordPress Version Information in head', 'add-functions-php' ),
	'self_pinback_invalid'           => __( 'Disable Self-Pingback (Automatic Reciprocal Link)', 'add-functions-php' ),
	'stop_standard_sitemap_output'   => __( 'Stop WordPress standard sitemap output', 'add-functions-php' ),
	'no_output_rest_api'             => __( 'Do not output link tags for REST API', 'add-functions-php' ),
	'not_output_srcset'              => __( 'Stop outputting “srcset” inside <img>', 'add-functions-php' ),

	/**
	* Security Measures
	*/
	'remove_author_archive'          => __( 'Disable Author Archive', 'add-functions-php' ),
	'disable_rest_api_user'          => __( 'Disable REST API to check user name', 'add-functions-php' ),
	'login_error_message'            => __( 'Change Login Error Message', 'add-functions-php' ),
	'login_limit_3'                  => __( 'Limit Login Attempts to 3', 'add-functions-php' ),
	'reject_login'                   => __( 'Block Logins from Outside Japan', 'add-functions-php' ),
	'reject_comments'                => __( 'Block Comments from Outside Japan', 'add-functions-php' ),
	// 'deny_php'                       => __( 'Deny PHP Execution in Media Library URLs', 'add-functions-php' ),

	/**
	* SEO Measures and Speed Optimization
	*/
	'jquery_miragrate'               => __( 'Do not load jQuery Migrate', 'add-functions-php' ),
	'add_defer_to_jquery'            => __( 'Add defer to jQuery <script> tag', 'add-functions-php' ),
	'add_async_to_jquery'            => __( 'Add async to jQuery <script> tag', 'add-functions-php' ),
	'jquery_head'                    => __( 'Load Official WordPress jQuery via Google CDN in <head>', 'add-functions-php' ),
	'jquery_body'                    => __( 'Load Official WordPress jQuery via Google CDN just before </body>', 'add-functions-php' ),
	'add_lazy'                       => __( 'Delay img loading using loading="lazy" (Not recommended to use with the following)', 'add-functions-php' ),
	'use_lazysizes'                  => __( 'Lazy-load img, video, and iframe using lazysizes.js', 'add-functions-php' ),
	'seo_setting'                    => __( 'Output SEO settings input fields on the page/post editing screen', 'add-functions-php' ),

	// 'redirect_https'                 => __( 'Redirect to HTTPS', 'add-functions-php' ),
	'setting_enable_keep_alive'      => __( 'Set Enable Keep-Alive', 'add-functions-php' ),
	'cash_img_fonts'                 => __( 'Cache Images and Fonts', 'add-functions-php' ),
	'cash_browser'                   => __( 'Set Browser Cache', 'add-functions-php' ),
	'stream_webp'                    => __( 'Prioritize serving .webp files with the same filename', 'add-functions-php' ),
	'ignore_etags'                   => __( 'Ignore ETags (Configure entity tags)', 'add-functions-php' ),
	'compress_file'                  => __( 'Compress HTML, CSS, JavaScript, Text, and XML', 'add-functions-php' ),

	/**
	* OGP Settings
	*/
	'ogp_meta_tag'                   => __( 'Output OGP Meta Tag with this Plugin', 'add-functions-php' ),
	'taxonomy_description'           => __( 'Set Tag/Category Description to Respective Descriptions', 'add-functions-php' ),
	'ogp_img'                        => __( 'Set Featured Image as OGP Image', 'add-functions-php' ),
	'twitter_card'                   => __( 'Output Twitter(X) Share Meta Tag with this Plugin', 'add-functions-php' ),

	/**
	* Extended Features
	*/
	'remove_blank_p'                 => __( 'Automatically Remove Empty p Tags', 'add-functions-php' ),
	'maintenance_mode'               => __( 'Switch to Maintenance Mode', 'add-functions-php' ),
	'fontawesome_v6'                 => __( 'Load Font Awesome (Version 6)', 'add-functions-php' ),
	'svg_upload'                     => __( 'Allow SVG Image Uploads', 'add-functions-php' ),
	'pdf_upload'                     => __( 'Allow PDF File Uploads', 'add-functions-php' ),
	'template_file'                  => __( 'Display Template File Name in Admin Bar', 'add-functions-php' ),
	'hide_update_notices'            => __( 'Hide Update Notifications Except for Specific Users', 'add-functions-php' ),
	'enable_auto_updates'            => __( 'Enable Plugin, WordPress, and Theme Auto Updates', 'add-functions-php' ),
	'img_resize_cancel'              => __( 'Do Not Force Resize When Uploading Large Images', 'add-functions-php' ),
	'add_slug_body_class'            => __( 'Add Slug to body (or #body_wrap) Class', 'add-functions-php' ),
	'add_browser_name_to_body_class' => __( 'Add Browser Name to body (or #body_wrap) Class', 'add-functions-php' ),
	'remove_default_widgets'         => __( 'Hide all default dashboard widgets', 'add-functions-php' ),
	'redirect_single_search_result'  => __( 'Redirect to Page if Only One Search Result', 'add-functions-php' ),
	'hidden_admin_bar'               => __( 'Output button to show/hide adminbar on frontend', 'add-functions-php' ),

);
