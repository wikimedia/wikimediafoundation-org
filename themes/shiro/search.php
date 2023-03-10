<?php
/**
 *
 * Template for displaying search results
 *
 * @package shiro
 */

get_header();

$wmf_results_copy = get_theme_mod( 'wmf_search_results_copy', /* translators: 1. search query */ __( 'Search results for %s', 'shiro-admin' ) );

$template_args = array(
	/* translators: Query that is currently being searched */
	'h1_title' => sprintf( __( $wmf_results_copy, 'shiro' ), get_search_query() ), // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
);

get_template_part( 'template-parts/header/page-noimage', null, $template_args );

?>

<?php if ( have_posts() ) : ?>
	<div class="search-results__count mw-980">
		<?php
		$total_results = $wp_query->found_posts;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$posts_per_page = get_query_var( 'posts_per_page' );
		$first_result = ( $posts_per_page * $paged ) - $posts_per_page + 1;
		$last_result = min( $total_results, $wp_query->post_count * $paged );
		if ( $total_results === 1 ) {
			printf( esc_html__( 'Showing 1 of 1 result', 'shiro' ) );
		} else {
			printf(
				/* translators: 1. first result, 2. last result, 3. total results */
				esc_html__( 'Showing %1$s - %2$s of %3$s results', 'shiro' ),
				esc_html( $first_result ),
				esc_html( $last_result ),
				esc_html( $total_results )
			);
		}
		?>
	</div>

	<div class="search-results__tabs mw-980">
		<?php
			$options = [
				'all' => 'All',
				'post' => 'News',
				'page' => 'Pages',
			];

			// All is the default option if none is selected, or if the post_type provided isn't in the list.
			if ( isset( $_GET['post_type'][0] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query_option = sanitize_text_field( wp_unslash( $_GET['post_type'][0] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
			$option = array_key_exists( $query_option, $options ) ? $query_option : 'all';
			$selected = esc_attr( $option );

			foreach ( $options as $key => $value ) {
				$active = $selected === $key ? 'active' : '';

				$href = esc_url( home_url( '/' ) ) . '?s=' . get_search_query();
				// Simplest way to get the all types is removing post_type param.
				if ( $key !== 'all' ) {
					$href .= '&post_type[]=' . $key;
					/* translators: post type, i.e., News or Pages */
					$aria_label = sprintf( __( 'Filter search for %s only', 'shiro' ), $value );
				} else {
					$aria_label = sprintf( __( 'Show all search results', 'shiro' ), $value );
				}

				printf(
					'<a href="%1$s" class="search-results__tab %2$s" aria-label="%3$s" title="%3$s">%4$s</a>',
					esc_url( $href ),
					esc_attr( $active ),
					esc_attr( $aria_label ),
					esc_html( $value )
				);
			}
			?>
	</div>
<?php endif; ?>

<div class="mw-980 mod-margin-bottom flex flex-medium news-card-list">

	<div id="search-results" class="card-list-container">

			<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();

					// Render the block for the current post
					echo WMF\Editor\Blocks\BlogPost\render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						[ 'post_id' => $post->ID ]
					);
				}
			} else {
				// If there are no posts, display a "content-none" template
				get_template_part( 'template-parts/content', 'none' );
			}
			?>
		</div>
	</div>

	<div id="pagination">
		<?php
		if ( have_posts() ) :
			get_template_part( 'template-parts/pagination' );
		endif;
		?>
	</div>

<?php
get_footer();
