<?php
/**
 * Server-side registration for the shiro/share-article block.
 */

namespace WMF\Editor\Blocks\HomePageHeroRotator;

const BLOCK_NAME = 'shiro/home-page-hero-rotator';

/**
 * Bootstrap this block functionality.
 */
function bootstrap() {
	//add_filter( 'render_block', __NAMESPACE__ . '\\render_block', 10, 3 );
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
function render_block( $attributes, $block_content ) {
	ob_start();
?>
	<nav class="hero-home__controls">
		<button class="hero-home__controls--previous">
			<?php wmf_show_icon( 'down' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Previous', 'shiro' ); ?></span>
		</button>
		<button class="hero-home__controls--next">
			<?php wmf_show_icon( 'down' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Next', 'shiro' ); ?></span>
		</button>
	</nav>
</div>
<?php
	$rotator_nav = ob_get_clean();

	// Trim the ending '</div> tag so that we can insert the nav buttons before it.
	return substr( rtrim( $block_content ), 0, -6 ) . $rotator_nav;
}
