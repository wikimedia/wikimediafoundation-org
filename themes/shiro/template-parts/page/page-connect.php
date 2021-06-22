<?php
/**
 * Sets up page Connect module.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'connect', true );

// Determine if this is using a legacy customization to the connect text
$no_custom_connect = empty( array_filter( $template_args ) );

$reusable_block = wmf_get_reusable_block_module( 'connect' );

if ( $no_custom_connect && $reusable_block ) {
	if ( is_a( $reusable_block, \WP_Post::class ) ) { ?>
		<?php /** Since we're not in the "content" area, these blocks need
		 * a wrapper with a set width or they look real strange. */ ?>
		<div class="block-area">
			<div class="wysiwyg mw-980">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters( 'the_content', $reusable_block->post_content );
				// phpcs:enable
				?>
			</div>
		</div>
	<?php }
} else {
	$template_args = empty( $template_args ) || is_string( $template_args ) ? array() : $template_args;

	$rand_translation = wmf_get_random_translation(
		'connect', array(
			'source' => 'meta',
		)
	);

	$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

	if ( empty( $template_args['hide'] ) ) {
		get_template_part( 'template-parts/modules/general/connect', null, $template_args );
	}
}
