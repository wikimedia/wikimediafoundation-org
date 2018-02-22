<?php
/**
 * Social Share Vertical Module.
 *
 * @package wmfoundation
 */

/**
 * Todo: We may need to add a way to specify the services to use for this.
 */

$share_text = get_theme_mod( 'social_share_text', __( 'Share', 'wmfoundation' ) );

$args = wp_parse_args(
	array(
		'uri' => wmf_get_current_url(),
	), wmf_get_template_data()
);
?>

<div class="social-share">
	<span class="h5 bold uppercase color-black"><?php echo esc_html( $share_text ); ?></span>
	<span class="inline-social-list">
		<a href="<?php echo esc_html( wmf_get_share_url( 'twitter', $args ) ); ?>" class="color-blue"><i class="fa fa-twitter" aria-hidden="true"></i></a>
		<a href="<?php echo esc_html( wmf_get_share_url( 'facebook', $args ) ); ?>" class="color-blue"><i class="fa fa-facebook" aria-hidden="true"></i></a>
	</span>
</div>
