<?php
/**
 * Server-side registration for the shiro/blog-post block.
 */

namespace WMF\Editor\Blocks\DoubleHeading;

const BLOCK_NAME = 'shiro/double-heading';

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
	$site_language = wmf_get_translations()[0];
	$translated_headings = [];
	$site_language_heading = null;

	foreach ( $attributes['secondaryHeadings'] as $heading ) {
		if ( $site_language['shortname'] === ( $heading['lang'] ?? '' ) ) {
			$site_language_heading = $heading;
			continue;
		}

		if ( trim( $heading['text'] ) === '' ) {
			continue;
		}

		$heading['className'] = '';
		if ( $heading['switchRtl'] ) {
			$heading['className'] = 'switch-rtl';
		}
		$translated_headings[] = $heading;
	}

	$random_key         = array_rand( $translated_headings );
	$translated_heading = $translated_headings[ $random_key ];

	ob_start()
	?>
		<div class="double-heading">
			<?php if ( ! empty( $site_language_heading ) ) : ?>
				<p class="double-heading__secondary is-style-h5">
					<span><?php echo esc_html( $site_language_heading['text'] ) ?></span>
					<?php if ( ! empty( $translated_heading ) ) : ?>
						—
						<span
							class="<?php echo esc_attr( $translated_heading['className'] ); ?>"
							lang="<?php echo esc_attr( $translated_heading['lang'] ?? '' ) ?>"
						>
							<?php echo esc_html( $translated_heading['text'] ) ?>
						</span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<h2 class="double-heading__primary is-style-h3">
				<?php echo esc_html( $attributes['primaryHeading'] ) ?>
			</h2>
		</div>
	<?php
	return ob_get_clean();
}
