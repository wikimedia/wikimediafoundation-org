<?php
/**
 * Output for individual role item on taxonomy page
 *
 * @package shiro
 */

$post_data = $args;

if ( empty( $post_data['id'] ) ) {
	return;
}

$post_id = $post_data['id'];
$post    = get_post( $post_id );

if ( ! $post ) {
	return;
}

?>

<li class="role__staff-list__item">
	<div class="role__staff-list__item__photo" style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'profile_thumb' ) ); ?>')"></div>
	<h4>
		<a href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>">
			<?php echo esc_html( get_the_title( $post_id ) ); ?>
		</a>
	</h4>

	<p><?php echo esc_html( get_post_meta( $post_id, 'profile_role', true ) ); ?></p>
</li>
