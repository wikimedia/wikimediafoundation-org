<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wmfoundation
 */

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/header/profile-single' );

?>

<?php
rkv_get_template_part( 'template-parts/thumbnail-framed',
	array(
		'container_image' => 'https://muledesign.github.io/wmf-pl-prototypes/images/staff-profile-pattern.jpg',
		'inner_image'     => get_post_thumbnail_id( get_the_ID() ),
		'container_class' => 'mod-margin-bottom',
	)
);
?>

<article class="mw-900">
	<div class="article-main mod-margin-bottom wysiwyg">
		<?php the_content(); ?>
	</div>
</article>

<?php
endwhile;

get_footer();
