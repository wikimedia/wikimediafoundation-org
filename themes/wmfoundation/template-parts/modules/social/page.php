<?php
/**
 * Social Share Module.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['heading'] ) && empty( $template_args['services'] ) ) {
	return;
}

$tweet_text = get_theme_mod( 'social_tweet_text', __( 'Tweet', 'wmfoundation' ) );
$share_text = get_theme_mod( 'social_share_text', __( 'Share', 'wmfoundation' ) );
?>

<div class="mw-1360 mod-margin-bottom ">
	<div class="social-share">
		<?php if ( ! empty( $template_args['heading'] ) ) : ?>
		<h4 class="h5 uppercase mar-bottom"><?php echo esc_html( $template_args['heading'] ); ?></h4>
		<?php endif; ?>
		<?php if ( in_array( 'twitter', $template_args['services'], true ) ) : ?>
		<a href="<?php echo esc_html( wmf_get_share_url( 'twitter', $template_args ) ); ?>" class="color-blue mar-right" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i><span class="h4 uppercase text-bold"><?php echo esc_html( $tweet_text ); ?></span></a>
		<?php endif; ?>
		<?php if ( in_array( 'facebook', $template_args['services'], true ) ) : ?>
		<a href="<?php echo esc_html( wmf_get_share_url( 'facebook', $template_args ) ); ?>" class="color-blue" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i><span class="h4 uppercase text-bold"><?php echo esc_html( $share_text ); ?></span></a>
		<?php endif; ?>
	</div>
</div>
