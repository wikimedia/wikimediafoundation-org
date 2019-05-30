<?php
/**
 * List of categories that refreshes page with category filter.
 *
 * @package shiro
 */

$post_id             = get_option( 'page_for_posts' );
$featured_categories = get_post_meta( $post_id, 'featured_categories', true );

if ( empty( $featured_categories ) ) {
	return;
}

$url              = get_the_permalink( $post_id );
$current_category = get_query_var( 'cat' );
$cat_anchor       = 'card-list';

if ( ! empty( $featured_categories ) ) :
	?>
<div class="news-categories" id="<?php echo esc_attr( $cat_anchor ); ?>">
	<div class="news-category-inner mw-1360">
		<ul class="link-list color-gray uppercase bold slider-on-mobile">
			<?php
			foreach ( $featured_categories as $i => $category ) :
				$term  = get_term( absint( $category ) );
				$link  = add_query_arg( 'cat', $category, $url ) . '#' . $cat_anchor;
				$class = (int) $category === (int) $current_category ? 'border-turquoise' : '';
				?>
				<?php if ( 0 !== $i ) : ?>
					<li aria-hidden="true">—</li>
				<?php endif; ?>

				<li class="<?php echo esc_attr( $class ); ?>">
					<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
	<?php
endif;
