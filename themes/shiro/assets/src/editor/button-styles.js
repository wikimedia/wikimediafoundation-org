import { unregisterBlockStyle, registerBlockStyle } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

/**
 * Change button example to remove the backgroundColor attribute, this will make
 * the preview show our custom styles.
 */
function changeButtonExample( settings, name ) {
	if ( name !== 'core/button' ) {
		return settings;
	}

	return {
		...settings,
		example: {
			attributes: {
				text: __( 'Call to Action', 'shiro' ),
			},
		},
	};
}
addFilter(
	'blocks.registerBlockType',
	'shiro/button-styles',
	changeButtonExample
);

domReady( () => {
	registerBlockStyle( 'core/button', {
		name: 'primary',
		label: __( 'Primary', 'shiro' ),
		isDefault: true,
	} );

	registerBlockStyle( 'core/button', {
		name: 'normal',
		label: __( 'Normal', 'shiro' ),
	} );

	registerBlockStyle( 'core/button', {
		name: 'destructive',
		label: __( 'Destructive', 'shiro' ),
	} );

	registerBlockStyle( 'core/button', {
		name: 'primary-old',
		label: __( 'Primary (Old)', 'shiro' ),
		isDefault: true,
	} );

	registerBlockStyle( 'core/button', {
		name: 'secondary',
		label: __( 'Secondary', 'shiro' ),
	} );

	registerBlockStyle( 'core/button', {
		name: 'tertiary',
		label: __( 'Tertiary', 'shiro' ),
	} );

	unregisterBlockStyle( 'core/button', 'outline' );
	unregisterBlockStyle( 'core/button', 'fill' );
} );
