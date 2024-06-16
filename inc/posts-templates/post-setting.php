<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e( 'archive slug', 'add-functions-php' ); ?></h4>
	<div class="grid-item admin toggle-container text_input">
		<label for="archive_slug">
			<input type="text" name="posts_functions_options_post[archive_slug]" placeholder="<?php _e( '(e.g., archive, etc.)', 'add-functions-php' ); ?>" id="archive_slug" value="<?php echo esc_attr( $selected_options['archive_slug'] ?? '' ); ?>">
			<span></span>
		</label>
	</div>
</div>
<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e( 'Archive labels: Labels that are treated the same as "post"', 'add-functions-php' ); ?></h4>
	<div class="grid-item admin toggle-container text_input">
			<input type="text" placeholder="<?php _e( '(e.g., blogs, columns, etc.)', 'add-functions-php' ); ?>" name="posts_functions_options_post[archive_label]" id="archive_label" value="<?php echo esc_attr( $selected_options['archive_label'] ?? '' ); ?>">
			<span></span>
		</label>
	</div>
</div>
<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e( 'Meta description for archive page', 'add-functions-php' ); ?></h4>
	<div class="grid-item admin toggle-container">
		<label for="archive_label">
			<textarea name="posts_functions_options_post[archive_description]" id="archive_description_input" cols="60" rows="4" style="width: 100%;"><?php echo esc_attr( $selected_options['archive_description'] ?? '' ); ?></textarea>
		</label>
	</div>
</div>

<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e( 'Maximum number of category archive pages', 'add-functions-php' ); ?></h4>
	<div class="grid-item admin toggle-container form-type-number">
		<label for="category_posts_per_page" class="number-spinner-wrap"> 
			<input type="number" name="posts_functions_options_post[category_posts_per_page]" id="category_posts_per_page" value="<?php echo esc_attr( $selected_options['category_posts_per_page'] ?? '10' ); ?>">
				<span class="spinner spinner-down">-</span>
				<span class="spinner spinner-up">+</span>
		</label>
	</div>
</div>
<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e( 'Archive page display order', 'add-functions-php' ); ?></h4>
	<div class="grid-item admin toggle-container">
		<label for="order" class="cp_ipselect">
			<select  class="cp_sl06" name="posts_functions_options_post[order]" id="order">
				<option value="ASC" <?php selected( $selected_options['order'] ?? '', 'ASC' ); ?>><?php _e( 'ascending-order', 'add-functions-php' ); ?></option>
				<option value="DESC" <?php selected( $selected_options['order'] ?? '', 'DESC' ); ?>><?php _e( 'descending-order', 'add-functions-php' ); ?></option>
			</select>
			<span class="cp_sl06_highlight"></span>
			<span class="cp_sl06_selectbar"></span>
			<span class="cp_sl06_selectlabel"><?php _e( 'Ascending or Descending', 'add-functions-php' ); ?></span>
		</label>
	</div>
	<div class="grid-item admin toggle-container">
		<label for="orderby" class="cp_ipselect">
			<select class="cp_sl06" name="posts_functions_options_post[orderby]" id="orderby">
				<option value="none" <?php selected( $selected_options['orderby'] ?? '', 'none' ); ?>><?php _e( 'none', 'add-functions-php' ); ?></option>
				<option value="ID" <?php selected( $selected_options['orderby'] ?? '', 'ID' ); ?>><?php _e( 'Order by Post ID', 'add-functions-php' ); ?></option>
				<option value="author" <?php selected( $selected_options['orderby'] ?? '', 'author' ); ?>><?php _e( 'Order by Author', 'add-functions-php' ); ?></option>
				<option value="title" <?php selected( $selected_options['orderby'] ?? '', 'title' ); ?>><?php _e( 'Order by title', 'add-functions-php' ); ?></option>
				<option value="date" <?php selected( $selected_options['orderby'] ?? '', 'date' ); ?>><?php _e( 'Order by Date', 'add-functions-php' ); ?></option>
				<option value="modified" <?php selected( $selected_options['orderby'] ?? '', 'modified' ); ?>><?php _e( 'Order by Date Updated', 'add-functions-php' ); ?></option>
				<option value="parent" <?php selected( $selected_options['orderby'] ?? '', 'parent' ); ?>><?php _e( 'Order by Parent ID order', 'add-functions-php' ); ?></option>
				<option value="rand" <?php selected( $selected_options['orderby'] ?? '', 'rand' ); ?>><?php _e( 'Random', 'add-functions-php' ); ?></option>
				<option value="comment_count" <?php selected( $selected_options['orderby'] ?? '', 'comment_count' ); ?>><?php _e( 'Order by number of comments', 'add-functions-php' ); ?></option>
			</select>
			<span class="cp_sl06_highlight"></span>
			<span class="cp_sl06_selectbar"></span>
			<span class="cp_sl06_selectlabel"><?php _e( 'sort order', 'add-functions-php' ); ?></span>
		</label>
	</div>
</div>

<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e( 'Maximum number of Tag pages', 'add-functions-php' ); ?></h4>
	<div class="grid-item admin toggle-container">
		<label for="tag_posts_per_page" class="number-spinner-wrap">
			<input type="number" name="posts_functions_options_post[tag_posts_per_page]" id="tag_posts_per_page" value="<?php echo esc_attr( $selected_options['tag_posts_per_page'] ?? '10' ); ?>">
				<span class="spinner spinner-down">-</span>
				<span class="spinner spinner-up">+</span>
		</label>
	</div>
</div>
<!-- <div class="grid_form_contents_item">
	<h4 class="grid-item label">カテゴリーのURLから"/category"と"/tag"を削除を削除</h4>
	<p>※ドメイン/category/カテゴリースラッグはドメイン/category_カテゴリースラッグになります。</p>
	<p>※ドメイン/tag/タグスラッグはドメイン/tag_タグスラッグになります。</p>
	<div class="grid-item admin toggle-container">
		<label class="toggle">
			<input class="toggle__input" type="checkbox" name="posts_functions_options_post[remove_category_tag_base]" <?php checked( $selected_options['remove_category_tag_base'] ?? '0', '1' ); ?> role="switch">
			<span class="toggle__slider"></span>
		</label>
	</div>
</div> -->

