/**
 * Block for showing categories the current article is in.
 */

import { sortBy } from 'lodash';

/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

export const name = 'shiro/read-more-categories';

/**
 * Hook that returns the terms for the given taxonomy.
 *
 * Adapted from the `withSelect` code in WordPress core.
 *
 * @param {string} slug Slug of the taxonomy.
 */
const useTerms = slug => {
	const terms = useSelect( select => {
		const { getTaxonomy } = select( 'core' );
		const taxonomy = getTaxonomy( slug );

		const terms = taxonomy ?
			select( 'core/editor' ).getEditedPostAttribute( taxonomy.rest_base ) :
			[];

		const getEntityRecord = select( 'core' ).getEntityRecord;

		return terms.map( id => getEntityRecord( 'taxonomy', slug, id ) );
	} );

	// While the entity is being retrieved, it is undefined. This makes sure we only
	// return valid term objects.
	return terms.filter( Boolean );
};

/**
 * Render terms as links with a separator.
 */
const renderTerms = terms => {
	return sortBy( terms, 'name' )
		.map( term => ( <a key={ `${term.taxonomy}_${term.id}` } href={ term.link }>{ term.name }</a> ) )
		.reduce( ( previous, current ) => {
			if ( previous === null ) {
				return current;
			}

			return [ previous, ', ', current ];
		}, null );
};

export const settings = {
	title: __( 'Read more categories', 'shiro-admin' ),

	category: 'wikimedia',

	apiVersion: 2,

	icon: 'category',

	description: __(
		'A block with a customizable label and links to the current post\'s categories',
		'shiro-admin'
	),

	attributes: {
		readMoreText: {
			type: 'string',
			default: __( 'Read more', 'shiro-admin' ),
		},
	},

	example: {},

	/**
	 * Edit component used to show categories and tags belonging to the current
	 * article
	 */
	edit: function ReadMoreCategories( { attributes, setAttributes } ) {
		const {
			readMoreText,
		} = attributes;

		const blockProps = useBlockProps( {
			className: 'read-more-categories',
		} );

		const categories = useTerms( 'category' );
		const tags = useTerms( 'post_tag' );

		const terms = [ ...tags, ...categories ];

		return (
			<div { ...blockProps } >
				<>
					<RichText
						allowedFormats={ [] }
						className="read-more-categories__text"
						keepPlaceholderOnFocus
						placeholder={ __( 'Write read more text', 'shiro-admin' ) }
						tagName="span"
						value={ readMoreText }
						onChange={ readMoreText => setAttributes( { readMoreText } ) }
					/>
					{ /* Whitespace to mimic frontend */ }
					{ ' ' }
					<span className="read-more-categories__links">
						{ renderTerms( terms ) }
					</span>
				</>
			</div>
		);
	},

	/**
	 * Save the share article block, it's a dynamic block.
	 */
	save: function Save() {
		return null;
	},
};
