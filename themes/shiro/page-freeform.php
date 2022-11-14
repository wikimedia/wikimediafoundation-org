<?php
/**
 * Template Name: Freeform
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

get_header();
while ( have_posts() ) {
	the_post();

	// Page Header.
	$parent_page   = wp_get_post_parent_id( get_the_ID() );
	$template_args = array(
		'h4_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'h4_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		'h2_title' => '',
		'h1_title' => '',
	);

	get_template_part( 'template-parts/header/page', 'noimage', $template_args );

	?>
		<div class="freeform-content">
			<?php the_content(); ?>
		</div>

	<?php
	get_template_part( 'template-parts/page/page', 'connect' );
}
get_footer();
