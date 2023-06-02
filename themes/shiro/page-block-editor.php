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

	$template_args = [];

	$blocks = parse_blocks( get_post()->post_content );
	$first_block = $blocks[0]['blockName'];
	$show_title = (
			$first_block !== 'shiro/landing-page-hero' &&
			$first_block !== 'shiro/home-page-hero' &&
			$first_block !== 'shiro/report-landing-hero'
	);

	if ( $show_title ) {
		$template_args['h1_title'] = get_the_title();
	}

	/**
	 * Breadcrumb link switch
	 *
	 * Possible values of the switch:
	 * 1. '' - meta data from component not set (page wasn't yet edited with this component on the page)
	 * 2. 'on' - set to yes
	 * 3. 'off' - set to no
	 */
	$parent_page = wp_get_post_parent_id( get_the_ID() );
	$show_breadcrumb = false;
	$breadcrumb_link_switch = get_post_meta( get_the_ID(), 'show_breadcrumb_links', true );
	if ( $breadcrumb_link_switch === 'on' ) {
		$breadcrumb_link_custom_title = get_post_meta( get_the_ID(), 'breadcrumb_link_title', true );
		$breadcrumb_link_title = ( ! empty( $breadcrumb_link_custom_title ) ) ? $breadcrumb_link_custom_title : get_the_title( $parent_page );

		$breadcrumb_link_custom_url = get_post_meta( get_the_ID(), 'breadcrumb_link_url', true );
		$breakcrumb_link = ( ! empty( $breadcrumb_link_custom_url ) ) ? $breadcrumb_link_custom_url : get_the_permalink( $parent_page );

		$template_args['h4_link'] = $breakcrumb_link;
		$template_args['h4_title'] = $breadcrumb_link_title;

		$show_breadcrumb = true;
	} elseif ( $breadcrumb_link_switch === 'off' ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedElseif
		// Does nothing.
	} elseif ( $breadcrumb_link_switch === '' && $show_title ) {
		// Default behavior.
		if ( ! empty( $parent_page ) ) {
			$template_args['h4_link'] = get_the_permalink( $parent_page );
			$template_args['h4_title'] = get_the_title( $parent_page );
			$show_breadcrumb = true;
		}
	}

	if ( $show_title || $show_breadcrumb ) {
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
