<?php
/**
 * Define shortcodes for section with quotes from the internet.
 * Author: Hang Do Thi Duc
 *
 * @package shiro
 */

/**
 * Define a [focus_blocks] shortcode that renders container with nested [focus_block].
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_focus_blocks_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'focus_blocks' );
	$content = do_shortcode( $content );

	// exclude p tag to avoid empty ones
	$allowed_tags = [ 'span' => [ 'class' => [], 'style' => [] ], 'img' => [ 'src' => [], 'height' => [], 'width' => [], 'alt' => [], 'style' => [], 'class' => [], 'style' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [], 'target' => [] ], 'h3' => [ 'class' => [], 'style' => [] ], 'div' => [ 'class' => [], 'style' => [], 'aria-hidden' => [] ], 'h2' => [ 'class' => [] ] ];

	ob_start();
	?>

	<div class="mw-980 mod-margin-bottom <?php echo esc_attr( $atts['class'] ) ?>">
		<?php if ( !empty( $atts['title'] ) ) { ?>
			<h2><?php echo esc_html( $atts['title'] ) ?></h2>
		<?php } ?>
		<div class="quotes-section-container">
			<div class="flex flex-medium flex-wrap flex-space-between">
				<?php echo wp_kses( $content, $allowed_tags ) ?>
			</div>
		</div>
	</div>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'focus_blocks', 'wmf_focus_blocks_callback' );

/**
 * Define a [focus_block] shortcode that renders focus block.
 *
 * @param array $atts Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_focus_block_callback( $atts = [], $content = '' ) {
	$defaults = [
		'title' => '',
		'uri' => '',
		'link_text' => '',
		'img' => '',
		'class' => '',
	];
	$atts = shortcode_atts( $defaults, $atts, 'focus_block' );
	static $index = 0;
	$index++;

	$image_id = custom_get_attachment_id_by_slug( $atts['img'] );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, array( 600, 600 ) ) : null;

	ob_start();
	?>

	<a href="<?php echo esc_url( $atts['uri'] ); ?>" class="w-32p mod-margin-bottom_sm focus-block rounded shadow <?php echo esc_attr( $atts['class'] ) ?>" target="_blank">
		<div class="card">
			<?php if ( isset($image_url) ) : ?>
			<div class="bg-img-container">
				<div class="bg-img" style="background-image: url(<?php echo esc_url( $image_url ); ?>);"></div>
			</div>
			<?php endif; ?>

			<div class="card-content ">

				<?php if ( ! empty( $atts['title'] ) ) : ?>
				<h2 class="h2"><?php echo esc_html( $atts['title'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $content ) ) : ?>
				<div class="mar-bottom">
					<span class="p"><?php echo wp_kses_post( $content ); ?></span>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $atts['uri'] ) && ! empty( $atts['link_text'] ) ) : ?>
					<div class="arrow-link stick-to-bottom" data-href="<?php echo esc_url( $atts['uri'] ); ?>" aria-hidden="true"><?php echo esc_html( $atts['link_text'] ); ?></div>
				<?php endif; ?>

			</div>
		</div>
	</a>

	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'focus_block', 'wmf_focus_block_callback' );
