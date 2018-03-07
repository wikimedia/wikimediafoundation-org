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
	wmf_get_template_data(),
	array(
		'uri'        => wmf_get_current_url(),
		'list_class' => ' link-list',
	)
);
?>

<div class="social-share text-center">
	<h4 class="h5 uppercase"><?php echo esc_html( $share_text ); ?></h4>
	<ul class="social-list color-blue<?php echo esc_attr( $args['list_class'] ); ?>">
		<li><a href="<?php echo esc_html( wmf_get_share_url( 'twitter', $args ) ); ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
		<li><a href="<?php echo esc_html( wmf_get_share_url( 'facebook', $args ) ); ?>"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
	</ul>
</div>
