<?php
/**
 * Template for Intro button
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args ) || empty( $template_args['title'] ) || empty( $template_args['link'] ) ) {
	return;
}

?>

<a href="<?php echo esc_url( $template_args['link'] ); ?>" class="btn btn-pink search-btn">
	<?php
    if (is_page( 'support' ) ):
        echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/src/svg/lock-white.svg" alt="" class="secure">';
    endif;

    echo esc_html( $template_args['title'] );
    ?>
</a>
