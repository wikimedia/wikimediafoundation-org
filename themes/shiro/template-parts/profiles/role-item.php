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

$is_list  = $post_data['list'] ?? true;
$post_id  = $post_data['id'];
$post     = get_post( $post_id );

if ( ! is_a( $post, \WP_Post::class ) ) {
	return;
}

$post_img = get_the_post_thumbnail(
	$post_id,
	$is_list ? 'profile_thumb' : 'image_4x3_large',
	[ 'class' => 'role__staff-list__item__photo' ]
);

$post_class = 'role__staff-list__item';
if ( $post_data['role'] ) {
	$post_class = $post_class . ' role__staff-list__item--' . $post_data['role'];
}
?>

<?php if ( $is_list ) : ?>
	<li class="<?php echo esc_attr( $post_class ); ?>">
<?php else : ?>
	<div class="<?php echo esc_attr( $post_class ); ?>">
<?php endif;

	if ( $post_img ) :
		echo wp_kses_post( $post_img );
	else :
	?>
		<span class="role__staff-list__item__photo"></span>
	<?php endif; ?>

	<h4>
		<a href="<?php the_permalink( $post_id ); ?>">
			<?php echo esc_html( $post->post_title ); ?>
		</a>
	</h4>

	<p><?php echo esc_html( get_post_meta( $post_id, 'profile_role', true ) ); ?></p>

<?php if ( $is_list ) : ?>
	</li>
<?php else : ?>
	</div>
<?php endif; ?>
