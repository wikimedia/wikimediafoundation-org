/**
 * Helper to figure out which available dataset has been set by URL in a spec.
 *
 * Returns the option to manage data inline, if not present.
 *
 * @param {Dataset[]} datasets      Datasets list.
 * @param {object}    json          Vega spec.
 * @param {?object}   defaultOption Value to return if no matching dataset is found.
 * @returns {Dataset|object} Selected dataset, or first option in list.
 */
export const getSelectedDatasetFromSpec = ( datasets, json, defaultOption = null ) => {
	if ( json?.data?.url ) {
		const activeDataset = datasets.find( ( { url } ) => url === json.data.url );
		if ( activeDataset ) {
			return activeDataset;
		}
	}
	return defaultOption;
};
