<?php
/**
 * Common Header partial
 *
 * @package wmfoundation
 */

$page_header_data = wmf_get_template_data();

$eyebrow_link  = ! empty( $page_header_data['eyebrow_link'] ) ? $page_header_data['eyebrow_link'] : '';
$eyebrow_title = ! empty( $page_header_data['eyebrow_title'] ) ? $page_header_data['eyebrow_title'] : '';
$eyebrow_class = ! empty( $page_header_data['eyebrow_class'] ) ? $page_header_data['eyebrow_class'] : 'h4 uppercase';
$mar_bottom    = ! empty( $page_header_data['mar_bottom'] ) ? $page_header_data['mar_bottom'] : '';

?>

<div class="header-content">
	<?php if ( ! empty( $eyebrow_title ) ) : ?>
	<h2 class="<?php echo esc_attr( $eyebrow_class ); ?> eyebrow">
		<?php if ( ! empty( $eyebrow_link ) ) : ?>
		<a href="<?php echo esc_url( $eyebrow_link ); ?>">
		<?php endif; ?>
			<?php echo esc_html( $eyebrow_title ); ?>
		<?php if ( ! empty( $eyebrow_link ) ) : ?>
		</a>
		<?php endif; ?>
	</h2>
	<?php endif; ?>
	<?php if ( ! empty( $mar_bottom ) ) : ?>
	<h1 class="mar-bottom"><?php echo esc_html( $mar_bottom ); ?></h1>
	<?php endif; ?>
</div>
