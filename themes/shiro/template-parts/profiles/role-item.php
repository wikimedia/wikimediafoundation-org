<?php
/**
 * Output for individual role item on taxonomy page
 *
 * @package shiro
 */

// $args is data passed into the template, also we provide fallbacks in case they don't exist.
// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$profile_data = $args ?? [];

if ( empty( $profile_data['id'] ) ) {
	return;
}

$is_list  = $profile_data['list'] ?? true;
$profile_id  = $profile_data['id'];
$profile     = get_post( $profile_id );

if ( ! is_a( $profile, \WP_Post::class ) ) {
	return;
}

$profile_img = get_the_post_thumbnail(
	$profile_id,
	$is_list ? 'profile_thumb' : 'image_4x3_large',
	[ 'class' => 'role__staff-list__item__photo' ]
);

$profile_class = 'role__staff-list__item';
if ( $profile_data['role'] ) {
	$profile_class .= ' role__staff-list__item--' . $profile_data['role'];
}
?>

<?php if ( $is_list ) : ?>
	<li class="<?php echo esc_attr( $profile_class ); ?>">
<?php else : ?>
	<div class="<?php echo esc_attr( $profile_class ); ?>">
<?php endif;

	if ( $profile_img ) :
		echo wp_kses_post( $profile_img );
	else :
	?>
		<span class="role__staff-list__item__photo"></span>
	<?php endif; ?>

	<h4>
		<a href="<?php the_permalink( $profile_id ); ?>">
			<?php echo esc_html( $profile->post_title ); ?>
		</a>
	</h4>

	<p><?php echo esc_html( get_post_meta( $profile_id, 'profile_role', true ) ); ?></p>

<?php if ( $is_list ) : ?>
	</li>
<?php else : ?>
	</div>
<?php endif; ?>
