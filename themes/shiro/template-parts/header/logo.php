<?php
/**
 * Adds Header logo
 *
 * @package shiro
 */

$wmf_header_image = get_header_image();
?>

<a href="<?php echo esc_url( get_site_url() ); ?>">
	<span class='btn-label-a11y'><?php bloginfo('name'); ?></span>
	<?php
	if ( empty( $wmf_header_image ) ) :
		wmf_show_icon( 'logo-horizontal' );
	else :
		?>
		<img src="<?php echo esc_url( $wmf_header_image ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>" />
	<?php endif; ?>
</a>
