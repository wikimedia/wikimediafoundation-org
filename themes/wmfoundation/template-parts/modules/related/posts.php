<?php
/**
 * Related posts module
 *
 * @package wmfoundation
 */

$posts = wmf_get_template_data();

if ( empty( $posts ) ) {
	return;
}

?>

<div class="w-100p news-list-container mod-margin-bottom">
	<div class="mw-1360">
		<h3 class="h3 color-gray">
			Related
		</h3>

		<h2 class="h2">
			Read further in the pursuit of knowledge
		</h2>

		<div class="card-list-container alternate-img">
			<?php
			foreach ( $posts as $post ) :
				setup_postdata( $post );
				wmf_get_template_part(
					'template-parts/modules/cards/card-horizontal', array(
						'title'      => get_the_title(),
						'link'       => get_the_permalink(),
						'image_id'   => get_post_thumbnail_id(),
						'authors'    => get_the_author_link(),
						'date'       => get_the_date(),
						'excerpt'    => get_the_excerpt(),
						'categories' => get_the_category(),

					)
				);
				wp_reset_postdata();
		endforeach;
		?>
		</div>
	</div>
</div>
