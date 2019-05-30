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
 * @package shiro
 */

get_header();

$post_id          = get_option( 'page_for_posts' );
$featured_post_id = get_post_meta( $post_id, 'featured_post', true );

$template_args = array(
	'h2_title' => get_the_title( $post_id ),
);

wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );

?>

<div class="w-100p cta mod-margin-bottom cta-secondary img-left-content-right cta-news header-featured-news no-duotone">
	<div class="mw-980">

	<?php
	$post = get_post( $featured_post_id );
	if ( ! empty( $post ) ) {
		setup_postdata( $post );
		$featured_post_id = (int) $post->ID;
		wmf_get_template_part(
			'template-parts/modules/cards/card-featured', array(
				'link'       => get_the_permalink(),
				'image_id'   => get_post_thumbnail_id(),
				'title'      => get_the_title(),
				'authors'    => wmf_byline(),
				'date'       => get_the_date(),
				'excerpt'    => get_the_excerpt(),
				'categories' => get_the_category(),
			)
		);

		wp_reset_postdata();
	}
	?>
	</div>
</div>

<?php get_template_part( 'template-parts/category-list' ); ?>

<div class="w-100p news-list-container news-card-list mod-margin-bottom">
	<div class="mw-980">
		<?php if ( have_posts() ) : ?>
			<div class="card-list-container">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( get_the_ID() === intval( $featured_post_id ) ) {
					continue;
				}

				wmf_get_template_part(
					'template-parts/modules/cards/card-horizontal', array(
						'link'       => get_the_permalink(),
						'image_id'   => get_post_thumbnail_id(),
						'title'      => get_the_title(),
						'authors'    => wmf_byline(),
						'date'       => get_the_date(),
						'excerpt'    => get_the_excerpt(),
						'categories' => get_the_category(),
					)
				);
			endwhile;
			?>
			</div>
			<?php
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</div>
</div>

<?php
if ( have_posts() ) :
	get_template_part( 'template-parts/pagination' );
endif;

$modules = array(
	'support',
	'connect',
);

foreach ( $modules as $module ) {
	get_template_part( 'template-parts/page/page', $module );
}

get_footer();
