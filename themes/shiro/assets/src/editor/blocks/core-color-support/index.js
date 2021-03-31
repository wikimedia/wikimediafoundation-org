/**
 * Remove support for picking text color from all blocks that support color pickers.
 */

/**
 * Remove the "text" property from the block supports for all blocks that
 * support the color picker. The text color in all cases should be set
 * intelligently based on the background color to preserve AAA contrast
 * requirements for accessibility.
 *
 * @param {object} settings Block registration settings.
 * @param {string} name Block name.
 */
const removeTextColorSelection = ( settings, name ) => {

	if ( ! settings.supports?.color ) {
		return settings;
	}

	return {
		...settings,

		supports: {
			...settings.supports,

			color: {
				...settings.supports.color,

				text: false,
			},
		},
	};
};

export const filters = [
	{
		hook: 'blocks.registerBlockType',
		namespace: 'shiro/color-supports',
		callback: removeTextColorSelection,
	},
];
