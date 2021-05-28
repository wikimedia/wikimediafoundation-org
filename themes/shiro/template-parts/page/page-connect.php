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

$reusable_block_id = get_theme_mod( 'wmf_connect_reusable_block' );

if ( $no_custom_connect
     && is_numeric( $reusable_block_id )
     && $reusable_block_id > 0
     && get_post_type( $reusable_block_id ) === 'wp_block' ) {
	$block = get_post( $reusable_block_id );
	if ( is_a( $block, \WP_Post::class ) ) { ?>
		<?php /** Since we're not in the "content" area, these blocks need
		 * a wrapper with a set width or they look real strange. */ ?>
		<div class="mw-980">
			<?php echo apply_filters( 'the_content', $block->post_content ) ?>
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
