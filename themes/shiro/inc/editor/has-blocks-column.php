<?php
/**
 * Contains all logic for showing which posts & pages have blocks.
 */

namespace WMF\Editor\HasBlockColumn;

use WP_Query;

const HAS_BLOCKS_NONCE_ACTION = 'shiro_has_blocks_filter_action';
const HAS_BLOCKS_NONCE_FIELD = 'shiro_has_blocks_filter_nonce';

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

		$output = $has_blocks ? __( 'Yes', 'shiro' ) : __( 'No', 'shiro' );

		echo esc_html( $output );
	}
}

/**
 * Add HTML select input for filtering on has_blocks. Has no visible filter
 * because it needs to be displayed compact.
 *
 * @param string $post_type The post type we we are rendering for.
 */
function add_has_blocks_filter( $post_type ) {
	if ( $post_type !== 'page' ) {
		return;
	}

	$current_filter = '';
	if ( isset($_GET['shiro_has_blocks_filter']) && check_admin_referer( HAS_BLOCKS_NONCE_ACTION, HAS_BLOCKS_NONCE_FIELD ) ) {
		$current_filter = sanitize_key ( $_GET['shiro_has_blocks_filter'] );
	}

	?>
		<label for="shiro_has_blocks_filter">
			<span class="screen-reader-text">
				<?php esc_html_e( 'Filter by whether the page has blocks', 'shiro' ); ?>
			</span>
			<?php wp_nonce_field( HAS_BLOCKS_NONCE_ACTION, HAS_BLOCKS_NONCE_FIELD ) ?>
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
 * Determine the amount of posts per page.
 *
 * Copied from WordPress core
 *
 * @see https://github.com/WordPress/wordpress-develop/blob/5.7.1/src/wp-admin/includes/post.php#L1160-L1189
 */
function posts_per_page( $post_type ) {
	$per_page       = "edit_{$post_type}_per_page";
	$posts_per_page = (int) get_user_option( $per_page );
	if ( empty( $posts_per_page ) || $posts_per_page < 1 ) {
		$posts_per_page = 20;
	}

	/**
	 * Documented in wp-admin/includes/post.php
	 */
	$posts_per_page = apply_filters( "edit_{$post_type}_per_page", $posts_per_page );

	/**
	 * Documented in wp-admin/includes/post.php
	 */
	$posts_per_page = apply_filters( 'edit_posts_per_page', $posts_per_page, $post_type );

	return $posts_per_page;
}

/**
 * Filter posts table query to filter based on the has_blocks filter.
 *
 * @param WP_Query $query The current query.
 * @return WP_Query The potentially altered query.
 */
function filter_on_has_blocks( $query ) {
	$current_filter = '';
	if ( isset($_GET['shiro_has_blocks_filter']) && check_admin_referer( HAS_BLOCKS_NONCE_ACTION, HAS_BLOCKS_NONCE_FIELD ) ) {
		$current_filter = sanitize_key( $_GET['shiro_has_blocks_filter'] );
	}

	if ( $current_filter === '' ) {
		return $query;
	}

	$query->query_vars['has_blocks'] = $current_filter === 'has_blocks' ? 'yes' : 'no';

	/*
	 * WordPress has special handling for hierarchical post types. It tries to query
	 * all posts to make sure it can display them hierarchically. However, when
	 * filtering with a LIKE, we much rather have a proper LIMIT on the query.
	 *
	 * So reset those query vars to their proper values.
	 *
	 * @see https://github.com/WordPress/wordpress-develop/blob/5.7.1/src/wp-admin/includes/post.php#L1194-L1200
	 */
	$post_type = $query->query_vars['post_type'];
	if (
		! empty( $post_type ) &&
		is_post_type_hierarchical( $post_type ) &&
		$query->query_vars['orderby'] === 'menu_order title' &&
		$query->query_vars['posts_per_page'] === -1
	) {
		$posts_per_page                              = posts_per_page( $post_type );
		$query->query_vars['posts_per_page']         = $posts_per_page;
		$query->query_vars['posts_per_archive_page'] = $posts_per_page;
		$query->query_vars['fields']                 = 'all';

		// Set this to force WordPress to use non-hierarchical display
		$query->query['orderby'] = 'title';
	}

	return $query;
}

/**
 * Adjust SQL query to filter by presence of blocks.
 *
 * @param string   $where The current WHERE SQL clause.
 * @param WP_Query $query The current WP_Query.
 *
 * @return string The altered WHERE SQL clause.
 */
function where_has_blocks( string $where, WP_Query $query ) {
	if ( $query->get('has_blocks', false ) ) {
		global $wpdb;
		if ( $query->get('has_blocks', 'no' ) === 'yes' ) {
			$where .= $wpdb->prepare( "AND `post_content` LIKE '%%%s%%%'",
				'<!-- wp:'
			);
		} else {
			$where .= $wpdb->prepare( "AND `post_content` NOT LIKE '%%%s%%%'",
				'<!-- wp:'
			);
		}
	}

	return $where;
}

/**
 * Add has_blocks query vary, so `where_has_blocks()` can look for it.
 *
 * @param array $vars
 *
 * @return array
 */
function add_has_blocks_query_var( $vars ) {
	$vars[] = 'has_blocks';
	return $vars;
}
