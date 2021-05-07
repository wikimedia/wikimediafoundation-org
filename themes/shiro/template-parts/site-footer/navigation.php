<?php

$wmf_projects_menu_label            = get_theme_mod( 'wmf_projects_menu_label', __( 'Projects', 'shiro' ) );
$wmf_movement_affiliates_menu_label = get_theme_mod( 'wmf_movement_affiliates_menu_label',
	__( 'Movement Affiliates', 'shiro' ) );
$wmf_other_links_menu_label         = get_theme_mod( 'wmf_other_links_menu_label', __( 'Other', 'shiro' ) );
?>
<div class="footer-row flex flex-medium projects-affiliation">
	<div class="w-50p">
		<h2 class="h3"><?php echo esc_html( $wmf_projects_menu_label ); ?></h2>
		<?php
		if ( has_nav_menu( 'footer-projects' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'footer-projects',
					'menu_class'     => 'lists-wrap flex flex-all',
					'container'      => '',
					'items_wrap'     => '<div id="%1$s" class="%2$s"><ul class="w-32p">%3$s</ul></div>',
					'walker'         => new WMF\Walkers\Columns(),
				)
			);
		}
		?>
	</div>

	<div class="w-25p">
		<h2 class="h3"><?php echo esc_html( $wmf_movement_affiliates_menu_label ); ?></h2>
		<div class="lists-wrap">
			<?php
			if ( has_nav_menu( 'footer-affiliates' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer-affiliates',
						'menu_class'     => '',
						'container'      => '',
					)
				);
			}
			?>
		</div>
	</div>

	<div class="w-25p">
		<h2 class="h3"><?php echo esc_html( $wmf_other_links_menu_label ); ?></h2>
		<div class="lists-wrap">
			<?php
			if ( has_nav_menu( 'footer-legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer-legal',
						'menu_class'     => '',
						'container'      => '',
					)
				);
			}
			?>
		</div>
		<br>
	</div>

</div>
