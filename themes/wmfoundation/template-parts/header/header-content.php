<?php
/**
 * Common Header partial
 *
 * @package wmfoundation
 */

$page_header_data = wmf_get_template_data();

$h4_link  = ! empty( $page_header_data['h4_link'] ) ? $page_header_data['h4_link'] : '';
$h4_title = ! empty( $page_header_data['h4_title'] ) ? $page_header_data['h4_title'] : '';
$h2_link  = ! empty( $page_header_data['h2_link'] ) ? $page_header_data['h2_link'] : '';
$h2_title = ! empty( $page_header_data['h2_title'] ) ? $page_header_data['h2_title'] : '';
$title    = ! empty( $page_header_data['h1_title'] ) ? $page_header_data['h1_title'] : '';

?>

<div class="header-content">

	<?php if ( ! empty( $h4_title ) ) : ?>
	<h2 class="h4 uppercase eyebrow">
		<?php if ( ! empty( $h4_link ) ) : ?>
		<a href="<?php echo esc_url( $h4_link ); ?>">
		<i class="material-icons" style="vertical-align: sub;">arrow_back</i>
		<?php endif; ?>
			<?php echo esc_html( $h4_title ); ?>
		<?php if ( ! empty( $h4_link ) ) : ?>
		</a>
		<?php endif; ?>
	</h2>
	<?php endif; ?>

	<?php if ( ! empty( $h2_title ) ) : ?>
		<h2 class="h2 uppercase eyebrow">
			<?php if ( ! empty( $h2_link ) ) : ?>
			<a href="<?php echo esc_url( $h2_link ); ?>">
				<?php endif; ?>
				<?php echo esc_html( $h2_title ); ?>
				<?php if ( ! empty( $h2_link ) ) : ?>
			</a>
		<?php endif; ?>
		</h2>
	<?php endif; ?>

	<?php if ( ! empty( $mar_bottom ) ) : ?>
	<h1 class="mar-bottom"><?php echo esc_html( $mar_bottom ); ?></h1>
	<?php endif; ?>
</div>
