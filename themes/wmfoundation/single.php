<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wmfoundation
 */

get_header();
while ( have_posts() ) {
	the_post();
	$intro       = get_post_meta( get_the_ID(), 'page_intro', true );
	$parent_page = get_option( 'page_for_posts' );

	wmf_get_template_part(
		'template-parts/header/page-noimage', array(
			'h4_link'   => get_the_permalink( $parent_page ),
			'h4_title'  => get_the_title( $parent_page ),
			'h1_title'  => get_the_title(),
			'page_meta' => sprintf( '<span>%s</span><time>%s</time>', wmf_byline(), get_the_date() ),
		)
	);

	wmf_get_template_part(
		'template-parts/thumbnail-framed',
		array(
			'inner_image' => get_post_thumbnail_id(),
		)
	);
	?>

	<?php if ( ! empty( $intro ) ) : ?>
	<div class="article-title mw-900">
		<h3 class="h3"><?php echo wp_kses_post( $intro ); ?></h3>
	</div>
	<?php endif; ?>

	<article class="mw-900 wysiwyg">
		<?php the_content(); ?>
	</article>

	<div class="article-footer mw-900 mod-margin-bottom">
		<?php get_template_part( 'template-parts/post-categories' ); ?>

		<?php
		wmf_get_template_part(
			'template-parts/modules/social/share-horizontal', array(
				'services' => get_post_meta( get_the_ID(), 'share_links', true ),
			)
		);
		?>
	</div>

	<?php
}

$modules = array(
	'profile',
	'offsite-links',
	'cta',
	'related-posts',
	'support',
	'connect',
);

foreach ( $modules as $module ) {
	get_template_part( 'template-parts/page/page', $module );
}
get_footer();
