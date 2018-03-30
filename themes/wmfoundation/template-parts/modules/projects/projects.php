<?php
/**
 * Handles projects wrapper and loop.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if (
	( empty( $template_args['projects'] ) || ! is_array( $template_args['projects'] ) ) ||
	( empty( $template_args['pre_heading'] ) && empty( $template_args['heading'] ) && empty( $template_args['content'] ) && empty( $template_args['link_uri'] ) )
) {
	return;
}

$project_class = '_map';

?>

<div class="w-100p mod-margin-bottom home-project-list-container">
	<div class="mw-1360 std-mod mod-margin-bottom">
		<div class="flex flex-medium home-project-list">
			<div class="w-50p home-project-list-item  home-project-list-item_blue">
				<?php if ( ! empty( $template_args['pre_heading'] ) ) : ?>
					<h3 class="h3 color-white"><?php echo esc_html( $template_args['pre_heading'] ); ?> — <span><?php echo esc_html( $template_args['rand_translation_title'] ); ?></span></h3>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['heading'] ) ) : ?>
				<p class="h3 color-white mar-bottom_lg"><?php echo esc_html( $template_args['heading'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<p class="color-white mar-bottom_lg"><?php echo wp_kses_post( strip_tags( $template_args['content'], '<em><span><del><strong>' ) ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) ) : ?>
				<div class="link-list hover-highlight uppercase color-white">
					<a href="<?php echo esc_url( $template_args['link_uri'] ); ?>"><?php echo esc_html( $template_args['link_text'] ); ?></a>
				</div>
				<?php endif; ?>
			</div>
			<?php
			foreach ( $template_args['projects'] as $project ) {
				$project['class'] = $project_class;
				wmf_get_template_part( 'template-parts/modules/projects/project', $project );
				$project_class = '_teal';
			}
			?>
		</div>
	</div>
</div>
