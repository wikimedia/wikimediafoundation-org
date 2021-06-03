<?php
/**
 * Profile card
 *
 * @package shiro
 */

$template_data = $args;

$link   = ! empty( $template_data['link'] ) ? $template_data['link'] : '';
$title  = ! empty( $template_data['title'] ) ? $template_data
['title'] : '';
$role   = ! empty( $template_data['role'] ) ? $template_data
['role'] : '';
$team   = ! empty( $template_data['team'] ) ? $template_data
['team'] : '';
$img_id = ! empty( $template_data['img_id'] ) ? $template_data
['img_id'] : '';
$image_el = wp_get_attachment_image($img_id,  'image_4x3_small', null, [
		'class' => 'profile__image',
] );

?>

<a class="profile" href="<?php echo esc_url( $link ); ?>">
	<?php echo $image_el ?>

	<div class="profile__content">
		<?php if ( ! empty( $title ) ) : ?>
		<h5 class="profile__name is-style-h5">
			<?php echo esc_html( $title ); ?>
		</h5>
		<?php endif; ?>

		<?php if ( ! empty( $role ) ) : ?>
			<span class="profile__title">
				<?php
				if ( $role && $team ) {
					esc_html_e(
					/* translators: 1. role 2. team */
					sprintf( '%1$s, %2$s', $role, $team ),
					'shiro'
					);
				} else {
					echo esc_html( $role );
				}
				?>
			</span>
		<?php endif; ?>
	</div>

</a>
