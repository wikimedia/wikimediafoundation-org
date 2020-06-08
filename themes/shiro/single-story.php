<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package shiro
 */

use WMF\Images\Credits;

get_header();

while ( have_posts() ) :
	the_post();

	?>

	<?php
	$roleterm    = get_the_terms( get_the_ID(), 'type' );
	$parent_name = ! empty( $roleterm ) ? $roleterm[0]->name : '';
	$parent_link = ! empty( $roleterm ) ? get_term_link( $roleterm[0] ) : '';

	if ( ! empty( $roleterm ) && ! is_wp_error( $roleterm ) ) {
		$team_name = $roleterm[0]->name;
		$ancestors = get_ancestors( $roleterm[0]->term_id, 'role' );
		$parent_id = is_array( $ancestors ) ? end( $ancestors ) : false;

		if ( $parent_id ) {
			$parent_term = get_term( $parent_id );
			$parent_name = $parent_term->name;
			$parent_link = get_term_link( $parent_id );
		}
	} else {
		// The Stories page which contains the list of stories sets the following post meta to the page ID on the
		// 'update_post_meta' hook.
		$parent_page_id = get_post_meta( get_the_ID(), '_story_parent_page', true );
		if ( (int) $parent_page_id > 0 ) {
			$parent_page = get_post( $parent_page_id );
			if ( $post instanceof \WP_Post ) {
				$parent_link = get_permalink( $parent_page->ID );
				$parent_name = sprintf( __( '%s stories' ), get_the_title( $parent_page->post_parent ) );
			}
		}
	}

	wmf_get_template_part(
		'template-parts/header/story-single',
		array(
			'back_to_link'  => $parent_link,
			'back_to_label' => $parent_name,
			'share_links'   => get_post_meta( get_the_ID(), 'contact_links', true ),
		)
	);

	$share_links = get_post_meta( get_the_ID(), 'contact_links', true );
	?>

	<div class="mw-980 mar-bottom">
		<div class="flex flex-medium flex-space-between mar-bottom_lg">
			<div class="w-48p">
				<?php
				wmf_get_template_part(
					'template-parts/thumbnail-framed',
					array(
						'inner_image'     => get_post_thumbnail_id( get_the_ID() ),
						'container_class' => '',
					)
				);
				?>
			</div>
			</div>
			<div class="w-50p">
				<div class="article-main mod-margin-bottom wysiwyg">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</div>

	<?php

	get_template_part( 'template-parts/page/page', 'offsite-links' );
endwhile;

get_footer();
