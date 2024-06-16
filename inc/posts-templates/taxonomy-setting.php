            <div class="grid_form_contents_item">
                <h4 class="grid-item label"><?php echo esc_html($taxonomy->label); ?><?php _e('Maximum number of archive pages', 'add-functions-php' )?></h4>
                <div class="grid-item admin toggle-container">
                    <label for="<?php echo esc_attr($taxonomy->name); ?>_posts_per_page">
                        <input type="number" name="add_functions_pack_selected_options_<?php echo esc_attr($post_type->name); ?>[<?php echo esc_attr($taxonomy->name); ?>_posts_per_page]" id="<?php echo esc_attr($taxonomy->name); ?>_posts_per_page" value="<?php echo esc_attr($selected_options[$taxonomy->name . '_posts_per_page'] ?? '10'); ?>">
                    </label>
                </div>
            </div>
            <div class="grid_form_contents_item">
                <h4 class="grid-item label"><?php _e('Add taxonomy term to URL', 'add-functions-php' )?></h4>
                <div class="grid-item admin toggle-container">
                    <label class="toggle">
                        <input class="toggle__input" type="checkbox" name="add_functions_pack_selected_options_<?php echo esc_attr($post_type->name); ?>[add_term_to_url]" <?php checked($selected_options['add_term_to_url'] ?? '0', '1'); ?> role="switch">
                        <span class="toggle__slider"></span>
                    </label>
                </div>
            </div>