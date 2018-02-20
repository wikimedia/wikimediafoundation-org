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

?>

<?php
$team_name = get_the_terms( get_the_ID(), 'team' );
$team_name = 'test';

wmf_get_template_part(
	'template-parts/header/profile-single',
	array(
		'back_to_link'  => get_post_type_archive_link( 'profile' ),
		// TODO: Need to confirm that this is either Staff or Community / separate taxonomy.
		'back_to_label' => 'Staff',
		'role'          => get_post_meta( get_the_ID(), 'profile_role', true ),
		'team_name'     => $team_name,
		'share_links'   => get_post_meta( get_the_ID(), 'contact_links', true ),
	)
);
?>

<?php
wmf_get_template_part(
	'template-parts/thumbnail-framed',
	array(
		'container_image' => get_theme_mod( 'wmf_profile_container_image' ),
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
