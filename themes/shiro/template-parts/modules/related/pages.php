<?php
/**
 * Set up related pages module
 *
 * @package shiro
 */

$template_data = $args;

if ( empty( $template_data ) || empty( $template_data['links'] ) ) {
	return;
}

$headline         = ! empty( $template_data['title'] ) ? $template_data['title'] : '';
$preheading       = ! empty( $template_data['preheading'] ) ? $template_data['preheading'] : '';
$translated_title = ! empty( $template_data['rand_translation_title'] ) ? $template_data['rand_translation_title'] : '';
$links            = count( $template_data['links'] ) > 2 ? array_rand( array_flip( $template_data['links'] ), 3 ) : $template_data['links'];
$rand_translation_title = wmf_get_random_translation( 'wmf_related_pages_pre_heading' );

?>

<div class="w-100p mod-margin-bottom">
	<div class="mw-1360 std-mod">
		<?php if ( ! empty( $preheading ) ) : ?>
			<p class="double-heading__secondary is-style-h5">
				<?php echo esc_html( $preheading ); ?>
                <?php if ( ! empty( $rand_translation_title['content'] ) ) : ?>
                    — <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
                <?php endif; ?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $headline ) ) : ?>
			<h2 class="double-heading__primary is-style-h3">
				<?php echo esc_html( $headline ); ?>
			</h2>
		<?php endif; ?>

		<div class="related-pages flex flex-medium">
			<?php
			foreach ( $links as $page ) :
				get_template_part(
					'template-parts/modules/related/item',
					null,
					array(
						'title' => get_the_title( $page ),
						'link'  => get_the_permalink( $page ),
						'image' => get_post_thumbnail_id( $page ),
						'id'    => $page,
					)
				);
			endforeach;
			?>
		</div>
	</div>
</div>
