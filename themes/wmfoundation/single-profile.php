<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wmfoundation
 */

use WMF\Images\Credits;

get_header();

while ( have_posts() ) :
	the_post();

	?>

	<?php
	$team_name       = '';
	$parent_name     = '';
	$parent_link     = '';
	$role            = get_the_terms( get_the_ID(), 'role' );
	$default_heading = get_theme_mod( 'wmf_related_profiles_heading', __( 'Other members of ', 'wmfoundation' ) );

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
			'inner_image'     => get_post_thumbnail_id( get_the_ID() ),
			'container_class' => '',
		)
	);
	?>

<article class="mw-900">
	<div class="article-main mod-margin-bottom wysiwyg">
		<?php the_content(); ?>
	</div>
</article>

	<?php

	get_template_part( 'template-parts/page/page', 'offsite-links' );

	Credits::get_instance()->pause();
	$template_args                  = get_post_meta( get_the_ID(), 'profiles', true );
	$template_args['profiles_list'] = wmf_get_related_profiles( get_the_ID() );
	$template_args['headline']      = ! empty( $template_args['headline'] ) ? $template_args['headline'] : $default_heading . $team_name;
	wmf_get_template_part( 'template-parts/modules/profiles/list', $template_args );
endwhile;
Credits::get_instance()->resume();

get_template_part( 'template-parts/page/page', 'listings' );

get_template_part( 'template-parts/profiles/related-posts' );

get_footer();
