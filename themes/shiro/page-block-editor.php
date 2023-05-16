<?php
/**
 * The template for displaying block editor pages.
 *
 * This is a complete replacement of page.php when all pages are converted to
 * blocks.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

get_header();

while ( have_posts() ) {
	the_post();

	$blocks = parse_blocks( get_post()->post_content );
	$first_block = $blocks[0]['blockName'];
	$show_title = (
			$first_block !== 'shiro/landing-page-hero' &&
			$first_block !== 'shiro/home-page-hero' &&
			$first_block !== 'shiro/report-landing-hero'
	);

	if ( $show_title ) {
		$template_args = array(
			'h1_title' => get_the_title(),
		);

		$breadcrumb_link_switch = get_post_meta( get_the_ID(), 'show_breadcrumb_links', true );
		if ( $breadcrumb_link_switch ) {
			$parent_page = wp_get_post_parent_id( get_the_ID() );

			$breadcrumb_link_custom_title = get_post_meta( get_the_ID(), 'breadcrumb_link_title', true );
			$breadcrumb_link_title = ( ! empty( $breadcrumb_link_custom_title ) ) ? $breadcrumb_link_custom_title : get_the_title( $parent_page );

			$breadcrumb_link_custom_url = get_post_meta( get_the_ID(), 'breadcrumb_link_url', true );
			$breakcrumb_link = ( ! empty( $breadcrumb_link_custom_url ) ) ? $breadcrumb_link_custom_url : get_the_permalink( $parent_page );

			$template_args = array(
				'h4_link'  => $breakcrumb_link,
				'h4_title' => $breadcrumb_link_title,
			);
		}

		get_template_part( 'template-parts/header/page', 'noimage', $template_args );
	} else {
		// Fake header content so we get the same margin before the hero blocks.
		?>
		<div class="header-content"></div>
		<?php
		get_template_part( 'template-parts/header/closing-tags' );
	}


	get_template_part( 'template-parts/content', 'page' );
}
get_footer();
