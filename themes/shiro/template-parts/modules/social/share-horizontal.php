<?php
/**
 * Social Share Vertical Module.
 *
 * @package shiro
 */

$share_args = wp_parse_args(
	[ 'uri' => wmf_get_current_url() ],
	$args
);

$services   = $share_args['services'] ?: array( 'facebook', 'twitter' );
$share_text = $share_args['title'] ?? get_theme_mod( 'wmf_social_share_text', __( 'Share', 'shiro-admin' ) );

?>

<div class="social-share social-share-home">
	<span class="inline-social-list">
		<?php if ( in_array( 'twitter', $services, true ) ) : ?>
			<a href="<?php echo esc_url( wmf_get_share_url( 'twitter', $share_args ) ); ?>" class="color-blue" target="_blank">
				<?php wmf_show_icon( 'social-twitter' ); ?>
				<?php echo esc_html( $share_text ); ?>
			</a>
		<?php endif; ?>

		<?php if ( in_array( 'facebook', $services, true ) ) : ?>
			<a href="<?php echo esc_url( wmf_get_share_url( 'facebook', $share_args ) ); ?>" class="color-blue" target="_blank">
				<?php wmf_show_icon( 'social-facebook' ); ?>
				<?php echo esc_html( $share_text ); ?>
			</a>
		<?php endif; ?>
	</span>
</div>
