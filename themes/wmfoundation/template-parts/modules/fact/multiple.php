<?php
/**
 * Handles page fact module for multiple facts.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['image'] ) && empty( $template_args['facts'] ) ) {
	return;
}

$image = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'full' ) : $image;

$fact_width = 3 === count( $template_args['facts'] ) ? 'w-32p' : 'w-45p';

?>

<div class="fact-container bg-img--turquoise-dark mod-margin-bottom">

	<div class="bg-img-container">
		<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>);"></div>
	</div>
	<div class="mw-1360 flex flex-medium flex-wrap">
		<?php foreach ( $template_args['facts'] as $fact ) : ?>
		<div class="fact-inner module-mu <?php echo esc_attr( $fact_width ); ?> mar-bottom_lg">
			<div class="fact-text-wrap">
				<?php if ( ! empty( $fact['heading'] ) ) : ?>
				<h2 class="fact-stat-lg bold color-white"><?php echo esc_html( $fact['heading'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $fact['content'] ) ) : ?>
				<h3 class="fact mar-bottom"><?php echo esc_html( $fact['content'] ); ?></h3>
				<?php endif; ?>
			</div>
			<?php

			$template_args = array(
				'message' => sprintf( '%1$s - %2$s', $fact['heading'], $fact['content'] ),
			);
			wmf_get_template_part( 'template-parts/modules/social/share', $template_args, 'horizontal' );
			?>
		</div>
		<?php endforeach; ?>
	</div>

</div>
