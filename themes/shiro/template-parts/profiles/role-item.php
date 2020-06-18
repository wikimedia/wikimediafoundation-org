<?php
/**
 * Output for individual role item on taxonomy page
 *
 * @package shiro
 */

$post_data = wmf_get_template_data();

if ( empty( $post_data['id'] ) ) {
	return;
}

$post_id = $post_data['id'];
$post    = get_post( $post_id );

if ( ! $post ) {
	return;
}

?>

<a class="staff-list-item mar-bottom_lg" href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>">
	<div class="staff-photo" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'profile_thumb' ) ); ?>')"></div>
	<h4>
		<?php echo esc_html( get_the_title( $post_id ) ); ?>
	</h4>

	<span><?php echo esc_html( get_post_meta( $post_id, 'profile_role', true ) ); ?></span>
</a>
