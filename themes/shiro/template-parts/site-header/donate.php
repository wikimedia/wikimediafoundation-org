<?php

/**
 * Adds donate button(s)
 *
 * @package shiro
 */

$page_id = get_queried_object_id();
$uri     = get_theme_mod( 'wmf_donate_now_uri',
	'https://donate.wikimedia.org/?utm_medium=wmfSite&utm_campaign=comms' );
$copy    = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate', 'shiro' ) );
?>

<div class="donate-btn">
	<div class="donate-btn--desktop">
		<a href="<?php echo esc_url( $uri ); ?>&utm_source=<?php echo esc_attr( $page_id ); ?>">
			<img src="<?php echo wmf_get_svg_uri( 'lock-pink' ); ?>"
				 alt="" class="secure">
			<?php echo esc_html( $copy ); ?>
		</a>
	</div>
	<div class="donate-btn--mobile">
		<a href="<?php echo esc_url( $uri ); ?>&utm_source=<?php echo esc_attr( $page_id ); ?>">
			<img src="<?php echo wmf_get_svg_uri( 'heart-pink' ); ?>"
				 alt="<?php echo esc_attr( $copy ); ?>">
		</a>
	</div>
</div>
