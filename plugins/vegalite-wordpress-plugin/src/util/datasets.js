import apiFetch from '@wordpress/api-fetch';
import { select } from '@wordpress/data';

import asyncThrottle from './throttle';

/**
 * Get the datasets route base for the active post being edited.
 *
 * @returns {string} Route string: /wp/v2/{type}/{id}/datasets/.
 */
const getPostDatasetsRoute = () => {
	const postId = select( 'core/editor' ).getCurrentPostId();
	const postType = select( 'core/editor' ).getCurrentPostType();
	if ( ! postId || ! postType ) {
		return '';
	}

	const postTypeObject = select( 'core' ).getEntity( 'postType', postType );
	if ( ! postTypeObject ) {
		return '';
	}

	return [ postTypeObject.baseURL, postId, 'datasets' ].join( '/' );
};

/**
 * Get a list of available datasets for the active post..
 *
 * @returns {Promise<Dataset[]>} Promise resolving to array of available datasets.
 */
export const getDatasets = () => apiFetch( {
	path: getPostDatasetsRoute(),
} );

/**
 * Query the current post for a specific dataset by dataset filename.
 *
 * @param {string} filename Filename of dataset to load.
 * @returns {Promise<Dataset>} Promise resolving to dataset JSON object.
 */
export const getDataset = ( filename ) => apiFetch( {
	// TODO: Get the collection slug for the relevant post type by using the post object.
	path: `${ getPostDatasetsRoute() }/${ filename }?format=json`,
} );

/**
 * Query for any dataset by URL and return as JSON.
 *
 * @param {string} url URL of remove CSV dataset.
 * @returns {Promise<Dataset>} JSON representation of the dataset.
 */
export const getDatasetByUrl = ( url ) => {
	const requestUrl = new URL( url );
	requestUrl.searchParams.set( 'format', 'json' );
	return fetch( requestUrl ).then( ( result ) => result.json() );
};

/**
 * Create a dataset in the API.
 *
 * @param {Dataset} dataset Dataset object.
 * @returns {Promise<Dataset>} Promise resolving to the created dataset object.
 */
export const createDataset = ( { filename, content = '' } ) => apiFetch( {
	path: getPostDatasetsRoute(),
	method: 'POST',
	data: {
		filename,
		content,
	},
} );

/**
 * Update a dataset in the API.
 *
 * @param {Dataset} dataset Dataset object.
 * @returns {Promise<Dataset>} Promise resolving to the updated dataset object.
 */
export const updateDataset = ( { filename, content } ) => apiFetch( {
	// TODO: Get the collection slug for the relevant post type by using the post object.
	path: `${ getPostDatasetsRoute() }/${ filename }`,
	method: 'POST',
	data: {
		filename,
		content,
	},
} );

/**
 * Delete a dataset in the API.
 *
 * @param {Dataset} dataset Dataset object.
 * @returns {Promise<boolean>} Promise resolving to whether dataset got deleted.
 */
export const deleteDataset = ( { filename } ) => apiFetch( {
	// TODO: Get the collection slug for the relevant post type by using the post object.
	path: `${ getPostDatasetsRoute() }/${ filename }`,
	method: 'DELETE',
} );

// Create debounced versions of all public methods.
[
	getDatasets,
	getDataset,
	createDataset,
	updateDataset,
	deleteDataset,
].forEach( ( method ) => {
	method.throttled = asyncThrottle( method, 200 );
} );

if ( module.hot ) {
	module.hot.accept();
}
