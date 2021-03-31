<?php
/**
 * Handles simple text CTA module.
 *
 * @package shiro
 */

$image_ids = $args['image_ids'];

if ( empty( $images ) ) {
	return;
}

$header = get_theme_mod( 'wmf_image_credit_header', __( 'Photo credits', 'shiro' ) );

?>

<div class="photo-credits white-bg mod-margin-bottom">

	<div class="mw-980">
		<?php if ( ! empty( $header ) ) : ?>
		<h3 class="h2"><?php echo esc_html( $header ); ?></h3>
		<?php endif; ?>

		<div class="flex flex-all flex-wrap color-gray">
			<?php
			foreach ( $image_ids as $image_id ) {
				get_template_part( 'template-parts/modules/images/credit', null, [ 'image_id' => $image_id ] );
			}
			?>
		</div>
	</div>

</div>
