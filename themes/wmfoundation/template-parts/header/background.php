<?php
/**
 * Adds Header background
 *
 * @package wmfoundation
 */

if ( ! is_page() ) {
	return;
}

$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );

if ( empty( $bg_opts['image'] ) ) {
	return;
}

?>
<div class="bg-img--<?php echo isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? 'pink' : 'blue'; ?> bg-pattern-container">
	<div class="bg-img-container bg-pattern">
		<div class="bg-img" style="background-image: url(<?php echo esc_url( wp_get_attachment_url( $bg_opts['image'] ) ); ?>);"></div>
	</div>
</div>
