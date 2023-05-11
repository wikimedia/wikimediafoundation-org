<?php
/**
 * Template for post list filter controls
 *
 * @package shiro
 */

?>

<section class="post-list-filter mw-980">

	<div class="post-list-filter__head">

		<h3>
			<?php
			$total_results = $wp_query->found_posts;
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			$posts_per_page = get_query_var( 'posts_per_page' );
			$first_result = ( $posts_per_page * $paged ) - $posts_per_page + 1;
			$last_result = min( $total_results, $wp_query->post_count * $paged );
			if ( $total_results === 1 ) {
				printf( esc_html__( 'Showing 1 of 1 result', 'shiro' ) );
			} elseif ( $total_results > 0 ) {
				printf(
					/* translators: 1. first result, 2. last result, 3. total results */
					esc_html__( 'Showing %1$s - %2$s of %3$s results', 'shiro' ),
					esc_html( $first_result ),
					esc_html( $last_result ),
					esc_html( $total_results )
				);
			}
			?>
		</h3>

		<button class="post-list-filter__toggle">
			<?php
			$filter_count = 0;

			if ( isset( $_GET['post_list_filters_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['post_list_filters_nonce'] ), 'post_list_filters' ) ) {
				$search_term = get_search_query();
				if ( ! empty( $search_term ) ) {
					$filter_count++;
				}

				if ( ! empty( sanitize_text_field( $_GET['date_from'] ) ) && ! empty( sanitize_text_field( $_GET['date_to'] ) ) ) {
					$filter_count++;
				}

				if ( ! empty( $_GET['categories'] ) ) {
					$filter_count += count( $_GET['categories'] );
				}

				printf( esc_html__( 'Show filters', 'shiro' ) );

				if ( $filter_count > 0 ) {
					echo ' <i>';
					/* translators: 1. how many filters were applied */
					printf( esc_html__( '(%s applied)', 'shiro' ), esc_html( $filter_count ) );
					echo '</i>';
				}
			} else {
				printf( esc_html__( 'Show filters', 'shiro' ) );
			}
			?>
		</button>

	</div>

	<form method="get" class="post-list-filter__form">

		<?php wp_nonce_field( 'post_list_filters', 'post_list_filters_nonce' ); ?>

		<div class="post-list-filter__container mw-980">

			<div class="filter-by-text">
				<h5>
					<?php printf( esc_html__( 'Filter by text', 'shiro' ) ); ?>
					<?php if ( ! empty( get_search_query() ) ) : ?>
						&nbsp;<i>(<?php printf( esc_html__( 'applied', 'shiro' ) ); ?>)</i>
					<?php endif; ?>
				</h5>
				<div class="search-text-input-button">
					<input type="text" name="s" value="<?php echo get_search_query(); ?>">
				</div>
			</div>

			<div class="filter-by-date">
				<h5>
					<?php printf( esc_html__( 'Filter by date', 'shiro' ) ); ?>
					<?php if ( ! empty( $_GET['date_from'] ) && ! empty( $_GET['date_to'] ) ) : ?>
						&nbsp;<i>(<?php printf( esc_html__( 'applied', 'shiro' ) ); ?>)</i>
					<?php endif; ?>
				</h5>
				<div class="filter-date-inputs-container">
					<input type="date" name="date_from" placeholder="From" value="<?php echo isset( $_GET['date_from'] ) ? esc_attr( sanitize_text_field( $_GET['date_from'] ) ) : ''; ?>">
					<input type="date" name="date_to" placeholder="To" value="<?php echo isset( $_GET['date_to'] ) ? esc_attr( sanitize_text_field( $_GET['date_to'] ) ) : ''; ?>">
					<button type="button" class="button-reset-date-filters">Reset</button>
				</div>
			</div>

			<div class="filter-by-category">
				<?php
				$categories = get_categories();
				$current_categories = isset( $_GET['categories'] ) ? array_map( 'sanitize_text_field', $_GET['categories'] ) : [];

				foreach ( $categories as $category ) {
					$category_display = ( $category->parent == 0 )
						? $category->name
						: get_category_parents( $category->parent, false, ' > ', false ) . $category->name;

					$categories_array[ $category->slug ] = $category_display;
				}
				asort( $categories_array );
				?>

				<h5>
					<?php printf( esc_html__( 'Filter by category', 'shiro' ) ); ?>
					<?php if ( count( $current_categories ) > 0 ) : ?>
						&nbsp;<i>(<?php echo count( $current_categories ) . ' ' . esc_html__( 'applied', 'shiro' ); ?>)</i>
					<?php endif; ?>
				</h5>

				<ul>
					<?php foreach ( $categories_array as $category->slug => $category_display ) : ?>
					<li>
						<label class='individual-category'>
							<input type="checkbox" name="categories[]" value="<?php echo esc_attr( $category->slug ); ?>" <?php checked( in_array( $category->slug, $current_categories ) ); ?>>
							<?php echo esc_html( $category_display ); ?>
						</label>
					</li>
					<?php endforeach; ?>
				</ul>

			</div>

			<button class='button-clear-filters' type="reset"><?php printf( esc_html__( 'Clear filters', 'shiro' ) ); ?></button>
			<button type="submit" class="button-apply-filters"><?php printf( esc_html__( 'Apply filters', 'shiro' ) ); ?></button>

		</div>

	</form>

</div>
