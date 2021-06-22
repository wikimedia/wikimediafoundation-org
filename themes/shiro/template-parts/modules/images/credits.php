<?php
/**
 * Handles simple text CTA module.
 *
 * @package shiro
 */

$image_ids = $args['image_ids'];

if ( empty( $image_ids ) ) {
	return;
}

$header = get_theme_mod( 'wmf_image_credit_header', __( 'Photo credits', 'shiro-admin' ) );

?>

<section class="photo-attribution">
	<?php if ( ! empty( $header ) ) : ?>
		<h2 class="photo-attribution__heading"><?php echo esc_html( $header ); ?></h2>
	<?php endif; ?>

	<div class="photo-attribution__inner">
		<?php
			foreach ( $image_ids as $image_id ) {
				get_template_part( 'template-parts/modules/images/credit', null, [ 'image_id' => $image_id ] );
			}
		?>
	</div>
</section>
