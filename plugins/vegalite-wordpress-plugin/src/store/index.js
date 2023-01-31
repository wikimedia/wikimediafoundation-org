import { createSelector } from 'reselect';

import { createReduxStore, register } from '@wordpress/data';

import * as api from '../util/datasets';

const actions = {
	/* eslint-disable jsdoc/require-returns-description */
	/**
	 * @param {string} filename Filename of dataset to retrieve.
	 * @returns {ReduxAction}
	 */
	getDataset: ( filename ) => ( {
		type: 'DATASET_GET',
		filename,
	} ),

	/**
	 * @param {string} url Full URL of dataset to retrieve.
	 * @returns {ReduxAction}
	 */
	getDatasetByUrl: ( url ) => ( {
		type: 'DATASET_GET_BY_URL',
		url,
	} ),

	/**
	 * @returns {ReduxAction}
	 */
	getDatasets: () => ( {
		type: 'DATASETS_GET_ALL',
	} ),

	// These functions are Thunks, functions which return actions and sequence
	// asynchronous behavior. Using Thunks with WordPress data stores requires
	// an experimental flag opt-in prior to WordPress 6.0.

	/**
	 * @param {Dataset} dataset Dataset to save.
	 * @returns {Function} Thunk action function.
	 */
	createDataset: ( dataset ) => async ( { dispatch } ) => {
		const createdDataset = await api.createDataset( dataset );
		dispatch( actions.setDataset( createdDataset ) );
		dispatch( actions.getDatasets() );
		return createdDataset;
	},

	/**
	 * @param {Dataset} dataset Dataset to update.
	 * @returns {Function} Thunk action function.
	 */
	updateDataset: ( dataset ) => async ( { dispatch } ) => {
		const updatedDataset = await api.updateDataset( dataset );
		dispatch( actions.setDataset( updatedDataset ) );
		return updatedDataset;
	},

	/**
	 * @param {Dataset} dataset Dataset to delete.
	 * @returns {Function} Thunk action function.
	 */
	deleteDataset: ( dataset ) => async ( { dispatch } ) => {
		const result = await api.deleteDataset( dataset );

		if ( result ) {
			dispatch( actions.removeDataset( dataset ) );
		}

		return { deleted: result };
	},

	/**
	 * @param {Dataset[]} datasets Array of datasets to save in store.
	 * @returns {ReduxAction}
	 */
	setDatasets: ( datasets ) => ( {
		type: 'DATASETS_SET',
		datasets,
	} ),

	/**
	 * @param {Dataset} dataset Dataset to save in store.
	 * @returns {ReduxAction}
	 */
	setDataset: ( dataset ) => ( {
		type: 'DATASET_SET',
		dataset,
	} ),

	/**
	 * @param {Dataset} dataset Dataset to remove from store.
	 * @returns {ReduxAction}
	 */
	removeDataset: ( dataset ) => ( {
		type: 'DATASET_UNSET',
		dataset,
	} ),
	/* eslint-enable jsdoc/require-returns-description */
};

// Controls enable asynchronous fulfillment of get requests. A subscribed
// component (using useState) initiates a request for the data on first
// render, and then gets re-rendered with the fulfilled data once available.
const controls = {
	/* eslint-disable jsdoc/require-returns */
	DATASETS_GET_ALL: () => api.getDatasets(),
	/** @param {ReduxAction} action Dispatched action */
	DATASET_GET: ( { filename } ) => api.getDataset( filename ),
	/** @param {ReduxAction} action Dispatched action */
	DATASET_GET_BY_URL: ( { url } ) => api.getDatasetByUrl( url ),
	/* eslint-enable jsdoc/require-returns */
};

// Resolvers sequence controls and actions to request data asynchronously.
// These functions allow select methods to trigger async behavior.
const resolvers = {
	/* eslint-disable jsdoc/require-returns */
	/**
	 * Sequence the actions necessary to request all datasets.
	 */
	*getDatasets() {
		/** @type {Dataset[]} */
		const datasets = yield actions.getDatasets();
		return actions.setDatasets( datasets );
	},
	/**
	 * Sequence the actions necessary to request a dataset by filename.
	 *
	 * @param {string} filename Filename of a dataset to retrieve.
	 */
	*getDataset( filename ) {
		if ( filename === 'inline' ) {
			return null;
		}
		/** @type {Dataset} */
		const dataset = yield actions.getDataset( filename );
		return actions.setDataset( dataset );
	},
	/**
	 * Sequence the actions necessary to request a dataset's content by filename.
	 *
	 * @param {string} filename Filename of a dataset to retrieve.
	 */
	*getDatasetContent( filename ) {
		if ( filename === 'inline' ) {
			return '';
		}
		/** @type {Dataset} */
		const dataset = yield actions.getDataset( filename );
		return actions.setDataset( dataset );
	},
	/**
	 * Sequence the actions necessary to request a dataset by URL.
	 *
	 * @param {string} url Public URL of a dataset to retrieve.
	 */
	*getDatasetByUrl( url ) {
		/** @type {Dataset} */
		const dataset = yield actions.getDatasetByUrl( url );
		return actions.setDataset( dataset );
	},
	/* eslint-enable jsdoc/require-returns */
};

/**
 * Redux dataset store.
 *
 * @typedef DatasetStore
 * @property {object.<string, Dataset>} datasets Dataset dictionary keyed by filename.
 */

/** @type {DatasetStore} */
const DEFAULT_STATE = {
	datasets: {},
};

/**
 * Store reducer.
 *
 * @param {DatasetStore} state  State tree
 * @param {object} action Action object.
 * @returns {object} Transformed state tree.
 */
const reducer = ( state = DEFAULT_STATE, action ) => {
	/* eslint-disable default-case */
	switch ( action.type ) {
		case 'DATASETS_SET':
			return {
				...state,
				datasets: {
					...action.datasets.reduce(
						( memo, dataset ) => ( {
							...memo,
							[ dataset.filename ]: dataset,
						} ),
						{}
					),
				},
			};
		case 'DATASET_SET':
			if ( ! action.dataset?.filename ) {
				return state;
			}
			return {
				...state,
				datasets: {
					...state.datasets,
					[ action.dataset.filename ]: {
						...( state.datasets[ action.dataset.filename ] || {} ),
						...action.dataset,
					},
				},
			};
		case 'DATASET_UPDATE':
			if ( ! action.dataset?.filename ) {
				return state;
			}
			return {
				...state,
				datasets: {
					...state.datasets,
					[ action.dataset.filename ]: {
						...( state.datasets[ action.dataset.filename ] || {} ),
						...action.dataset,
					},
				},
			};
		case 'DATASET_DELETE':
		case 'DATASET_UNSET':
			if ( ! action.dataset?.filename ) {
				return state;
			}
			return {
				...state,
				datasets: {
					...state.datasets,
					[ action.dataset.filename ]: undefined,
				},
			};
	}

	return state;
};

const selectors = {
	/**
	 * Get array of datasets.
	 *
	 * @param {DatasetStore} state State tree.
	 * @returns {Dataset[]} Array of datasets.
	 */
	getDatasets: createSelector(
		( state ) => state.datasets,
		( datasets ) => Object.values( datasets )
			.filter( Boolean )
			.map( ( dataset ) => ( {
				...dataset,
				// Enable dataset list to be used as-is in <SelectControl>.
				value: dataset.filename,
				label: dataset.filename,
			} ) )
	),

	/**
	 * Retrieve a dataset by filename string.
	 *
	 * @param {DatasetStore} state    State tree.
	 * @param {string}       filename Filename of requested dataset.
	 * @returns {?Dataset} Dataset object, or null if not found.
	 */
	getDataset( state, filename ) {
		return state.datasets[ filename ] || null;
	},

	/**
	 * Retrieve a dataset's CSV content by filename string.
	 *
	 * @param {DatasetStore} state    State tree.
	 * @param {string}       filename Filename of requested dataset.
	 * @returns {string} Dataset CSV content string, or empty string if not found.
	 */
	getDatasetContent( state, filename ) {
		return state.datasets[ filename ]?.content || '';
	},

	/**
	 * Retrieve a dataset by its public URL.
	 *
	 * @param {DatasetStore} state State tree.
	 * @param {string}       url   Dataset public URL.
	 * @returns {?Dataset} Dataset object, or null if not found.
	 */
	getDatasetByUrl( state, url ) {
		/** @type {Dataset[]} */
		const datasets = Object.values( state.datasets );
		return datasets.find( ( dataset ) => dataset.url === url ) || null;
	},
};

const store = createReduxStore( 'csv-datasets', {
	// This line can be removed when only supporting WordPress 6.0 or later.
	__experimentalUseThunks: true,
	reducer,
	actions,
	selectors,
	controls,
	resolvers,
} );

register( store );

if ( module.hot ) {
	module.hot.accept();
}
