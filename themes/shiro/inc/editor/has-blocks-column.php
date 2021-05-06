<?php
/**
 * Contains all logic for showing which posts & pages have blocks.
 */

namespace WMF\Editor\HasBlockColumn;

/**
 * Bootstrap all hooks related to the has-block column.
 */
function bootstrap() {
	add_filter( 'manage_page_posts_columns', __NAMESPACE__ . '\\add_column' );
	add_action( 'manage_page_posts_custom_column', __NAMESPACE__ . '\\render_column_content', 10, 2 );
	add_action( 'restrict_manage_posts', __NAMESPACE__ . '\\add_has_blocks_filter' );
	add_filter( 'parse_query', __NAMESPACE__ . '\\filter_on_has_blocks' );
}

/**
 * Add has blocks column to the possible columns.
 */
function add_column( $columns ) {
	$columns['has_blocks'] = __( 'Has blocks', 'shiro' );

	return $columns;
}

/**
 * Render content of the has_blocks column.
 *
 * @param string $column Name of the column.
 * @param mixed $post Post of the current row.
 */
function render_column_content( $column, $post ) {
	if ( $column === 'has_blocks' ) {
		$has_blocks = has_blocks( $post );

		$output = $has_blocks ? __( 'Yes', 'shiro' ) : 'No';

		echo esc_html( $output );
	}
}

function add_has_blocks_filter( $post_type ) {
	if ( $post_type !== 'page' ) {
		return;
	}

	$current_filter = $_GET['shiro_has_blocks_filter'] ?? '';

	?>
		<label for="shiro_has_blocks_filter">
			<span class="screen-reader-text">
				<?php esc_html_e( 'Has blocks filter', 'shiro' ); ?>
			</span>
			<select id="shiro_has_blocks_filter" name="shiro_has_blocks_filter">
				<option value=""<?php selected( '', $current_filter ); ?>>
					<?php esc_html_e( 'All', 'shiro' ); ?>
				</option>
				<option value="has_blocks"<?php selected( 'has_blocks', $current_filter ); ?>>
					<?php esc_html_e( 'Has blocks', 'shiro' ); ?>
				</option>
				<option value="has_no_blocks"<?php selected( 'has_no_blocks', $current_filter ); ?>>
					<?php esc_html_e( 'Doesn\'t have blocks', 'shiro' ); ?>
				</option>
			</select>
		</label>
	<?php
}

/**
 * Filter posts table query to filter based on the has_blocks filter.
 *
 * @param \WP_Query $query The current query.
 * @return \WP_Query The potentially altered query.
 */
function filter_on_has_blocks( $query ) {
	global $wpdb;

	$current_filter = $_GET['shiro_has_blocks_filter'] ?? '';

	if ( $current_filter === '' ) {
		return $query;
	}

	$should_have_blocks = $current_filter === 'has_blocks';
	$has_blocks_search  = '<!-- wp:';

	if ( $should_have_blocks ) {
		$result = $wpdb->get_results(
			$wpdb->prepare(<<<QUERY
				SELECT `ID`
				FROM `wp_posts`
				WHERE `post_content` LIKE '%%%s%%'
				  AND `post_type` = 'page'
				LIMIT 250
QUERY,
				$has_blocks_search
			)
		);

		$query->query_vars['post__in'] = wp_list_pluck( $result, 'ID' );
	} else {
		$result = $wpdb->get_results(
			$wpdb->prepare( <<<QUERY
				SELECT `ID`
				FROM `wp_posts`
				WHERE `post_content` NOT LIKE '%%%s%%'
				  AND `post_type` = 'page'
				LIMIT 250
QUERY,
				$has_blocks_search
			)
		);

		$query->query_vars['post__in'] = wp_list_pluck( $result, 'ID' );
	}

	return $query;
}
