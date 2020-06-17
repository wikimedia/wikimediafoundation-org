<?php
/**
 * Placeholder shortcode for the FM support field.
 */

namespace WMF\Shortcodes\Translation_Bar;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_translation_bar', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_translation_bar] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function shortcode_callback( $atts = [] ) {
	$html = shortcode_content( get_the_ID() );

	return wp_kses_post( $html );
}

/**
 * Render shortcode HTML.
 *
 * @param int $post_id The post ID.
 */
function shortcode_content( int $post_id ) {
	$wmf_translations = wmf_get_translations();
	if ( false === $wmf_translations ) {
		return '';
	}
	ob_start(); ?>
	<div class="translation-bar">
		<div class="translation-bar-inner mw-980">
			<ul class="list-inline">
				<?php foreach ( $wmf_translations as $wmf_index => $wmf_translation ) : ?>
					<?php if ( 0 !== $wmf_index ) : ?>
						<li class="divider">&middot;</li>
					<?php endif; ?>
					<li>
						<?php if ( $wmf_translation['selected'] ) : ?>
							<span><?php echo esc_html( $wmf_translation['name'] ); ?></span>
						<?php else : ?>
							<span lang="<?php echo esc_attr( $wmf_translation['shortname'] ); ?>"><a
									href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( count( $wmf_translations ) > 10 ) : ?>
				<div class="arrow-wrap">
				<span>
					<span class="elipsis">&hellip;</span>
					<?php wmf_show_icon( 'trending', 'icon-turquoise material' ); ?>
				</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}
