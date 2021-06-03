<?php

/**
 * Adds donate button(s)
 *
 * @package shiro
 */

$page_id = get_queried_object_id();
$uri     = get_theme_mod( 'wmf_donate_now_uri',
	'https://donate.wikimedia.org/?utm_medium=wmfSite&utm_campaign=comms' );
$copy    = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate', 'shiro-admin' ) );
?>

<div class="nav-donate">
	<a href="<?php echo esc_url( $uri ); ?>&utm_source=<?php echo esc_attr( $page_id ); ?>" class="nav-donate__link">
		<?php wmf_show_icon( 'lock', 'nav-donate__icon nav-donate__icon--lock' ); ?>
		<?php wmf_show_icon( 'heart', 'nav-donate__icon nav-donate__icon--heart' ); ?>
		<span class="nav-donate__copy">
			<?php echo esc_html( $copy ); ?>
		</span>
	</a>
</div>
