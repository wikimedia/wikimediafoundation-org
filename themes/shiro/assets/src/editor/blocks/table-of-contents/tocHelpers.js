import { cleanForSlug } from '@wordpress/editor';
import { escapeHTML } from '@wordpress/escape-html';

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
export const getHeadingBlocks = blocks => {
	const nestedBlocks = getNestedBlocks( blocks );
	let headingBlocks = [];

	// Filter for H2 heading blocks.
	nestedBlocks
		.filter(
			block =>
				block.name === 'core/heading' && block.attributes.level === 2
		)
		.forEach( block => {
			headingBlocks.push( block );
		} );

	return headingBlocks;
};

/**
 * Process an array of blocks to set anchor attributes.
 *
 * @param {Array} blocks Blocks to process.
 */
export const setHeadingAnchors = blocks => {
	blocks.forEach( ( block, index ) => {
		const originalContent =
			block.originalContent !== undefined
				? block.originalContent.replace( /(<([^>]+)>)/gi, '' )
				: undefined;
		const updatedContent = block.attributes.content;
		const previousContent = block.attributes.previousContent;
		const headingAnchor = block.attributes.anchor;

		// Get the index that was defined in the anchor.
		const anchorIndex =
			headingAnchor !== undefined && headingAnchor.charAt( 0 ) === 'a'
				? headingAnchor.split( '-' )[ 0 ].replace( 'a', '' )
				: undefined;

		// Only process the ones that have changed.
		if (
			index + 1 === parseInt( anchorIndex ) &&
			( updatedContent === previousContent ||
				( previousContent === undefined &&
					updatedContent === originalContent ) )
		) {
			return;
		}

		// We only want to update anchors that are empty or auto-generated.
		if (
			headingAnchor === undefined ||
			headingAnchor === '' ||
			headingAnchor.includes( cleanForSlug( originalContent ) ) ||
			headingAnchor.includes( cleanForSlug( updatedContent ) ) ||
			headingAnchor.includes( cleanForSlug( previousContent ) )
		) {
			// Create a code to prepend (to try to ensure we always have a unique id).
			const indexPrepender = 'a' + ( index + 1 ) + '-';

			// Add a new attribute to store updated content that hasn't been saved.
			block.attributes.previousContent = updatedContent;

			// Generate an anchor for the h2.
			block.attributes.anchor =
				indexPrepender + cleanForSlug( updatedContent );
		}
	} );
};
