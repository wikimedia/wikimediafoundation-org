/**
 * If no style class is applied, then add a default class.
 *
 * @param {object} blockProps A blockProps object
 * @param {string} blockProps.className The classes we're concerned with
 * @param {string} [defaultStyle=is-style-base90] The style to be applied
 * @returns {object} A blockProps object
 */
const applyDefaultStyle = ( blockProps, defaultStyle = 'is-style-base90' ) => {
	if ( blockProps.className.indexOf( 'is-style-' ) === -1 ) {
		blockProps.className = `${blockProps.className} ${defaultStyle}`;
	}

	return blockProps;
};

export default applyDefaultStyle;
