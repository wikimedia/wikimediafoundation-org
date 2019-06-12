<?php
/**
 * Adds Header navigation
 *
 * @package shiro
 */


$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate Now', 'shiro' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', '#' );
$wmf_menu_button = get_theme_mod( 'wmf_menu_button_copy', __( 'MENU', 'shiro' ) );

$wmf_translation_selected = get_theme_mod( 'wmf_selected_translation_copy', __( 'Languages', 'shiro' ) );
$wmf_translations = wmf_get_translations();
?>


<nav class="main-nav flex flex-medium flex-align-center">

	<?php
	if ( has_nav_menu( 'header' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'header',
				'menu_class'     => 'nav-links list-inline',
				'container'      => '',
			)
		);
	}
	?>
	<div class="search-cta-container">
		<?php get_search_form( true ); ?>
		<!-- <a class="nav-cta btn <?php echo esc_attr( wmf_get_header_cta_button_class() ); ?>" href="<?php echo esc_url( $wmf_donate_uri ); ?>"><?php echo esc_html( $wmf_donate_button ); ?></a> -->
	</div>


	<?php if ( $wmf_translations !== false ) : ?>
		<?php
			# Find which is the current language and display that
			$selected = array_filter($wmf_translations, function ($lang) {
				return $lang['selected'];
			});

			if ($selected[0]['name'] === "English") {
				$lang_code = "en";
			} else {
				$lang_code = explode('/',$selected[0]['uri'])[3];
			}
		?>
		<div class="language-dropdown">
			<button aria-label="Select language">
				<span class="btn-label-a11y">Current language: </span>
				<img src="/wp-content/themes/shiro/assets/src/svg/language.svg" alt="" class="language-icon">
				<span><?php echo $lang_code; ?></span>
				<img src="/wp-content/themes/shiro/assets/src/svg/down.svg" alt="" class="down-indicator">
			</button>

			<div class="language-list">
				<ul>
					<?php foreach ( $wmf_translations as $wmf_index => $wmf_translation ) : ?>
						<li>
							<?php if ( $wmf_translation['selected'] ) : ?>
							<a class="selected" href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a>
							<?php else : ?>
							<a href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	<?php endif ?>

</nav>
