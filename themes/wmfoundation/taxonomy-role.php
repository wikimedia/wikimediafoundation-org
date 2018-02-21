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

$parent_section_title = '';
$parent_section_link = '';

$profile_parent_page = get_theme_mod( 'wmf_profile_parent_page' );
if ( ! empty( $profile_parent_page ) ) {
	$parent_section_title = get_the_title( $profile_parent_page );
	$parent_section_link = get_the_permalink( $profile_parent_page );

}
?>

<?php
	wmf_get_template_part(
		'template-parts/header/page-noimage',
		array(
			'title' => 'Staff and Contractors',
			'proilf'
		)
	);

//get_template_part( 'template-parts/content', 'page' );
get_footer();
