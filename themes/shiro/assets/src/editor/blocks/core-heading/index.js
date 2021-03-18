import { __ } from '@wordpress/i18n';

/**
 * Overrides to the core/heading block.
 */

export const name = 'core/heading';

/**
 * Remove the ability to set alignment or font size on headings.
 *
 * @param {object} settings Block registration settings.
 * @param {string} name     Nmae of the block.
 * @returns {object} The altered block registration settings.
 */
const filterHeadingSupports = ( settings, name ) => {
	if ( name !== 'core/heading' ) {
		return settings;
	}

	return {
		...settings,

		supports: {
			...settings.supports,
			align: false,
			fontSize: false,
		},
	};
};

export const filters = [
	{
		hook: 'blocks.registerBlockType',
		namespace: 'shiro/heading-supports',
		callback: filterHeadingSupports,
	},
];

export const mimicHeadingStyles = [
	{
		name: 'h1',
		label: __( 'Mimic h1', 'shiro' ),
	},
	{
		name: 'h2',
		label: __( 'Mimic h2', 'shiro' ),
	},
	{
		name: 'h3',
		label: __( 'Mimic h3', 'shiro' ),
	},
	{
		name: 'h4',
		label: __( 'Mimic h4', 'shiro' ),
	},
	{
		name: 'h5',
		label: __( 'Mimic h5', 'shiro' ),
	},
	{
		name: 'h6',
		label: __( 'Mimic h6', 'shiro' ),
	},
];

export const styles = mimicHeadingStyles;
