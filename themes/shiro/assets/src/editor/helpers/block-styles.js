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
	label: __( 'Light', 'shiro-admin' ),
	isDefault: true,
};

const styles = [
	defaultStyle,
	{
		name: 'base70',
		label: __( 'Gray', 'shiro-admin' ),
	},
	{
		name: 'base0',
		label: __( 'Dark', 'shiro-admin' ),
	},
	{
		name: 'blue90',
		label: __( 'Blue - Faded', 'shiro-admin' ),
	},
	{
		name: 'blue50',
		label: __( 'Blue - Vibrant', 'shiro-admin' ),
	},
	{
		name: 'red90',
		label: __( 'Red - Faded', 'shiro-admin' ),
	},
	{
		name: 'red50',
		label: __( 'Red - Vibrant', 'shiro-admin' ),
	},
	{
		name: 'yellow90',
		label: __( 'Yellow - Faded', 'shiro-admin' ),
	},
	{
		name: 'yellow50',
		label: __( 'Yellow - Vibrant', 'shiro-admin' ),
	},
	{
		name: 'donate-red90',
		label: __( 'Donate', 'shiro-admin' ),
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
