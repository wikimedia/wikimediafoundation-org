/**
 * Style variants used commonly for blocks.
 */

import { __ } from '@wordpress/i18n';

const styles = [
	{
		name: 'base90',
		label: __( 'Light', 'shiro' ),
		isDefault: true,
	},
	{
		name: 'base70',
		label: __( 'Gray', 'shiro' ),
	},
	{
		name: 'base0',
		label: __( 'Dark', 'shiro' ),
	},
	{
		name: 'blue90',
		label: __( 'Blue - Faded', 'shiro' ),
	},
	{
		name: 'blue50',
		label: __( 'Blue - Vibrant', 'shiro' ),
	},
	{
		name: 'red90',
		label: __( 'Red - Faded', 'shiro' ),
	},
	{
		name: 'red50',
		label: __( 'Red - Vibrant', 'shiro' ),
	},
	{
		name: 'yellow90',
		label: __( 'Yellow - Faded', 'shiro' ),
	},
	{
		name: 'yellow50',
		label: __( 'Yellow - Vibrant', 'shiro' ),
	},
];

/**
 * If no style class is applied, then add a default class.
 *
 * @param {object} blockProps A blockProps object
 * @param {string} blockProps.className The classes we're concerned with
 * @param {string} [defaultStyle=is-style-base90] The style to be applied
 * @returns {object} A blockProps object
 */
const applyDefaultStyle = ( blockProps, defaultStyle = 'is-style-base90' ) => {
	if ( ! blockProps.className.includes( 'is-style-' ) ) {
		blockProps.className = `${blockProps.className} ${defaultStyle}`;
	}

	return blockProps;
};

export default styles;
export {
	applyDefaultStyle,
};
