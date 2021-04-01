import './style.scss';

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
		styles: [],
	};
}

export const
	name = 'core/quote',
	styles = [],
	filters = [
		{
			hook: 'blocks.registerBlockType',
			namespace: 'shiro/button-styles',
			callback: changeQuoteRegistration,
		},
	];
