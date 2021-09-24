<?php
/**
 * Handles projects wrapper and loop.
 *
 * @package shiro
 */

$template_args = $args;

if (
	( empty( $template_args['projects'] ) || ! is_array( $template_args['projects'] ) ) ||
	( empty( $template_args['heading'] ) && empty( $template_args['content'] ) && empty( $template_args['link_uri'] ) )
) {
	return;
}

// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$title                  = get_theme_mod( 'wmf_projects_pre_heading', __( 'Projects', 'shiro-admin' ) );
$rand_translation_title = wmf_get_random_translation( 'wmf_projects_pre_heading' );

$project_class = '_map';
// phpcs:enable

?>

<div class="w-100p mod-margin-bottom home-project-list-container">
	<div class="mw-980 std-mod mod-margin-bottom">
		<?php if ( ! empty( $title ) ) : ?>
			<p class="double-heading__secondary is-style-h5">
				<?php echo esc_html( $title ); ?> — <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span>
			</p>
		<?php endif; ?>
		<div class="flex flex-medium flex-space-between home-project-list">
			<div class="w-32p home-project-list-item  home-project-list-item_blue">
				<?php if ( ! empty( $template_args['heading'] ) ) : ?>
				<h2 class="double-heading__primary is-style-h3">
					<?php echo esc_html( $template_args['heading'] ); ?>
				</h2>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<p class="mar-bottom_lg">
					<?php
					echo wp_kses(
						$template_args['content'], array(
							'em'     => array(),
							'span'   => array( 'class', 'id' ),
							'del'    => array(),
							'strong' => array(),
						)
					);
					?>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) ) : ?>
				<a class="arrow-link" href="<?php echo esc_url( $template_args['link_uri'] ); ?>"><?php echo esc_html( $template_args['link_text'] ); ?></a>
				<?php endif; ?>
			</div>
			<?php
			foreach ( $template_args['projects'] as $project ) {
				$project['class'] = $project_class;
				get_template_part( 'template-parts/modules/projects/project', null, $project );
				$project_class = '_teal';
			}
			?>
		</div>
	</div>
</div>
