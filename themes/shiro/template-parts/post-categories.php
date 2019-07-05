<?php
/**
 * Read More links for categories in post
 *
 * @package shiro
 */

$post_categories = wp_get_object_terms(
	get_the_ID(), array( 'post_tag', 'category' ), array(
		'fields' => 'id=>name',
	)
);

if ( empty( $post_categories ) || is_wp_error( $post_categories ) ) {
	return;
}

?>
<div class="read-more flex flex-medium">
	<h5 class="h5 color-black">Read More</h5>
	<ul class="link-list inline-block">
		<?php foreach ( $post_categories as $cat_id => $category ) : ?>
			<?php
			$term_link = get_term_link( $cat_id );
			if ( is_wp_error( $term_link ) ) {
				continue;
			}
			?>
		<li><a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $category ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
