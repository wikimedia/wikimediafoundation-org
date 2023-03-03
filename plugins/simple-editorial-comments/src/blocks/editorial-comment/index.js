import blockData from './block.json';
import EditEditorialComment from './EditEditorialComment';

export const name = blockData.name;

export const settings = {
	// Apply the block settings from the JSON configuration file.
	...blockData,

	edit: EditEditorialComment,

	/**
	 * Return null on save so rendering can be done in PHP.
	 *
	 * @returns {null} Empty so that server can complete rendering.
	 */
	save() {
		return null;
	},
};
