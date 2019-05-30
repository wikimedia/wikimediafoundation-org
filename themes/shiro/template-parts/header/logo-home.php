<?php
/**
 * Adds Header logo
 *
 * @package shiro
 */

$wmf_header_image = get_header_image();

?>

<div class="logo-container">
	<a href="<?php echo esc_url( get_site_url() ); ?>">
	<?php
	if ( empty( $wmf_header_image ) ) :
		wmf_show_icon( 'logo-horizontal' );
	else :
		?>
		<img src="<?php echo esc_url( $wmf_header_image ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>" />
	<?php endif; ?>
	</a>
</div>
