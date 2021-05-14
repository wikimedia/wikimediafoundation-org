<?php
/**
 * Server-side registration for the shiro/share-article block.
 */

namespace WMF\Editor\Blocks\ShareArticle;

const BLOCK_NAME = 'shiro/share-article';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\register_block' );
}

/**
 * Register the block here.
 */
function register_block() {
	register_block_type(
		BLOCK_NAME,
		[
			'apiVersion'      => 2,
			'render_callback' => __NAMESPACE__ . '\\render_block',
		]
	);
}

/**
 * Render this block, given its attributes.
 *
 * @param [] $attributes Block attributes.
 * @return string HTML markup.
 */
function render_block( $attributes ) {
	$enable_twitter = $attributes['enableTwitter'] ?? true;
	$enable_facebook = $attributes['enableFacebook'] ?? true;

	if ( ! $enable_twitter && ! $enable_facebook ) {
		return '';
	}

	ob_start()
	?>
	<div class="share-article">
		<?php if ( $enable_twitter ) : ?>
			<a class="share-article__link" href="<?php echo esc_url( wmf_get_share_url( 'twitter' ) ); ?>" target="_blank" rel="noreferrer noopener">
				<?php wmf_show_icon( 'social-twitter' ); ?>
			</a>
		<?php endif; ?>

		<?php if ( $enable_facebook ) : ?>
			<a class="share-article__link" href="<?php echo esc_url( wmf_get_share_url( 'facebook' ) ); ?>" target="_blank" rel="noreferrer noopener">
				<?php wmf_show_icon( 'social-facebook' ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
