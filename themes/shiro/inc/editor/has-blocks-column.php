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
	add_filter( 'query_vars', __NAMESPACE__ . '\\add_has_blocks_query_var' );
	add_filter( 'posts_where', __NAMESPACE__ . '\\where_has_blocks', 10, 2 );
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

	$query->query_vars['has_blocks'] = $current_filter === 'has_blocks' ? 'yes' : 'no';

	return $query;
}

/**
 * Adjust SQL query to filter by presence of blocks.
 *
 * @param string    $where
 * @param \WP_Query $query
 *
 * @return string
 */
function where_has_blocks( string $where, \WP_Query $query ) {
	if ( $query->get('has_blocks', false ) ) {
		$comparison = $query->get('has_blocks', 'no' ) === 'yes' ? 'LIKE' : 'NOT LIKE';

		global $wpdb;
		$where .= $wpdb->prepare( <<<QUERY
			AND `post_content` $comparison '%%%s%%%'
QUERY,
			'<!-- wp:'
		);
	}

	return $where;
}

/**
 * Add has_blocks query vary, so `where_has_blocks()` can look for it.
 *
 * @param $vars
 *
 * @return mixed
 */
function add_has_blocks_query_var( $vars ) {
	$vars[] = 'has_blocks';
	return $vars;
}
