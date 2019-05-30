<?php
/**
 * Profile card
 *
 * @package shiro
 */

$template_data = wmf_get_template_data();

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
	<div class="img-container" style="background-image:url(<?php echo wp_get_attachment_image_url( $img_id, 'image_4x5_large' ); ?>)">
	</div>
	<?php endif; ?>

	<div class="card-content top-into-img">
		<?php if ( ! empty( $title ) ) : ?>
		<h5 class="person-name">
			<?php echo esc_html( $title ); ?>
		</h5>
		<?php endif; ?>

		<?php if ( ! empty( $role ) || ! empty( $team ) ) : ?>
			<span class="person-title p color-gray">
				<?php
					$count = empty( $role ) || empty( $team ) ? 1 : 2;
					printf(
						// Translators: the placeholders are for the $role and $team.
						// @codingStandardsIgnoreStart.
						_n(
							'%1$s',
							'%1$s, %2$s',
							$count,
							'shiro'
						),
						// @codingStandardsIgnoreEnd.
						empty( $role ) ? esc_html( $team ) : esc_html( $role ),
						esc_html( $team )
					);
				?>
			</span>
		<?php endif; ?>
	</div>

</a>
