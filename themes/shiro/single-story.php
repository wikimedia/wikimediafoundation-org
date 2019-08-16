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
	$roleterm        = get_the_terms( get_the_ID(), 'type' );
	$parent_name     = $roleterm[0]->name;
	$parent_link     = get_term_link( $roleterm[0] );

	if ( ! empty( $roleterm ) && ! is_wp_error( $roleterm ) ) {
		$team_name = $roleterm[0]->name;
		$ancestors = get_ancestors( $roleterm[0]->term_id, 'role' );
		$parent_id = is_array( $ancestors ) ? end( $ancestors ) : false;

		if ( $parent_id ) {
			$parent_term = get_term( $parent_id );
			$parent_name = $parent_term->name;
			$parent_link = get_term_link( $parent_id );
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
