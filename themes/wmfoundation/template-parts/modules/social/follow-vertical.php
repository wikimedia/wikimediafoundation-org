<?php
/**
 * Social Follow Vertical Module.
 *
 * @package wmfoundation
 */

$follow_text = get_theme_mod( 'wmf_social_follow_text', __( 'Follow', 'wmfoundation' ) );
$facebook    = get_theme_mod( 'wmf_facebook_url' );
$twitter     = get_theme_mod( 'wmf_twitter_url' );
$instagram   = get_theme_mod( 'wmf_instagram_url' );
$blog        = get_theme_mod( 'wmf_blog_url' );

$facebook_label  = get_theme_mod( 'wmf_facebook_label', 'Facebook' );
$twitter_id      = get_theme_mod( 'wmf_twitter_id', 'Wikimedia' );
$twitter_id      = sprintf( '@%s', trim( $twitter_id, '@' ) );
$instagram_label = get_theme_mod( 'wmf_instagram_label', 'Instagram' );
$blog_label      = get_theme_mod( 'wmf_blog_label', 'Wikimedia Blog' );

if ( empty( $facebook ) && empty( $twitter ) && empty( $instagram ) && empty( $blog ) ) {
	return;
}
?>

<div class="module-mu w-18p">
	<?php if ( ! empty( $follow_text ) ) : ?>
	<h3 class="h3"><?php echo esc_html( $follow_text ); ?></h3>
	<?php endif; ?>
	<div class="wysiwyg">
		<p></p>
	</div>
	<ul class="link-list social-list color-blue ">

		<?php if ( ! empty( $facebook ) ) : ?>
		<li>
			<a href="<?php echo esc_url( $facebook ); ?>">
				<?php wmf_show_icon( 'social-facebook' ); ?>
				<?php echo esc_html( $facebook_label ); ?>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $twitter ) ) : ?>
		<li class="twitter-container">
			<a href="<?php echo esc_url( $twitter ); ?>">
				<?php wmf_show_icon( 'social-twitter' ); ?>
				<?php echo esc_html( $twitter_id ); ?>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $instagram ) ) : ?>
		<li>
			<a href="<?php echo esc_url( $instagram ); ?>">
				<?php wmf_show_icon( 'social-instagram' ); ?>
				<?php echo esc_html( $instagram_label ); ?>
			</a>
		</li>
		<?php endif; ?>
		<?php if ( ! empty( $blog ) ) : ?>
		<li>
			<a href="<?php echo esc_url( $blog ); ?>">
				<span class="wmf-logo-icon"><?php wmf_show_icon( 'wikimedia' ); ?></span>
				<?php echo esc_html( $blog_label ); ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>

</div>
