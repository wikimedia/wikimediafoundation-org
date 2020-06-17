<?php
/**
 * Front Page Template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package shiro
 */

get_header();
while ( have_posts() ) {
	the_post();
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
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-<?php the_ID(); ?> -->
<?php
}
get_footer();
