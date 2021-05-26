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
		$reusable_blocks   = get_posts( [
			'post_type'   => 'wp_block',
			'numberposts' => 50,
		] );
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
				'label'       => __( 'Connect' ),
				'type'        => 'select',
				'choices'     => $choices,
				'section'     => $section_id,
				'description' => count( $selectable_blocks ) > 0
					? __( 'Select a reusable block to be shown in the "Connect" area.', 'shiro-admin' )
					: sprintf( __( '<strong>There are no viable reusable blocks!</strong> This reusable block must include at least one of the the Connect or the Mailchimp Subscribe blocks. Please <a href="%s">create one</a>.',
						'shiro-admin' ), admin_url( 'edit.php?post_type=wp_block' ) ),
			]
		);
	}

}
