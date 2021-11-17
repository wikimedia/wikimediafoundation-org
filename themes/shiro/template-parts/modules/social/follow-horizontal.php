<?php
/**
 * Social Follow Vertical Module.
 *
 * @package shiro
 */

$template_args = $args;

$follow_text = ! empty( $template_args['follow_text'] ) ? $template_args['follow_text'] : get_theme_mod( 'wmf_social_follow_text', __( 'Follow', 'shiro-admin' ) );
$facebook    = ! empty( $template_args['facebook_url'] ) ? $template_args['facebook_url'] : get_theme_mod( 'wmf_facebook_url' );
$twitter     = ! empty( $template_args['twitter_url'] ) ? $template_args['twitter_url'] : get_theme_mod( 'wmf_twitter_url' );
$instagram   = ! empty( $template_args['instagram_url'] ) ? $template_args['instagram_url'] : get_theme_mod( 'wmf_instagram_url' );
$blog        = ! empty( $template_args['blog_url'] ) ? $template_args['blog_url'] : get_theme_mod( 'wmf_blog_url' );

$facebook_label  = ! empty( $template_args['facebook_label'] ) ? $template_args['facebook_label'] : get_theme_mod( 'wmf_facebook_label', __( 'Facebook', 'shiro-admin') );
$twitter_id      = ! empty( $template_args['twitter_id'] ) ? $template_args['twitter_id'] : get_theme_mod( 'wmf_twitter_id', __( 'Twitter', 'shiro-admin' ) );
$twitter_id      = sprintf( '@%s', trim( $twitter_id, '@' ) );
$instagram_label = ! empty( $template_args['instagram_label'] ) ? $template_args['instagram_label'] : get_theme_mod( 'wmf_instagram_label', __( 'Instagram', 'shiro-admin' ) );
$blog_label      = ! empty( $template_args['blog_label'] ) ? $template_args['blog_label'] : get_theme_mod( 'wmf_blog_label', __( 'Wikimedia Blog', 'shiro-admin' ) );

if ( empty( $facebook ) && empty( $twitter ) && empty( $instagram ) && empty( $blog ) ) {
	return;
}
?>

<div class="mar-top">
	<?php if ( ! empty( $follow_text ) ) : ?>
	<h4><?php echo esc_html( $follow_text ); ?></h4>
	<?php endif; ?>
	<ul class="link-list social-list color-blue ">

		<?php if ( ! empty( $facebook ) ) : ?>
		<li>
			<a aria-describedby="a11y-message--new-window" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noreferrer">
				<?php wmf_show_icon( 'social-facebook' ); ?>
				<span aria-hidden="true"><?php echo esc_html( $facebook_label ); ?></span>
				<span class="visually-hidden"><?php esc_html_e( 'Connect with us on Facebook', 'shiro' ); ?></span>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $twitter ) ) : ?>
		<li class="twitter-container">
			<a aria-describedby="a11y-message--new-window" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noreferrer">
				<?php wmf_show_icon( 'social-twitter' ); ?>
				<span aria-hidden="true"><?php echo esc_html( $twitter_id ); ?></span>
				<span class="visually-hidden"><?php esc_html_e( 'Connect with us on Twitter', 'shiro' ); ?></span>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $instagram ) ) : ?>
		<li>
			<a aria-describedby="a11y-message--new-window" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noreferrer">
				<?php wmf_show_icon( 'social-instagram' ); ?>
				<span aria-hidden="true"><?php echo esc_html( $instagram_label ); ?></span>
				<span class="visually-hidden"><?php esc_html_e( 'Connect with us on Instagram', 'shiro' ); ?></span>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $blog ) ) : ?>
		<li>
			<a href="<?php echo esc_url( $blog ); ?>">
				<span class="wmf-logo-icon"><?php wmf_show_icon( 'wikimedia' ); ?></span>
				<span aria-hidden="true"><?php echo esc_html( $blog_label ); ?></span>
				<span class="visually-hidden"><?php esc_html_e( 'Read our blog', 'shiro' ); ?></span>
			</a>
		</li>
		<?php endif; ?>
	</ul>
	<span aria-hidden="true" class="visually-hidden" id="a11y-message--new-window">
    (opens new window)</span>
</div>
