	<h3><?php _e('Code to be added to &lt;head&gt;', 'add-functions-php' )?></h3>
	<textarea id="add_head" name="add_functions_php_settings[add_head]" rows="6"><?php echo isset( $options['add_head'] ) ? esc_textarea( $options['add_head'] ) : ''; ?></textarea>
	<h3><?php _e('Code to be added immediately after the start of &lt;body&gt;', 'add-functions-php' )?></h3>
	<textarea id="body_start" name="add_functions_php_settings[body_start]" rows="6"><?php echo isset( $options['body_start'] ) ? esc_textarea( $options['body_start'] ) : ''; ?></textarea>
	<h3><?php _e('Code to be added before the end of &lt;body&gt;.', 'add-functions-php' )?></h3>
	<textarea id="body_after" name="add_functions_php_settings[body_after]" rows="6"><?php echo isset( $options['body_after'] ) ? esc_textarea( $options['body_after'] ) : ''; ?></textarea>