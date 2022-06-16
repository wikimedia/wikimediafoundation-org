/**
 * Block for implementing the blog-list component.
 */

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, QueryControls } from '@wordpress/components';
import { useState, useRef, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { addQueryArgs } from '@wordpress/url';

import './style.scss';

export const name = 'shiro/blog-list';

export const settings = {
	title: __( 'Blog list', 'shiro-admin' ),

	category: 'wikimedia',

	apiVersion: 2,

	icon: 'list-view',

	description: __(
		'Dynamic list of recent posts',
		'shiro-admin'
	),

	attributes: {
		postsToShow: {
			type: 'integer',
			default: 2,
		},
		categories: {
			type: 'array',
			items: {
				type: 'object',
			},
		},
		order: {
			type: 'string',
			default: 'desc',
		},
		orderBy: {
			type: 'string',
			default: 'date',
		},
		selectedAuthor: {
			type: 'number',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function BlockListEdit( { attributes, setAttributes } ) {
		const {
			postsToShow,
			categories,
			order,
			orderBy,
			selectedAuthor,
		} = attributes;

		const blockProps = useBlockProps( {
			className: 'blog-list',
		} );

		const [ categoriesList, setCategoriesList ] = useState( [] );
		const [ authorList, setAuthorList ] = useState( [] );
		const categorySuggestions = categoriesList.reduce(
			( accumulator, category ) => ( {
				...accumulator,
				[ category.name ]: category,
			} ),
			{}
		);

		/**
		 * Handle selecting a category from the list.
		 *
		 * (Copied from the core/latest-posts block.)
		 */
		const selectCategories = tokens => {
			const hasNoSuggestion = tokens.some(
				token =>
					typeof token === 'string' && ! categorySuggestions[ token ]
			);
			if ( hasNoSuggestion ) {
				return;
			}
			// Categories that are already will be objects, while new additions will be strings (the name).
			// allCategories nomalizes the array so that they are all objects.
			const allCategories = tokens.map( token => {
				return typeof token === 'string'
					? categorySuggestions[ token ]
					: token;
			} );
			// We do nothing if the category is not selected
			// from suggestions.
			if ( allCategories.includes( null ) ) {
				return false;
			}
			setAttributes( { categories: allCategories } );
		};

		const isStillMounted = useRef();

		/**
		 * Prepopulate the list of categories and users to select from.
		 *
		 * (Copied from the core/latest-posts block.)
		 */
		useEffect( () => {
			isStillMounted.current = true;

			apiFetch( {
				path: addQueryArgs( '/wp/v2/categories', { per_page: -1 } ),
			} )
				.then( data => {
					if ( isStillMounted.current ) {
						setCategoriesList( data );
					}
				} )
				.catch( () => {
					if ( isStillMounted.current ) {
						setCategoriesList( [] );
					}
				} );
			apiFetch( {
				path: addQueryArgs( '/wp/v2/users', { per_page: -1 } ),
			} )
				.then( data => {
					if ( isStillMounted.current ) {
						setAuthorList( data );
					}
				} )
				.catch( () => {
					if ( isStillMounted.current ) {
						setAuthorList( [] );
					}
				} );

			return () => {
				isStillMounted.current = false;
			};
		}, [] );

		return (
			<div { ...blockProps } >
				<InspectorControls>
					<PanelBody title={ __( 'Sorting and filtering' ) }>
						<QueryControls
							{ ...{
								order,
								orderBy,
							} }
							authorList={ authorList }
							categorySuggestions={ categorySuggestions }
							numberOfItems={ postsToShow }
							selectedAuthorId={ selectedAuthor }
							selectedCategories={ categories }
							onAuthorChange={
								value => setAttributes( {
									selectedAuthor: value !== '' ? Number( value ) : undefined,
								} )
							}
							onCategoryChange={ selectCategories }
							onNumberOfItemsChange={ postsToShow => setAttributes( { postsToShow } ) }
							onOrderByChange={ orderBy => setAttributes( { orderBy } ) }
							onOrderChange={ order => setAttributes( { order } ) }
						/>
					</PanelBody>

				</InspectorControls>
				<ServerSideRender
					attributes={ attributes }
					block={ name }
				/>
			</div>
		);
	},

	/**
	 * Save nothing, to allow for server-side rendering.
	 */
	save: function () {
		return null;
	},
};
