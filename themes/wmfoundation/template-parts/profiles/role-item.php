<?php
/**
 * Output for individual role item on taxonomy page
 *
 * @package wmfoundation
 */

$post_data = wmf_get_template_data();

if ( empty( $post_data['id'] ) ) {
	return;
}

$post_id = $post_data['id'];

?>

<a class="staff-list-item mar-bottom_lg" href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>">
	<?php echo get_the_post_thumbnail( $post_id, 'profile_thumb' ); ?>
	<h4 class="mar-bottom_sm">
		<?php echo esc_html( get_the_title( $post_id ) ); ?>
	</h4>

	<span class="color-gray"><?php echo esc_html( get_post_meta( $post_id, 'profile_role', true ) ); ?></span>
</a>
