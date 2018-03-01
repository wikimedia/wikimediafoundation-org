<?php
/**
 * Set up related pages module
 *
 * @package wmfoundation
 */

$template_data = wmf_get_template_data();

if ( empty( $template_data ) || empty( $template_data['links'] ) ) {
	return;
}

$headline = ! empty( $template_data['title'] ) ? $template_data['title'] : '';


?>

<div class="w-100p mod-margin-bottom">
	<div class="mw-1360 std-mod">
		<?php if ( ! empty( $headline ) ) : ?>
			<h2 class="h2 white"><?php echo esc_html( $headline ); ?></h2>
		<?php endif; ?>

		<div class="related-pages flex flex-medium">
			<?php foreach ( $template_data['links'] as $page ) :
				wmf_get_template_part( 'template-parts/modules/related/item', array(
					'title' => get_the_title( $page ),
					'link'  => get_the_permalink( $page ),
					'img_id' => get_post_thumbnail_id( $page ),
					'id'    => $page,
				));
			endforeach; ?>
		</div>
	</div>
</div>