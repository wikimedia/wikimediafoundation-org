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

?>

<a class="card card-vertical card-person rounded shadow hover-img-zoom w-32p" href="<?php echo esc_url( $link ); ?>">
	<?php if ( ! empty( $img_id ) ) : ?>
	<div class="img-container" style="background-image:url(<?php echo esc_url(wp_get_attachment_image_url( $img_id, 'image_4x5_large' )); ?>)">
	</div>
	<?php endif; ?>

	<div class="card-content top-into-img">
		<?php if ( ! empty( $title ) ) : ?>
		<h5 class="person-name">
			<?php echo esc_html( $title ); ?>
		</h5>
		<?php endif; ?>

		<?php if ( ! empty( $role ) ) : ?>
			<span class="person-title p color-gray">
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
