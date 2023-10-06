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
	'h1_title' => sprintf( $wmf_results_copy, get_search_query() ),
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
				esc_html__( 'Showing %1$s-%2$s of %3$s results', 'shiro' ),
				esc_html( $first_result ),
				esc_html( $last_result ),
				esc_html( $total_results )
			);
		}
		?>
	</div>

	<div class="search-results__tabs mw-980">
		<?php
			$sorting_options = [
				'relevance' => [
					'query' => 'orderby=relevance',
					'label' => __( 'Relevance', 'shiro' ),
				],
				'date-desc' => [
					'query' => 'orderby=date&order=DESC',
					'label' => __( 'Date (newest)', 'shiro' ),
				],
				'date-asc' => [
					'query' => 'orderby=date&order=ASC',
					'label' => __( 'Date (oldest)', 'shiro' ),
				],
			];

			/**
			 * An empty sort_by value means sorting isn't applied for that post_type,
			 * so the sort dropdown will not be displayed.
			 */
			$search_results_tabs = [
				'all' => [
					'label' => __( 'All', 'shiro' ),
					'sort_by' => 'relevance',
				],
				'post' => [
					'label' => __( 'News', 'shiro' ),
					'sort_by' => 'date-desc',
				],
				'page' => [
					'label' => __( 'Pages', 'shiro' ),
					'sort_by' => false, // No sorting option for pages.
				],
			];

			// All is the default option if none is selected, or if the post_type provided isn't in the list.
			$query_option = ( isset( $_GET['post_type'][0] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				? sanitize_text_field( wp_unslash( $_GET['post_type'][0] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				: 'all';
			$option = array_key_exists( $query_option, $search_results_tabs ) ? $query_option : 'all';
			$selected = esc_attr( $option );

			foreach ( $search_results_tabs as $key => $value ) {
				$active = $selected === $key ? 'active' : '';

				$current_url = add_query_arg( 's', get_search_query(), home_url( '/' ) );

				// Add the default sorting option for the current post_type
				if ( isset( $sorting_options[ $value['sort_by'] ] ) ) {
					$sort_query = $sorting_options[ $value['sort_by'] ]['query'];
					$current_url = add_query_arg( $sort_query, '', $current_url );
				}

				// Simplest way to get the all types is not adding post_type param filter.
				if ( $key !== 'all' ) {
					$current_url = add_query_arg( 'post_type[]', $key, $current_url );

					/* translators: post type, i.e., News or Pages */
					$aria_label = sprintf( __( 'Filter search for %s only', 'shiro' ), $value['label'] );
				} else {
					$aria_label = sprintf( __( 'Show all search results', 'shiro' ), $value['label'] );
				}

				printf(
					'<a href="%1$s" class="search-results__tab %2$s" aria-label="%3$s" title="%3$s">%4$s</a>',
					esc_url( $current_url ),
					esc_attr( $active ),
					esc_attr( $aria_label ),
					esc_html( $value['label'] )
				);
			}

			// Default sort option.
			$current_sort = 'relevance';

			// Check the URL for each sorting option's parameters
			foreach ( $sorting_options as $sort_key => $option ) {
				$query_params = [];
				parse_str( $option['query'], $query_params );
				$match = true;
				foreach ( $query_params as $param => $value ) {
					if ( ! isset( $_GET[ $param ] ) || sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) !== $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$match = false;
						break;
					}
				}
				if ( $match ) {
					$current_sort = $sort_key;
					break;
				}
			}

			$current_sort_label = $sorting_options[ $current_sort ]['label'];
			?>

			<?php if ( ! empty( $search_results_tabs[ $selected ]['sort_by'] ) ) : ?>

				<div class="search-results__tabs__sort">

					<button aria-haspopup="true" aria-expanded="false">
						<span>Sort by</span>&nbsp;<span class="selected-sort"><?php echo esc_html( $current_sort_label ); ?></span>
						<span class="dropdown-icon"></span>
					</button>

					<div class="sort-dropdown" role="menu">
						<?php
						foreach ( $sorting_options as $sort_key => $option ) {
							$option_query_params = [];
							parse_str( $option['query'], $option_query_params );
							$custom_sort_url = wmf_set_custom_sort_url( $option_query_params );
							?>
							<a href="<?php echo esc_url( $custom_sort_url ); ?>" class="sort-option" data-sort="<?php echo esc_attr( $sort_key ); ?>" role="menuitem">
								<?php echo esc_html( $option['label'] ); ?>
							</a>
						<?php } ?>
					</div>

				</div>

			<?php endif; ?>
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
