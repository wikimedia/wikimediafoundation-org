<?php
/**
 * Template Name: Report Landing Page
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
		'h2_title' => get_the_title(),
		'h1_title' => get_post_meta( get_the_ID(), 'sub_title', true ),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		get_template_part( 'template-parts/header/header-content', null, $template_args );
	} else {
		get_template_part( 'template-parts/header/page-noimage', null, $template_args );
	}

	get_template_part( 'template-parts/header/index' );
	?>

	<div class="mw-980 mod-margin-bottom flex flex-medium report-template toc__section">
		<div class="w-32p toc__sidebar">
			<?php get_sidebar( 'report' ); ?>
		</div>

		<div class="w-68p report-content toc__content">
			<?php get_template_part( 'template-parts/page/page', 'intro' ); ?>
		</div>
	</div>

	<?php
	$modules = array(
		'support',
	);

	foreach ( $modules as $module ) {
		get_template_part( 'template-parts/page/page', $module );
	}
}
get_footer();
