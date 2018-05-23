<?php
/**
 * Social Share Vertical Module.
 *
 * @package wmfoundation
 */

$share_text = get_theme_mod( 'social_share_text', __( 'Share', 'wmfoundation' ) );

$template_data = wmf_get_template_data();
$services      = ! empty( $template_data['services'] ) ? $template_data['services'] : array( 'facebook', 'twitter' );

$args = wp_parse_args(
	$template_data,
	array(
		'uri'        => wmf_get_current_url(),
		'list_class' => 'link-list social-list',
	)
);
?>

<div class="social-share text-center social-container social-stacked">
	<h4 class="h5 uppercase mar-bottom_sm"><?php echo esc_html( $share_text ); ?></h4>
	<ul class="color-blue <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php if ( in_array( 'twitter', $services, true ) ) : ?>
		<li class="mar-bottom_sm">
			<a href="<?php echo esc_html( wmf_get_share_url( 'twitter', $args ) ); ?>">
				<?php wmf_show_icon( 'social-twitter' ); ?>
			</a>
		</li>
		<?php endif; ?>

		<?php if ( in_array( 'facebook', $services, true ) ) : ?>
		<li>
			<a href="<?php echo esc_html( wmf_get_share_url( 'facebook', $args ) ); ?>">
				<?php wmf_show_icon( 'social-facebook' ); ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>
</div>
