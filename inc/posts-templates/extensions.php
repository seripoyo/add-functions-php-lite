<ul>
	<?php
	$selected_options = get_option( 'posts_functions_options_post', array() );
	foreach ( $posts_functions_list as $key => $label ) :
		$checked = isset( $selected_options[ $key ] ) ? $selected_options[ $key ] : '0';
		?>
	<li>
		<label for="<?php echo esc_attr( $key ); ?>" class="toggle">
		<input class="toggle__input" type="checkbox"
			name="posts_functions_options_post[<?php echo esc_attr( $key ); ?>]"
			id="<?php echo esc_attr( $key ); ?>"
			<?php checked( $checked, '1' ); ?>>
		<span class="toggle__slider"></span>
		</label>
		<span><?php echo esc_html( $label ); ?></span>
	</li>
	<br>
	<?php endforeach; ?>
</ul>
