import { InspectorControls } from '@wordpress/block-editor';
import { unregisterBlockStyle } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';

import IconSelector from './IconSelector';

import './style.scss';

const withIconSelector = createHigherOrderComponent( ButtonBlockEdit => {
	/**
	 * Insert the icon selector in the inspector controls for the button block.
	 */
	return function ButtonBlockEditWithIconSelector( props ) {
		const { name, attributes, setAttributes } = props;

		return (
			<>
				<ButtonBlockEdit { ...props } />
				{ name === 'core/button' && (
					<InspectorControls>
						<IconSelector
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					</InspectorControls>
				) }
			</>
		);
	};
} );

/**
 * Change button registration to:
 *
 * - Change example to remove the backgroundColor attribute, this will make the
 *   preview show our custom styles.
 * - Add our custom attributes & variations
 *
 * @param {object} settings The original block settings.
 * @param {string} name     Name of the block.
 * @returns {object} The altered settings.
 */
function changeButtonRegistration( settings, name ) {
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
		variations: [
			{
				name: 'donate-pink',
				title: __( 'Pink donate button', 'shiro' ),
				attributes: {
					text: __( 'Donate now', 'shiro' ),
					className: 'is-style-secondary has-icon has-icon-lock-white',
				},
			},
		],
	};
}

domReady( () => {
	unregisterBlockStyle( 'core/button', 'outline' );
	unregisterBlockStyle( 'core/button', 'fill' );
} );

export const
	name = 'core/button',
	styles = [
		{
			name: 'primary',
			label: __( 'Primary', 'shiro' ),
			isDefault: true,
		},
		{
			name: 'secondary',
			label: __( 'Secondary', 'shiro' ),
		},
		{
			name: 'tertiary',
			label: __( 'Tertiary', 'shiro' ),
		},
		{
			name: 'as-link',
			label: __( 'As link', 'shiro' ),
		},
	],
	filters = [
		{
			hook: 'editor.BlockEdit',
			namespace: 'shiro/button-styles',
			callback: withIconSelector,
		},
		{
			hook: 'blocks.registerBlockType',
			namespace: 'shiro/button-styles',
			callback: changeButtonRegistration,
		},
	];
