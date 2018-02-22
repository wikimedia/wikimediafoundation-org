<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

get_header(); ?>

<?php
$template_args = array(
	'h1_title' => get_the_archive_title(),
);

if ( ! is_home() ) {
	$posts_page                = get_option( 'page_for_posts' );
	$template_args['h4_link']  = get_permalink( $posts_page );
	$template_args['h4_title'] = get_the_title( $posts_page );
}

wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );

?>

<div class="w-100p news-list-container news-card-list mod-margin-bottom">
	<div class="mw-1360">
		<?php if ( have_posts() ) : ?>
		<div class="card-list-container">
			<?php
			while ( have_posts() ) :
				the_post();

				wmf_get_template_part(
					'template-parts/modules/cards/card-horizantal', array(
						'link'       => get_the_permalink(),
						'image_id'   => get_post_thumbnail_id(),
						'title'      => get_the_title(),
						'authors'    => get_the_author_link(),
						'date'       => get_the_date(),
						'excerpt'    => get_the_excerpt(),
						'categories' => get_the_category(),
					)
				);


			?>


			<?php endwhile; ?>

		</div>
		<?php else : ?>
		<div>No results</div>
		<?php endif; ?>
	</div>
</div>

<?php if ( have_posts() ) : ?>
	<?php get_template_part( 'template-parts/pagination' ); ?>
<?php endif; ?>
<?php
get_footer();
