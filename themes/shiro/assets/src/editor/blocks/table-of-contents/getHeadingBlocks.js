import { cleanForSlug } from '@wordpress/editor';

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
	const nestedBlocks = getNestedBlocks( blocks );
	let headingBlocks = [];

	// Filter for H2 heading blocks.
	nestedBlocks
		.filter(
			block =>
				block.name === 'core/heading' && block.attributes.level === 2
		)
		.forEach( block => {
			if (
				block.attributes.anchor === undefined ||
				block.attributes.anchor === ''
			) {
				// TODO: debounce this with useEffect
				block.attributes.anchor = cleanForSlug(
					block.attributes.content
				);
			}
			headingBlocks.push( block );
		} );

	return headingBlocks;
};

export default getHeadingBlocks;
