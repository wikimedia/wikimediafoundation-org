<?php

$wmf_projects_menu_label            = get_theme_mod( 'wmf_projects_menu_label', __( 'Projects', 'shiro' ) );
$wmf_movement_affiliates_menu_label = get_theme_mod( 'wmf_movement_affiliates_menu_label',
	__( 'Movement Affiliates', 'shiro' ) );
$wmf_other_links_menu_label         = get_theme_mod( 'wmf_other_links_menu_label', __( 'Other', 'shiro' ) );

$default_args = [
	'menu_class' => 'site-footer__navmenu',
	'container'  => '',
	'items_wrap' => '<nav class="%2$s"><ul>%3$s</ul></nav>',
];

$menus = [
	[
		'label'    => $wmf_projects_menu_label,
		'location' => 'footer-projects',
		'args'     => [
			'theme_location' => 'footer-projects',
		]
	],
	[
		'label'    => $wmf_movement_affiliates_menu_label,
		'location' => 'footer-affiliates',
		'args'     => [
			'theme_location' => 'footer-affiliates',
		]
	],
	[
		'label'    => $wmf_other_links_menu_label,
		'location' => 'footer-legal',
		'args'     => [
			'theme_location' => 'footer-legal',
		]
	]
]; ?>

<div class="site-footer__navigation">

	<?php foreach ( $menus as $menu ) {
		if ( has_nav_menu( $menu['location'] ?? '' ) ) {
			$args = array_merge( $default_args, $menu['args'] ?? [] );
			?>
			<div class="site-footer__navigation-section site-footer__navigation-section--<?php echo esc_attr( $menu['location'] ); ?>">
				<h2><?php echo esc_html( $menu['label'] ); ?></h2>
				<?php wp_nav_menu( $args ); ?>
			</div>
			<?php
		}
	}
	?>

</div>
