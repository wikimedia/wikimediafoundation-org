import { useBlockProps } from '@wordpress/block-editor';
import { select, subscribe } from '@wordpress/data';
import { cleanForSlug } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';

export const name = 'shiro/toc',
	settings = {
		apiVersion: 2,
		title: __( 'Table of Contents', 'shiro' ),
		category: 'wikimedia',
		icon: 'menu-alt2',
		description: __(
			'A table of contents menu for the sidebar on list template pages.',
			'shiro'
		),
		supports: {
			inserter: true,
			multiple: false,
			reusable: false,
		},
		attributes: {},

		/**
		 * Render edit of the table of contents block.
		 */
		edit: function EditTableOfContentsBlock( {
			attributes,
			setAttributes,
		} ) {
			const blockProps = useBlockProps( {
				className: 'table-of-contents toc',
			} );

			return <div { ...blockProps }></div>;
		},

		/**
		 * Render save of the table of contents block.
		 */
		save: function SaveTableOfContentsBlock( { attributes } ) {
			const blockProps = useBlockProps.save( {
				className: 'table-of-contents toc',
			} );

			return <div { ...blockProps }></div>;
		},
	};

subscribe( () => {
	const { getBlocks } = select( 'core/block-editor' );
	const [ ...topLevelBlocks ] = getBlocks();

	// Return early if nothing exists (content not inititalized yet).
	if ( ! topLevelBlocks || topLevelBlocks.length === 0 ) {
		return;
	}

	/**
	 * Process an array of blocks to look for ToC block.
	 *
	 * This is only meant to be used inside of a column block.
	 *
	 * @param {Array} blocks Blocks to process.
	 */
	const getTableOfContentsBlock = blocks => {
		let tocBlocks = [];
		// Filter for column blocks.
		// TODO: we probably want to filter this by a style or something as well.
		blocks
			.filter( block => block.name === 'core/columns' )
			.forEach( block =>
				// Look in the first inner column.
				block.innerBlocks[ 0 ].innerBlocks
					.filter( block => block.name === name )
					.forEach( block => tocBlocks.push( block ) )
			);

		return tocBlocks;
	};

	const currentBlockInstances = getTableOfContentsBlock( topLevelBlocks );

	// Return early if we don't have this block.
	if ( currentBlockInstances.length === 0 ) {
		return;
	}

	/**
	 * Process an array of blocks to look for nested blocks.
	 *
	 * This is only meant to be used inside of a column block,
	 * to find H2s in the second column.
	 *
	 * @param {Array} blocks Blocks to process.
	 */
	const getNestedBlocks = blocks => {
		let nestedBlocks = [];
		// Filter for column blocks.
		// TODO: we probably want to filter this by a style or something as well.
		blocks
			.filter( block => block.name === 'core/columns' )
			.forEach( block =>
				// Handle second inner column.
				block.innerBlocks[ 1 ].innerBlocks.forEach( block => {
					// Add the column's inner blocks to nestedBlocks.
					nestedBlocks.push( block );
				} )
			);

		return nestedBlocks;
	};

	/**
	 * Process an array of blocks to look for heading blocks.
	 *
	 * @param {Array} blocks Blocks to process.
	 */
	const getHeadingBlocks = blocks => {
		let headingBlocks = [];
		// Filter for H2 heading blocks.
		blocks
			.filter(
				block =>
					block.name === 'core/heading' &&
					block.attributes.level === 2
			)
			.forEach( block => {
				if ( block.attributes.anchor === undefined ) {
					block.attributes.anchor = cleanForSlug(
						block.attributes.content
					);
				}
				headingBlocks.push( block );
			} );

		return headingBlocks;
	};

	const nestedBlocks = getNestedBlocks( topLevelBlocks );
	const headingBlocks = getHeadingBlocks( nestedBlocks );
	console.log( headingBlocks );
} );
