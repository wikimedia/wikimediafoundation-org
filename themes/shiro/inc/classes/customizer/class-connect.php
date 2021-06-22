<?php
/**
 * Contact Customizer.
 *
 * @package shiro
 */

namespace WMF\Customizer;

/**
 * Setups the customizer and related settings.
 * Adds new fields to create sections for the contact details
 */
class Connect extends Base {
	/**
	 * Get the default text for a field defined for the Contact customizer.
	 *
	 * These are defined here because the Customizer values are not saved, and
	 * are needed in get_theme_mod() calls in templates. This allows for a
	 * centralized location for this values and reduces duplication.
	 *
	 * @param string $setting
	 *
	 * @return string
	 */
	public static function defaults( string $setting = '' ): string {
		$defaults = [
			'wmf_subscribe_action'            => 'https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56',
			'wmf_subscribe_additional_fields' => '<input type="hidden" value="2" name="group[4037]" id="mce-group[4037]-4037-1">',
		];

		return $defaults[ $setting ] ?? '';
	}

	/**
	 * Add Customizer fields for header section.
	 */
	public function setup_fields() {
		$section_id = 'wmf_connect';
		$this->customize->add_section(
			$section_id, array(
				'title'    => __( 'Connect', 'shiro-admin' ),
				'priority' => 70,
			)
		);

		$control_id = 'wmf_connect_reusable_block';
		$this->customize->add_setting( $control_id );
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
		$reusable_blocks   = get_posts( [
			'post_type'   => 'wp_block',
			'numberposts' => 50,
		] );
		// phpcs:enable

		$selectable_blocks = [];
		foreach ( $reusable_blocks as $block ) {
			if ( has_block( 'shiro/contact', $block->ID )
			     || has_block( 'shiro/mailchimp-subscribe', $block->ID ) ) {
				$selectable_blocks[ $block->ID ] = $block->post_title;
			}
		}

		// We're using `+` instead of `array_merge` because array_merge rewrites numeric IDs
		$choices = [ 0 => 'No CTA' ] + $selectable_blocks;
		$this->customize->add_control(
			$control_id, [
				'label'       => __( 'Connect Block', 'shiro-admin' ),
				'type'        => 'select',
				'choices'     => $choices,
				'section'     => $section_id,
				'description' => count( $selectable_blocks ) > 0
					? __( 'Select a reusable block to be shown in the "Connect" area.', 'shiro-admin' )
					: sprintf( __( '<strong>There are no viable reusable blocks!</strong> This reusable block must include at least one of the the Connect or the Mailchimp Subscribe blocks. Please <a href="%s">create one</a>.',
						'shiro-admin' ), admin_url( 'edit.php?post_type=wp_block' ) ),
			]
		);

		$control_id = 'wmf_subscribe_action';
		$this->customize->add_setting( $control_id, [
			'default' => $this::defaults( 'wmf_subscribe_action' )
		] );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Subscribe form action URL', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'text',
			)
		);

		$control_id = 'wmf_subscribe_additional_fields';
		$this->customize->add_setting( $control_id, [
			'default' => $this::defaults( 'wmf_subscribe_additional_fields' )
		] );
		$this->customize->add_control(
			$control_id, array(
				'label'   => __( 'Subscribe form additional fields', 'shiro-admin' ),
				'section' => $section_id,
				'type'    => 'textarea',
			)
		);
	}

}
