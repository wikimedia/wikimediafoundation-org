<?php
/**
 * Handles page fact module for multiple facts.
 *
 * @package shiro
 */

$template_args = $args;

$wmf_tweet_this = get_theme_mod( 'wmf_tweet_this_copy', __( 'Tweet this', 'shiro-admin' ) );

if ( empty( $template_args['image'] ) && empty( $template_args['facts'] ) ) {
	return;
}

$image = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'large' ) : $image;

$fact_width = 3 === count( $template_args['facts'] ) ? 'w-32p' : 'w-48p';

?>

<div class="fact-container">

	<div class="mw-980 flex flex-medium flex-wrap flex-space-between">
		<?php
		foreach ( $template_args['facts'] as $i => $fact ) :
			$fact_width = 0 !== $i ? $fact_width . ' hide-sm' : $fact_width;
			?>
		<div class="fact-inner rounded module-mu <?php echo esc_attr( $fact_width ); ?> mar-bottom_lg">
			<div class="fact-text-wrap">
				<?php if ( ! empty( $fact['heading'] ) ) : ?>
				<h2 class="fact-stat-lg"><?php echo esc_html( $fact['heading'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $fact['content'] ) ) : ?>
				<h3 class="fact mar-bottom"><?php echo esc_html( $fact['content'] ); ?></h3>
				<?php endif; ?>
			</div>
			<?php

			$template_args = array(
				'message'  => sprintf( '%1$s %2$s', str_replace(['%', '+'], ['%25', '%2B'], $fact['heading']), str_replace(['%', '+'], ['%25', '%2B'], $fact['content']) ),
				'services' => array( 'twitter' ),
				'title'    => $wmf_tweet_this,
			);
			get_template_part( 'template-parts/modules/social/share', 'horizontal', $template_args );
			?>
		</div>
		<?php endforeach; ?>
	</div>

</div>
