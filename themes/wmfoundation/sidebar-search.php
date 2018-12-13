<?php
/**
 * Search results sidebar
 *
 * @package wmfoundation
 */

$post_types         = array();
$allowed_post_types = array( 'post', 'profile', 'page' );

foreach ( $allowed_post_types as $post_type ) {
	$post_type_object                       = get_post_type_object( $post_type );
	$post_types[ $post_type_object->label ] = $post_type;
}
$current_post_types = get_query_var( 'post_type' );
$current_order      = strtolower( get_query_var( 'order' ) );
$current_orderby    = get_query_var( 'orderby' );
$current_orderby    = ! empty( $current_orderby ) ? $current_orderby : 'date';

$wmf_sidebar_type       = get_theme_mod( 'wmf_search_sidebar_type', __( 'Result Type', 'wmfoundation' ) );
$wmf_sidebar_sortby     = get_theme_mod( 'wmf_search_sidebar_sortby', __( 'Sort By', 'wmfoundation' ) );
$wmf_sidebar_sort_des   = get_theme_mod( 'wmf_search_sidebar_sort_des', __( 'Title (descending)', 'wmfoundation' ) );
$wmf_sidebar_sort_asc   = get_theme_mod( 'wmf_search_sidebar_sort_asc', __( 'Title (ascending)', 'wmfoundation' ) );
$wmf_sidebar_sort_new   = get_theme_mod( 'wmf_search_sidebar_sort_new', __( 'Newest', 'wmfoundation' ) );
$wmf_sidebar_sort_old   = get_theme_mod( 'wmf_search_sidebar_sort_old', __( 'Oldest', 'wmfoundation' ) );
$wmf_sidebar_submit     = get_theme_mod( 'wmf_search_sidebar_submit', __( 'Submit', 'wmfoundation' ) );

?>
<div class="module-mu wysiwyg w-32p">
	<div class="mar-bottom_lg">
		<h4 class="uppercase small mar-bottom"><?php echo esc_attr( $wmf_sidebar_type ); ?></h4>
		<form id="searchFilter" role="serach" method="GET" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			foreach ( $post_types as $post_type_name => $post_type_label ) :
				$is_checked = in_array( $post_type_label, (array) $current_post_types, true ) ? 'checked' : '';
				?>
			<div class="checkbox-row">
				<input type="checkbox" name="post_type[]" value="<?php echo esc_attr( $post_type_label ); ?>" id="<?php echo esc_attr( $post_type_label ); ?>" <?php echo esc_attr( $is_checked ); ?> />
				<label for="<?php echo esc_attr( $post_type_label ); ?>">
					<?php echo esc_html( $post_type_name ); ?>
				</label>
			</div>
			<?php endforeach; ?>
		<h4 class="uppercase small  mar-bottom"><?php echo esc_attr( $wmf_sidebar_sortby ); ?></h4>
			<select class="mar-bottom" id="sortSelect" name="orderby[<?php echo esc_attr( $current_orderby ); ?>]">
				<option data-type="title" value="desc"
				<?php
				if ( 'title' === $current_orderby && 'desc' === $current_order ) {
					echo esc_attr( 'selected' ); }
				?>
				><?php echo esc_attr( $wmf_sidebar_sort_des ); ?></option>
				<option data-type="title" value="asc"
				<?php
				if ( 'title' === $current_orderby && 'asc' === $current_order ) {
					echo esc_attr( 'selected' ); }
				?>
				><?php echo esc_attr( $wmf_sidebar_sort_asc ); ?></option>
				<option data-type="date" value="desc"
				<?php
				if ( 'date' === $current_orderby && 'desc' === $current_order ) {
					echo esc_attr( 'selected' ); }
				?>
				><?php echo esc_attr( $wmf_sidebar_sort_new ); ?></option>
				<option data-type="date" value="asc"
				<?php
				if ( 'date' === $current_orderby && 'asc' === $current_order ) {
					echo esc_attr( 'selected' ); }
				?>
				><?php echo esc_attr( $wmf_sidebar_sort_old ); ?></option>
			</select>

			<input type="hidden" id="keyword" name="s" value="<?php the_search_query(); ?>" />
			<input type="submit" id="searchsubmit" value="<?php echo esc_attr( $wmf_sidebar_submit ); ?>" />
		</form>


	</div>
</div>
