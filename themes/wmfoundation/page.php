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
wmf_get_template_part(
	'template-parts/header/page-default',
	array(
		'parent_section_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'parent_section_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
	)
);

get_template_part( 'template-parts/content', 'page' );

endwhile;
get_footer();
