<?php
/**
 *
 * Template for displaying search results
 *
 * @package wmfoundation
 */

get_header(); ?>

<?php

$wmf_results_copy      = get_theme_mod( 'wmf_search_results_copy', __( 'Search results for %s', 'wmfoundation' ) );

$template_args = array(
	/* translators: Query that is currently being searched */
	'h1_title' => sprintf( __( $wmf_results_copy, 'wmfoundation' ), get_search_query() ),
);

wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );

?>

<div class="mw-1360 mod-margin-bottom flex flex-medium news-card-list">
	<div id="search-results" class="card-list-container w-68p">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();

				wmf_get_template_part(
					'template-parts/modules/cards/card-horizontal', array(
						'link'       => get_the_permalink(),
						'image_id'   => get_post_thumbnail_id(),
						'title'      => get_the_title(),
						'authors'    => wmf_byline(),
						'date'       => get_the_date(),
						'excerpt'    => get_the_excerpt(),
						'categories' => get_the_category(),
						'sidebar'    => true,
					)
				);
			endwhile;
			?>

			<?php
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>

	<?php
	if ( have_posts() ) {
		get_sidebar( 'search' );
	}
	?>
</div>

<div id="pagination">
	<?php
	if ( have_posts() ) :
		get_template_part( 'template-parts/pagination' );
	endif;
	?>
</div>

<?php
get_footer();
