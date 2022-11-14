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

get_template_part( 'template-parts/header/page-noimage', null, $template_args );

?>

<div class="blog-list">

	<?php
	$post = get_post( $featured_post_id );
	if ( ! empty( $post ) ) {
		$featured_post_id = (int) $post->ID;
		echo wp_kses_post( WMF\Editor\Blocks\BlogPost\render_block(
			[
				'post_id' => $featured_post_id,
				'is_featured' => true,
			]
		) );
	}
	?>

	<?php get_template_part( 'template-parts/category-list' ); ?>

	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			if ( get_the_ID() === intval( $featured_post_id ) ) {
				continue;
			}

			echo wp_kses_post( WMF\Editor\Blocks\BlogPost\render_block(
				[ 'post_id' => $post->ID ]
			) );
		endwhile;

	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;
	?>

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
