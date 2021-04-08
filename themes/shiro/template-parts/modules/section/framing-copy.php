<?php
/**
 * The Framing Copy Module.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['pre_heading'] ) && empty( $template_args['heading'] ) && empty( $template_args['modules'] ) ) {
	return;
}

$rand_translation_title = empty( $template_args['rand_translation_title'] ) ? '' : $template_args['rand_translation_title'];

$has_title = ! empty( $template_args['pre_heading'] ) && ! empty( $template_args['heading'] );

$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );
$ungrid_color = isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? 'pink' : '';

$has_modules = isset($template_args['modules']) && count($template_args['modules']) > 0;
$has_many_modules = count($template_args['modules']) > 2;
$has_image = get_the_post_thumbnail_url();
?>

<?php if ( $has_many_modules && $has_image && !is_front_page() ) { ?>
	<div class="framing-copy-ungrid mw-980">
		<div class="ungrid-line <?php echo esc_attr($ungrid_color); ?>"></div>
		<?php $no_of_modules = count($template_args['modules']); ?>

		<div class="ungrid-top-3">
			<?php for ($i = 0; $i < 3; $i++) { ?>
				<div class="ungrid-top-box ungrid-top-box-<?php echo esc_attr($i); ?>">
					<div class="code <?php echo esc_attr($ungrid_color); ?>">0<?php echo esc_html($i+1); ?></div>
					<?php get_template_part( 'template-parts/modules/mu/ungrid', null, $template_args['modules'][$i] ); ?>
				</div>
			<?php } ?>
		</div>

		<?php if ($no_of_modules === 4) { ?>
			<div class="ungrid-bottom-1">
				<div class="ungrid-bottom-box ungrid-bottom-box-4">
					<div class="code <?php echo esc_attr($ungrid_color); ?>">04</div>
					<?php get_template_part( 'template-parts/modules/mu/ungrid', null, $template_args['modules'][3] ); ?>
				</div>
			</div>
		<?php } ?>

		<?php if ($no_of_modules === 5) { ?>
			<div class="ungrid-bottom-2">
				<?php for ($i = 3; $i < 5; $i++) { ?>
					<div class="ungrid-bottom-box ungrid-bottom-box-<?php echo esc_attr( $i ); ?>">
						<div class="code <?php echo esc_attr($ungrid_color); ?>">0<?php echo esc_html($i+1); ?></div>
						<?php get_template_part( 'template-parts/modules/mu/ungrid', null, $template_args['modules'][$i] ); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>

		<?php if ($no_of_modules === 6) { ?>
			<div class="ungrid-bottom-3">
				<?php for ($i = 3; $i < 6; $i++) { ?>
					<div class="ungrid-bottom-box ungrid-bottom-box-<?php echo esc_attr( $i ); ?>">
						<div class="code <?php echo esc_attr($ungrid_color); ?>">0<?php echo esc_html($i+1); ?></div>
						<?php get_template_part( 'template-parts/modules/mu/ungrid', null, $template_args['modules'][$i] ); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
<?php } ?>

<?php if ( $has_modules && is_front_page()) { ?>
	<div class="flex flex-medium flex-wrap mw-980 mod-margin-bottom flex-space-between">
		<?php
			foreach ( $template_args['modules'] as $key=>$module ) {
				$module["index"] = $key;
				get_template_part( 'template-parts/modules/mu/text-home', null, $module );
			}
		?>
	</div>
<?php } elseif ( !$has_image) { ?>
	<div class="flex flex-medium flex-wrap mw-980 mod-margin-bottom fifty-fifty">
		<?php
			foreach ( $template_args['modules'] as $key=>$module ) {
				$module["index"] = $key;
				get_template_part( 'template-parts/modules/mu/text', null, $module );
			}
		?>
	</div>
<?php } ?>

<?php if ( $has_title ) : ?>
<?php endif; ?>
