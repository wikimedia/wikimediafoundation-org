<?php
/**
 * Template for post list filter controls
 *
 * @package shiro
 */

// Get query vars.
$query_var_search_term = get_search_query();
$query_var_date_from   = isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '';
$query_var_date_to     = isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '';
$query_var_categories  = isset( $_GET['categories'] ) ? array_map( 'sanitize_text_field', $_GET['categories'] ) : [];

// Results count.
$total_results = $wp_query->found_posts;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$posts_per_page = get_query_var( 'posts_per_page' );
$first_result = ( $posts_per_page * $paged ) - $posts_per_page + 1;
$last_result = min( $total_results, $wp_query->post_count * $paged );

// Create a sorted array of categories.
$categories = get_categories();
$categories_array = [];
foreach ( $categories as $category ) {
	$category_display = ( $category->parent == 0 )
		? $category->name
		: get_category_parents( $category->parent, false, ' > ', false ) . $category->name;

	$categories_array[ $category->slug ] = $category_display;
}
asort( $categories_array );

// Applied filters count.
$applied_filter_count = 0;
if ( isset( $_GET['post_list_filters_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['post_list_filters_nonce'] ), 'post_list_filters' ) ) {
	// Search term.
	if ( ! empty( $query_var_search_term ) ) {
		$applied_filter_count++;
	}

	// Date interval.
	if ( ! empty( $query_var_date_from ) || ! empty( $query_var_date_to ) ) {
		$applied_filter_count++;
	}

	// Categories.
	$applied_filter_count += count( $query_var_categories );
}

?>

<section class="post-list-filter mw-980">

	<div class="post-list-filter__head">

			<?php
			if ( $total_results > 0 ) {
				printf(
					esc_html(
						/* translators: 1. first result, 2. last result, 3. total results */
						_n( // phpcs:ignore WordPress.WP.I18n.MismatchedPlaceholders
							'Showing %1$d result', // phpcs:ignore WordPress.WP.I18n.MissingSingularPlaceholder
							'Showing %1$d-%2$d of %3$d results',
							$total_results,
							'shiro'
						)
					),
					esc_html( $first_result ),
					esc_html( $last_result ),
					esc_html( $total_results )
				);
			}
			?>

		<button class="action-button post-list-filter__toggle">
			<span class="post-list-filter__toggle__message--hide"><?php echo esc_html__( 'Hide filters', 'shiro' ); ?></span>
			<span class="post-list-filter__toggle__message--show">
			<?php
			printf( esc_html__( 'Show filters', 'shiro' ) );
			if ( $applied_filter_count > 0 ) {
				echo ' <em>';
				/* translators: 1. how many filters were applied */
				printf( esc_html__( '(%s applied)', 'shiro' ), esc_html( $applied_filter_count ) );
				echo '</em>';
			}
			?>
			</span>
		</button>

	</div>

	<form method="get" class="post-list-filter__form">

		<?php wp_nonce_field( 'post_list_filters', 'post_list_filters_nonce' ); ?>

		<div class="post-list-filter__container mw-980">

			<div class="filter-by-text">
				<h5>
					<?php printf( esc_html__( 'Filter by text', 'shiro' ) ); ?>
					<?php if ( ! empty( $query_var_search_term ) ) : ?>
						&nbsp;<em>(<?php printf( esc_html__( 'applied', 'shiro' ) ); ?>)</em>
					<?php endif; ?>
				</h5>
				<div class="search-text-input-button">
					<input type="text" name="s" value="<?php echo esc_attr( $query_var_search_term ); ?>">
				</div>
			</div>

			<div class="filter-by-date">
				<h5>
					<?php printf( esc_html__( 'Filter by date', 'shiro' ) ); ?>
					<?php if ( ! empty( $query_var_date_from ) || ! empty( $query_var_date_to ) ) : ?>
						&nbsp;<em>(<?php printf( esc_html__( 'applied', 'shiro' ) ); ?>)</em>
					<?php endif; ?>
				</h5>
				<div class="filter-date-inputs-container">
					<input type="date" name="date_from" placeholder="From" value="<?php echo esc_attr( $query_var_date_from ); ?>">
					<input type="date" name="date_to" placeholder="To" value="<?php echo esc_attr( $query_var_date_to ); ?>">
					<button type="button" class="action-button action-button--clear" id="button-reset-date-filters"><?php printf( esc_html__( 'Reset dates', 'shiro' ) ); ?></button>
				</div>
			</div>

			<div class="filter-by-category">
				<h5>
					<?php printf( esc_html__( 'Filter by category', 'shiro' ) ); ?>
					<?php if ( count( $query_var_categories ) > 0 ) : ?>
						&nbsp;<em>(<?php echo count( $query_var_categories ) . ' ' . esc_html__( 'applied', 'shiro' ); ?>)</em>
					<?php endif; ?>
				</h5>

				<ul class='category-container'>
					<?php foreach ( $categories_array as $category_slug => $category_display ) : ?>
					<li>
						<label class='individual-category
								<?php if ( in_array( $category_slug, $query_var_categories ) ) : ?>
									individual-category--applied
								<?php endif; ?>'>
							<input type="checkbox" name="categories[]" value="<?php echo esc_attr( $category_slug ); ?>" <?php checked( in_array( $category_slug, $query_var_categories ) ); ?>>
							<?php echo esc_html( $category_display ); ?>
						</label>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<button class='action-button action-button--clear' id="button-clear-filters" type="reset"><?php esc_html_e( 'Clear all filters', 'shiro' ); ?></button>
			<button class="action-button action-button--right" id="button-apply-filters" type="submit"><?php esc_html_e( 'Apply filters', 'shiro' ); ?></button>

		</div>

	</form>

</div>
