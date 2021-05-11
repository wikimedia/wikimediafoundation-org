<?php
/**
 * Server-side registration for the shiro/article block.
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

	$terms = wp_get_object_terms(
		get_the_ID(),
		array( 'post_tag', 'category' ),
		array( 'fields' => 'id=>name' )
	);

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	ob_start();
	?>

	<div class="read-more-categories">
		<span class="read-more-categories__text">
			<?php echo esc_html( $attributes['readMoreText'] ?? __( 'Read more', 'shiro' ) ); ?>
		</span>
		<span class="read-more-categories__links">
			<?php $i = 0; ?>
			<?php foreach ( $terms as $term_id => $term_title ) : ?>
				<?php
					$term_link = get_term_link( $term_id );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}

					$is_last = ++$i === count( $terms );
				?>
				<a href="<?php echo esc_attr( $term_link ); ?>"><?php echo esc_html( $term_title ); ?></a><?php
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
