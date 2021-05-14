/**
 * Block for showing categories the current article is in.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { ToggleControl, PanelBody } from '@wordpress/components';
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
	return terms
		.map( term => ( <a href={ term.link }>{ term.name }</a> ) )
		.reduce( ( previous, current ) => {
			if ( previous === null ) {
				return current;
			}

			return [ previous, ', ', current ];
		}, null );
};

export const settings = {
	title: __( 'Read more categories', 'shiro' ),

	category: 'wikimedia',

	apiVersion: 2,

	icon: 'category',

	description: __(
		'A block that shows a text and the categories of the current article',
		'shiro'
	),

	attributes: {
		readMoreText: {
			type: 'string',
			default: __( 'Read more', 'shiro' ),
		},
	},

	example: {},

	/**
	 * Edit component used to manage featured image and page intro.
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
						placeholder={ __( 'Write read more text', 'shiro' ) }
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

	/*
	<div class="read-more-categories">
		<span class="read-more-categories__text">
			<?php echo esc_html( $attributes['readMoreText'] ?? '' ); ?>
		</span>
		<span class="read-more-categories__links">
			<?php $i = 0; ?>
			<?php foreach ( $terms as $term_id => $term_title ) : ?>
				<?php
					$term_link = get_term_link( $term_id );
					if ( is_wp_error( $term_link ) ) {
						continue;
					}

					$is_last = ++$i === count( $terms );
				?>
				<a href="<?php echo esc_attr( $term_link ); ?>"><?php echo esc_html( $term_title ); ?></a><?php
					if ( ! $is_last ) {
						echo ',';
					}
				?>
			<?php endforeach; ?>
		</span>
	</div>
	 */

	/**
	 * Save the share article block, it's a dynamic block.
	 */
	save: function Save() {
		return null;
	},
};
