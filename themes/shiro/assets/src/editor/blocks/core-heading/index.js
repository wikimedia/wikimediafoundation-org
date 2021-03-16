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
