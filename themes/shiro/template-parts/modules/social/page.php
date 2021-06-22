<?php
/**
 * Social Share Module.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['heading'] ) && empty( $template_args['services'] ) ) {
	return;
}

$tweet_text = get_theme_mod( 'social_tweet_text', __( 'Tweet', 'shiro-admin' ) );
$share_text = get_theme_mod( 'social_share_text', __( 'Share', 'shiro-admin' ) );
?>

<div class="mw-1360 mod-margin-bottom ">
	<div class="social-share">
		<?php if ( ! empty( $template_args['heading'] ) ) : ?>
		<h4 class="h5 uppercase mar-bottom"><?php echo esc_html( $template_args['heading'] ); ?></h4>
		<?php endif; ?>
		<?php if ( in_array( 'twitter', $template_args['services'], true ) ) : ?>
		<a href="<?php echo esc_url( wmf_get_share_url( 'twitter', $template_args ) ); ?>" class="color-blue mar-right" target="_blank"><?php wmf_show_icon( 'social-twitter' ); ?><span class="h4 uppercase text-bold"><?php echo esc_html( $tweet_text ); ?></span></a>
		<?php endif; ?>
		<?php if ( in_array( 'facebook', $template_args['services'], true ) ) : ?>
		<a href="<?php echo esc_url( wmf_get_share_url( 'facebook', $template_args ) ); ?>" class="color-blue" target="_blank"><?php wmf_show_icon( 'social-facebook' ); ?><span class="h4 uppercase text-bold"><?php echo esc_html( $share_text ); ?></span></a>
		<?php endif; ?>
	</div>
</div>
