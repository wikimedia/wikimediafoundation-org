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
$team_name   = '';
$parent_name = '';
$parent_link = '';
$role        = get_the_terms( get_the_ID(), 'role' );

if ( ! empty( $role ) && ! is_wp_error( $role ) ) {
	$team_name = $role[0]->name;
	$ancestors = get_ancestors( $role[0]->term_id, 'role' );
	$parent_id = is_array( $ancestors ) ? end( $ancestors ) : false;

	if ( $parent_id ) {
		$parent_term = get_term( $parent_id );
		$parent_name = $parent_term->name;
		$parent_link = get_term_link( $parent_id );
	}
}

wmf_get_template_part(
	'template-parts/header/profile-single',
	array(
		'back_to_link'  => $parent_link,
		'back_to_label' => $parent_name,
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

// Related Profiles.
$template_args = get_post_meta( get_the_ID(), 'profiles', true );
if ( ! empty( $template_args ) ) {
	$template_args['profiles_list'] = wmf_get_related_profiles( get_the_ID() );
	wmf_get_template_part( 'template-parts/modules/profiles/list', $template_args );
}
endwhile;

get_footer();
