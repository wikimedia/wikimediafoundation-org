/**
 * Export registration information for Vega Lite block.
 */
import blockData from './block.json';
import EditVisualization from './EditVisualization';

export const name = blockData.name;

export const settings = {
	// Apply the block settings from the JSON configuration file.
	...blockData,

	/**
	 * Render the editor UI for this block.
	 *
	 * @returns {React.ReactNode} Editorial interface to display in block editor.
	 */
	edit: EditVisualization,

	/**
	 * Return null on save so rendering can be done in PHP.
	 *
	 * @returns {null} Empty so that server can complete rendering.
	 */
	save() {
		return null;
	},
};
