<?php
/**
 * Ajax actions and hooks.
 *
 * @package wmfoundation
 */

 /**
  * Set up search AJAX endpoint.
  */
function wmf_ajax_search() {
	$post_types = ! empty( $_POST['post_type'] ) ? (array) $_POST['post_type'] : '';

	if ( is_array( $post_types ) ) {
		$post_types = array_filter( $post_types, 'post_type_exists' );
	}

	$keyword = ! empty( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';

	$default_args = array(
		'post_status' => 'publish',
	);
	$custom_args  = array(
		'post_type' => $post_types,
		's'         => $keyword,
	);
	$custom_args  = array_filter( $custom_args );
	$args         = wp_parse_args( $custom_args, $default_args );

	$search_query = new WP_Query( $args );

	$posts_html = '';
	ob_start();
	if ( $search_query->have_posts() ) {
		while ( $search_query->have_posts() ) :
			$search_query->the_post();
			wmf_get_template_part(
				'template-parts/modules/cards/card-horizontal', array(
					'link'       => get_the_permalink(),
					'image_id'   => get_post_thumbnail_id(),
					'title'      => get_the_title(),
					'authors'    => get_the_author_link(),
					'date'       => get_the_date(),
					'excerpt'    => get_the_excerpt(),
					'categories' => get_the_category(),
					'sidebar'    => true,
				)
			);
		endwhile;
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	$posts_html = ob_get_clean();

	$pagination = '';

	if ( $search_query->have_posts() ) {
		global $wp_query;
		$wp_query = $search_query;
		set_query_var( 'search_args', $custom_args );
		ob_start();
		get_template_part( 'template-parts/pagination' );
		$pagination = ob_get_clean();
	}

	wp_send_json_success(
		array(
			'posts_html' => wp_kses_post( $posts_html ),
			'pagination' => wp_kses_post( $pagination ),
		), 200
	);
}
add_action( 'wp_ajax_nopriv_ajax_search', 'wmf_ajax_search' );
add_action( 'wp_ajax_ajax_search', 'wmf_ajax_search' );
