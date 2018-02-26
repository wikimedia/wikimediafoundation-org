<?php
/**
 * Adding pagination links
 *
 * @package wmfoundation
 */

$newer = get_theme_mod( 'wmf_pagination_newer', __( 'Newer', 'wmfoundation' ) );
$older = get_theme_mod( 'wmf_pagination_older', __( 'Older', 'wmfoundation' ) );

$previous_arrow = '<i><svg width="54" height="16" viewBox="0 0 54 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<title>Scroll Arrow</title>
<g id="Canvas" transform="translate(-7418 13076)">
<rect x="7418" y="-13076" width="54" height="16"></rect>
<clipPath id="clip-0" clip-rule="evenodd">
<path d="M 7371 -17795L 8811 -17795L 8811 -10227L 7371 -10227L 7371 -17795Z"></path>
</clipPath>
<g id="News Index" clip-path="url(#clip-0)">
<path d="M 7371 -17795L 8811 -17795L 8811 -10227L 7371 -10227L 7371 -17795Z" fill="#FFFFFF"></path>
<use xlink:href="#path0_fillleft" transform="matrix(-1 1.22465e-16 -1.22465e-16 -1 7472 -13060)"></use>
</g>
</g><defs>
<path id="path0_fillleft" d="M 46 0L 46 6L 0 6L 0 10L 46 10L 46 16L 54 8L 46 0Z"></path>
</defs>
</svg>
</i>';

$next_arrow = '<i><svg width="54" height="16" viewBox="0 0 54 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<title>Scroll Arrow</title>
<g id="Canvas" transform="translate(-8715 13076)">
<rect x="8715" y="-13076" width="54" height="16"></rect>
<clipPath id="clip-0" clip-rule="evenodd">
<path d="M 7371 -17795L 8811 -17795L 8811 -10227L 7371 -10227L 7371 -17795Z" fill="#FFFFFF"></path>
</clipPath>
<g id="News Index" clip-path="url(#clip-0)">
<path d="M 7371 -17795L 8811 -17795L 8811 -10227L 7371 -10227L 7371 -17795Z" fill="#FFFFFF"></path>
<use xlink:href="#path0_fillright" transform="translate(8715 -13076)"></use>
</g>
</g><defs>
<path id="path0_fillright" d="M 46 0L 46 6L 0 6L 0 10L 46 10L 46 16L 54 8L 46 0Z"></path>
</defs>
</svg></i>';
?>

<div class="mod-margin-bottom">
	<div class="pagination-container">
		<div class="pagination-inner p bold mw-1360">
			<div class="nav-newer uppercase">
				<?php previous_posts_link( $previous_arrow . $newer ); ?>
			</div>

			<div class="page-number-list">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'prev_next' => false,
							'type'      => 'list',
						)
					)
				);
				?>
			</div>

			<div class="nav-older uppercase">
				<?php next_posts_link( $older . $next_arrow ); ?>
			</div>
		</div>
	</div>
</div>
