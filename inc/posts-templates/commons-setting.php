<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e('Maximum number of archive page displays', 'add-functions-php' )?></h4>
	<div class="grid-item admin toggle-container">
		<label for="<?php echo esc_attr( $post_type->name ); ?>_posts_per_page" class="number-spinner-wrap">
			<input type="number" name="posts_functions_options_<?php echo esc_attr( $post_type->name ); ?>[posts_per_page]" id="<?php echo esc_attr( $post_type->name ); ?>_posts_per_page" value="<?php echo esc_attr( $selected_options['posts_per_page'] ?? '10' ); ?>">
			<span class="spinner spinner-down">-</span>
	<span class="spinner spinner-up">+</span>
		</label>
	</div>
</div>

<div class="grid_form_contents_item">
	<h4 class="grid-item label"><?php _e('Max Revision Count', 'add-functions-php' )?></h4>
	<div class="grid-item admin toggle-container">
		<label for="<?php echo esc_attr( $post_type->name ); ?>_revisions_number" class="number-spinner-wrap">
			<input type="number" name="posts_functions_options_<?php echo esc_attr( $post_type->name ); ?>[revisions_number]" id="<?php echo esc_attr( $post_type->name ); ?>_revisions_number" value="<?php echo esc_attr( $selected_options['revisions_number'] ?? '-1' ); ?>">
				<span class="spinner spinner-down">-</span>
				<span class="spinner spinner-up">+</span>
		</label>
	</div>
</div>
