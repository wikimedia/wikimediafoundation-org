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

?>
<div class="module-mu wysiwyg w-32p">
	<div class="mar-bottom_lg">
		<h4 class="uppercase small mar-bottom">Result Type</h4>
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

			<input type="hidden" id="keyword" name="s" value="<?php the_search_query(); ?>" />
			<input type="submit" id="searchsubmit" value="Submit" />
		</form>


	</div>
</div>
