<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

get_header();
while ( have_posts() ) :
	the_post();
?>

<?php
$parent_page = wp_get_post_parent_id( get_the_ID() );

if ( has_post_thumbnail() ) :
	wmf_get_template_part(
		'template-parts/header/page-image',
		array(
			'img'                  => get_the_post_thumbnail_url( get_the_ID(), 'large' ),
			'parent_section_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
			'parent_section_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		)
	);
else :
	wmf_get_template_part(
		'template-parts/header/page-noimage',
		array(
			'parent_section_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
			'parent_section_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		)
	);
endif;

get_template_part( 'template-parts/content', 'page' );

endwhile;
get_footer();
