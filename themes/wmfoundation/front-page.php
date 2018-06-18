<?php
/**
 * Front Page Template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wmfoundation
 */

get_header();
while ( have_posts() ) {
	the_post();

	// Page Header.
	$parent_page   = wp_get_post_parent_id( get_the_ID() );
	$subtitle      = get_post_meta( get_the_ID(), 'sub_title', true );
	$template_args = array(
		'h4_link'  => ! empty( $parent_page ) ? get_the_permalink( $parent_page ) : '',
		'h4_title' => ! empty( $parent_page ) ? get_the_title( $parent_page ) : '',
		'h1_title' => get_the_title(),
	);

	if ( has_post_thumbnail() ) {
		$template_args['image'] = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		wmf_get_template_part( 'template-parts/header/page-image', $template_args );
	} else {
		wmf_get_template_part( 'template-parts/header/page-noimage', $template_args );
	}

?>
<div class="site-main-nav home-subnav mw-1360 white-bg">
	<div class="logo-nav-container">
		<?php get_template_part( 'template-parts/header/logo' ); ?>
		<?php get_template_part( 'template-parts/header/nav-container' ); ?>
	</div>
	<?php get_template_part( 'template-parts/header/navigation' ); ?>
</div>

<div class="page-intro mw-1360 mod-margin-bottom wysiwyg">
	<div class="w-75p">
		<h2><?php echo esc_html( $subtitle ); ?></h2>

		<div class="page-intro-text">
			<?php the_content(); ?>
		</div>

	</div>
</div>
<?php

	$modules = array(
		'focus-blocks',
		'featured-posts',
		'projects',
		'profiles',
		'facts',
		'framing-copy',
		'support',
		'connect',
	);

foreach ( $modules as $module ) {
	get_template_part( 'template-parts/page/page', $module );
}
}
get_footer();
