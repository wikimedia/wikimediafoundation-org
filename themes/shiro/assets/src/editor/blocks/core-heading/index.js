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
		name: 'sans-p',
		label: __( 'Sans p', 'shiro-admin' ),
	},
	{
		name: 'h1',
		label: __( 'Mimic h1', 'shiro-admin' ),
	},
	{
		name: 'h2',
		label: __( 'Mimic h2', 'shiro-admin' ),
	},
	{
		name: 'h3',
		label: __( 'Mimic h3', 'shiro-admin' ),
	},
	{
		name: 'sans-h3',
		label: __( 'Sans h3', 'shiro-admin' ),
	},
	{
		name: 'h4',
		label: __( 'Mimic h4', 'shiro-admin' ),
	},
	{
		name: 'h5',
		label: __( 'Mimic h5', 'shiro-admin' ),
	},
	{
		name: 'h6',
		label: __( 'Mimic h6', 'shiro-admin' ),
	},
];

export const styles = mimicHeadingStyles;
