<?php
/**
 * Server-side registration for the shiro/read-more-categories block.
 */

namespace WMF\Editor\Blocks\ReadMoreCategories;

const BLOCK_NAME = 'shiro/read-more-categories';

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
	$categories = get_the_terms( get_the_ID(), 'category' ) ?: [];
	$tags = get_the_terms( get_the_ID(), 'post_tag' ) ?: [];
	$terms = array_merge( $categories, $tags );

	usort( $terms, function( $a, $b ) {
		return strcmp( $a->name, $b->name );
	} );

	ob_start();
	?>

	<div class="read-more-categories">
		<span class="read-more-categories__text">
			<?php echo esc_html( $attributes['readMoreText'] ?? __( 'Read more', 'shiro' ) ); ?>
		</span>
		<span class="read-more-categories__links">
			<?php $i = 0; ?>
			<?php foreach ( $terms as $term ) : ?>
				<?php
					$term_link = get_term_link( $term->term_id );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}

					$is_last = ++$i === count( $terms );
				?>
				<a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term->name ); ?></a><?php
					if ( ! $is_last ) {
						echo ',';
					}
				?>
			<?php endforeach; ?>
		</span>
	</div>
	<?php
	return ob_get_clean();
}
