import { __ } from '@wordpress/i18n';
import './style.scss';

export const styles = [
	{
		name: 'default',
		label: __( 'Default', 'shiro' ),
		isDefault: true,
	},
	{
		name: 'pullquote',
		label: __( 'Pullquote', 'shiro' ),
	},
];

/**
 * Change quote registration to fit the needs of the Wikimedia design.
 *
 * @param {object} settings The original block settings.
 * @param {string} name     Name of the block.
 * @returns {object} The altered settings.
 */
function changeQuoteRegistration( settings, name ) {
	if ( name !== 'core/quote' ) {
		return settings;
	}

	return {
		...settings,
		// To register these styles we could also export them from this file.
		// However, the problem in that case is that core styles aren't removed.
		styles,
	};
}

export const
	name = 'core/quote',
	filters = [
		{
			hook: 'blocks.registerBlockType',
			namespace: 'shiro/button-styles',
			callback: changeQuoteRegistration,
		},
	];
