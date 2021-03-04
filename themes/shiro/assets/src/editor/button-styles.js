import { InspectorControls } from '@wordpress/block-editor';
import { unregisterBlockStyle, registerBlockStyle } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

import IconSelector from './components/IconSelector';

const withIconSelector = createHigherOrderComponent( ButtonBlockEdit => {
	/**
	 * Insert the icon selector in the inspector controls for the button block.
	 */
	return function ButtonBlockEditWithIconSelector( props ) {
		const { name, attributes, setAttributes } = props;

		return (
			<>
				<ButtonBlockEdit { ...props } />
				{ name === 'core/button' && <InspectorControls>
					<IconSelector
						attributes={ attributes }
						setAttributes={ setAttributes }
					/>
				</InspectorControls> }
			</>
		);
	};
} );

wp.hooks.addFilter(
	'editor.BlockEdit',
	'shiro/button-styles',
	withIconSelector
);

/**
 * Change button example to:
 *
 * - Remove the backgroundColor attribute, this will make the preview show our
 *   custom styles.
 * - Add our custom attributes & variations
 *
 * @param {object} settings The original block settings.
 * @param {string} name     Name of the block.
 * @returns {object} The altered settings.
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
