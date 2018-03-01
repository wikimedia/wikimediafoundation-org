<?php
/**
 * Read More links for categories in post
 *
 * @package wmfoundation
 */

$post_categories = wp_get_post_categories(
	get_the_ID(), array(
		'fields' => 'names',
	)
);

if ( empty( $post_categories ) || is_wp_error( $post_categories ) ) {
	return;
}

?>
<div class="read-more flex flex-medium">
	<h5 class="h5 color-black">Read More</h5>
	<ul class="link-list inline-block">
		<?php foreach ( $post_categories as $category ) : ?>
		<li>
			<a href="<?php echo esc_url( get_term_link( $category, 'category' ) ); ?>"><?php echo esc_html( $category ); ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
