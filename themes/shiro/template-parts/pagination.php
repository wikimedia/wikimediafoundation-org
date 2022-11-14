<?php
/**
 * Adding pagination links
 *
 * @package shiro
 */

$newer = get_theme_mod( 'wmf_pagination_newer', __( 'Newer', 'shiro-admin' ) );
$older = get_theme_mod( 'wmf_pagination_older', __( 'Older', 'shiro-admin' ) );

$previous_arrow = <<<SVG
<svg fill="none" height="18" viewBox="0 0 12 18" width="12" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m9.75 0-9 9 9 9 1.5-1.5-7.5-7.5 7.5-7.5z" fill="#000" fill-rule="evenodd"/></svg>
SVG;

$next_arrow = <<<SVG
<svg fill="none" height="18" viewBox="0 0 12 18" width="12" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m2.25 0-1.5 1.5 7.499 7.5-7.499 7.5 1.5 1.5 9-9z" fill-rule="evenodd"/></svg>
SVG;

$additional_args = get_query_var( 'search_args' );

$base = get_query_var( 'pagination_base' );

$pagination_args = array(
	'prev_next' => false,
	'type'      => 'list',
);

if ( ! empty( $base ) ) {
	$pagination_args['base'] = $base;
}

if ( ! empty( $additional_args ) ) {
	$pagination_args['add_args'] = $additional_args;
}
?>

<div class="pagination">
	<div class="pagination__inner">
		<div class="pagination__previous-page">
			<?php previous_posts_link( $previous_arrow . $newer ); ?>
		</div>

		<div class="pagination__page-numbers">
			<?php
				echo wp_kses_post(
					paginate_links( $pagination_args )
				);
			?>
		</div>

		<div class="pagination__next-page">
			<?php next_posts_link( $older . $next_arrow ); ?>
		</div>
	</div>
</div>
