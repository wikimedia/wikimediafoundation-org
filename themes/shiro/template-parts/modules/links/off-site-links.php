<?php
/**
 * Handles off site links module.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['links'] ) || ! is_array( $template_args['links'] ) ) {
	return;
}

$pre_heading = get_theme_mod( 'wmf_off_site_links_pre_heading', __( 'ELSEWHERE IN WIKIMEDIA', 'shiro' ) );
$heading     = empty( $template_args['heading'] ) ? '' : $template_args['heading'];
$split       = empty( $template_args['split'] ) ? false : $template_args['split'];

$rand_translation_title = wmf_get_random_translation( 'wmf_off_site_links_pre_heading' );

$width_class = $split ? 'mw-1360' : 'mw-900';
$wrap_class  = $split ? 'fifty-fifty' : '';
$title_class = $split ? 'small' : '';

?>

<div class="elsewhere-wikimedia white-bg mod-margin-bottom <?php echo esc_attr( $width_class ); ?>">
	<div class="mw-980">
		<?php if ( ! empty( $pre_heading ) ) : ?>
		<h3 class="h3 uppercase color-gray <?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $pre_heading ); ?> — <span lang="<?php echo esc_attr( $rand_translation_title['lang'] ); ?>"><?php echo esc_html( $rand_translation_title['content'] ); ?></span></h3>
		<?php endif; ?>
		<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<div class="flex flex-medium flex-wrap <?php echo esc_attr( $wrap_class ); ?>">
			<?php
			foreach ( $template_args['links'] as $link ) {
				$link['split'] = $split;
				get_template_part( 'template-parts/modules/links/off-site-link', null, $link );
			}
			?>
		</div>
	</div>
</div>
