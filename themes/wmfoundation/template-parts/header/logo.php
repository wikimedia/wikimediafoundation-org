<?php
/**
 * Adds Header logo
 *
 * @package wmfoundation
 */

$wmf_header_image = get_header_image();
?>

<div class="logo-container">
	<a href="<?php echo esc_url( get_site_url() ); ?>">
		<div class="logo-full">
			<?php
			if ( empty( $wmf_header_image_mobile ) ) :
				wmf_show_icon( 'logo-horizontal' );
			else :
			?>
				<img src="<?php echo esc_url( $wmf_header_image_mobile ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>" />
			<?php endif; ?>
		</div>
	</a>
</div>
