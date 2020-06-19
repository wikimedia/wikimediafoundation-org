<?php
/**
 * Placeholder shortcode for the donate element.
 *
 * @package WMF
 */

namespace WMF\Shortcodes\Donate_CTA;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_donate_cta', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_donate_cta] wrapper shortcode.
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
 *
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$wmf_homedonate_button = get_theme_mod( 'wmf_homedonate_button', __( 'Donate now', 'shiro' ) );
	$wmf_homedonate_uri    = get_theme_mod( 'wmf_homedonate_uri', '#' );
	$wmf_homedonate_intro  = get_theme_mod( 'wmf_homedonate_intro', __( 'Protect and sustain Wikipedia', 'shiro' ) );
	$wmf_homedonate_secure = get_theme_mod( 'wmf_homedonate_secure', __( 'SECURE DONATIONS', 'shiro' ) );

	ob_start(); ?>
	<div class="donate-cta">
		<p>
			<?php echo esc_html( $wmf_homedonate_intro ); ?>
		</p>
		<a class="btn btn-blue" href="<?php echo esc_url( $wmf_homedonate_uri ); ?>">
			<?php echo esc_html( $wmf_homedonate_button ); ?>
		</a>
		<div class="secure">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/lock.svg" alt="">
			<?php echo esc_html( $wmf_homedonate_secure ); ?>
		</div>
	</div>

	<?php return (string) ob_get_clean();
}
