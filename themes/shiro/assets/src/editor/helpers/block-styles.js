/**
 * Style variants used commonly for blocks.
 */

import { __ } from '@wordpress/i18n';

/**
 * The default style (or an empty string if none is set).
 *
 * @type {string}
 */
const defaultStyle = {
	name: 'base90',
	className: 'is-style-base90',
	label: __( 'Light', 'shiro' ),
	isDefault: true,
};

const styles = [
	defaultStyle,
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
 * The default class is the first item in the shared block-styles.js with the
 * isDefault: true.
 *
 * @param {object} blockProps A blockProps object
 * @param {string} blockProps.className The classes we're concerned with
 * @param {string} [style=] The style to be applied
 * @returns {object} A blockProps object
 */
const applyDefaultStyle = ( blockProps, style = defaultStyle.className ) => {

	if ( ! blockProps.className.includes( 'is-style-' ) ) {
		blockProps.className = `${blockProps.className} ${style}`;
	}

	return blockProps;
};

export default styles;
export {
	defaultStyle,
	applyDefaultStyle,
};
