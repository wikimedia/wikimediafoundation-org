<?php
/**
 * Handles Support Module CTA.
 *
 * @package wmfoundation
 */

$image_id  = get_theme_mod( 'wmf_support_image' );
$image     = is_numeric( $image_id ) ? wp_get_attachment_image_url( $image_id, 'image_16x9_large' ) : '';
$heading   = get_theme_mod( 'wmf_support_heading' );
$content   = get_theme_mod( 'wmf_support_content' );
$link_uri  = get_theme_mod( 'wmf_support_link_uri' );
$link_text = get_theme_mod( 'wmf_support_link_text' );

if ( empty( $link_uri ) || empty( $link_text ) ) {
	return; // CTAs need links.
}

?>

<div class="w-100p cta mod-margin-bottom cta-primary img-left-content-right bg-img--pink btn-blue">
	<div class="mw-1360">
		<div class="card">
			<?php if ( ! empty( $image ) ) : ?>
			<div class="bg-img-container">
				<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>)"></div>
			</div>
			<?php endif; ?>

			<div class="card-content w-45p">

				<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="h2"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $content ) ) : ?>
					<p><?php echo esc_html( $content ); ?></p>
				<?php endif; ?>

				<a class="btn " href="<?php echo esc_url( $link_uri ); ?>"><?php echo esc_html( $link_text ); ?></a>

			</div>
		</div>

	</div>
</div>
