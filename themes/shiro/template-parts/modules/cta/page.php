<?php
/**
 * Handles page specific CTA.
 *
 * @package shiro
 */

$template_args = $args;

$page_id = get_queried_object_id();

if ( empty( $template_args['image'] ) && empty( $template_args['heading'] ) && empty( $template_args['content'] ) && empty( $template_args['link_uri'] ) ) {
	return;
}

$image = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'large' ) : $image;

$bg_class = empty( $template_args['background_color'] ) || 'blue' === $template_args['background_color'] ? 'bg-img--blue btn-pink' : 'bg-img--turquoise btn-blue';
$class    = empty( $template_args['class'] ) ? $bg_class . ' cta-primary' : $template_args['class'];
?>

<div class="w-100p cta mod-margin-bottom_sm <?php echo esc_attr( $class ); ?>">
	<div class="mw-980">
		<div class="card flex flex-medium flex-wrap fifty-fifty">
			<?php if ( ! empty( $image ) ) : ?>
				<div class="card-content w-50p sm-img-container">
					<div class="sm-img rounded" style="background-image: url(<?php echo esc_url( $image ); ?>)"></div>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['heading'] ) || ! empty( $template_args['content'] ) || ! empty( $template_args['link_uri'] ) ) : ?>
			<div class="card-content w-50p">

				<?php if ( ! empty( $template_args['heading'] ) ) : ?>
				<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<div class="mar-bottom mar-top">
					<?php echo wp_kses_post( str_replace( '<p>', '<p class="cta-description">', wpautop( $template_args['content'] ) ) ); ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) && empty( $template_args['support_cta'] ) ) : ?>
				<a class="btn " href="<?php echo esc_url( $template_args['link_uri'] ); ?>"><span class="cta-btn-text"><?php echo esc_html( $template_args['link_text'] ); ?></span></a>

				<?php elseif ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) ) : ?>
				<a class="btn " href="<?php echo esc_url( $template_args['link_uri'] ); ?>&utm_source=<?php echo esc_attr( $page_id ); ?>"><span class="cta-btn-text"><?php echo esc_html( $template_args['link_text'] ); ?></span></a>
				<?php endif; ?>

			</div>
			<?php endif; ?>
		</div>

	</div>
</div>
