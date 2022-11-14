<?php
/**
 *
 * Template for displaying search results
 *
 * @package shiro
 */

get_header(); ?>

<?php

$wmf_results_copy = get_theme_mod( 'wmf_search_results_copy', /* translators: 1. search query */ __( 'Search results for %s', 'shiro-admin' ) );

$template_args = array(
	/* translators: Query that is currently being searched */
	'h1_title' => sprintf( __( $wmf_results_copy, 'shiro' ), get_search_query() ),
);

get_template_part( 'template-parts/header/page-noimage', null, $template_args );

?>

<div class="mw-980 mod-margin-bottom flex flex-medium news-card-list">
	<div id="search-results" class="card-list-container">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();

				echo WMF\Editor\Blocks\BlogPost\render_block(
					[ 'post_id' => $post->ID ]
				);
			endwhile;
			?>

			<?php
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>
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
