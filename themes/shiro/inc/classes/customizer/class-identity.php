<?php
/**
 * Extend Site Identity customizer settings.
 *
 * @package shiro
 */

namespace WMF\Customizer;

/**
 * Adds controls & settings to existing (core-provided) "Site Identity" section
 * of the Customizer.
 */
class Identity extends Base {
	public function setup_fields() {
		$section_id = 'title_tagline';

		$this->customize->add_setting( 'wmf_site_logo', [
			'default' => false,
			'type'    => 'theme_mod',
		] );

		$this->customize->add_control( new \WP_Customize_Image_Control( $this->customize, 'wmf_shiro_logo', [
			'label'       => __( 'Site Logo', 'shiro-admin' ),
			'description' => __( 'Set the logo that appears in the site header. <strong>SVGs strongly encouraged.</strong>' ),
			'settings'    => 'wmf_site_logo',
			'section'     => $section_id,
		] ) );
	}
}
