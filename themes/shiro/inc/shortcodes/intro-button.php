<?php
/**
 * Placeholder shortcode for the FM intro button field.
 */

namespace WMF\Shortcodes\Intro_Button;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_intro_button', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_intro_button] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function shortcode_callback( $atts = [] ) {
	$html = shortcode_content( get_the_ID() );

	return wp_kses_post( $html );
}

/**
 * Render the shortcode HTML.
 *
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$button = (array) get_post_meta( $post_id, 'intro_button', true );
	if ( empty( $button ) ) {
		return '';
	}
	ob_start();
	?>

	<a href="<?php echo esc_url( $button['link'] ); ?>" class="btn btn-pink search-btn">
		<?php
		if ( is_page( 'support' ) ) :
			echo '<img src="' . esc_url( get_stylesheet_directory_uri() ) . '/assets/src/svg/lock-white.svg" alt="" class="secure">';
		endif;

		echo esc_html( $button['title'] );
		?>
	</a>
	<?php
	return (string) ob_get_clean();
}
