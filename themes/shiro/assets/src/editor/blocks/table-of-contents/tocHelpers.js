/**
 * Replace spaces with dashes, for HTML ids.
 *
 * @param {string} string Text that needs to made into an id.
 * @returns {string} The new id for use.
 */
const makeId = string => string.replace( /\s/g, '-' ).toLocaleLowerCase();

/**
 * Process an array of blocks to look for heading blocks.
 *
 * @param {Array} blocks Blocks to process.
 */
export const getHeadingBlocks = blocks => {
	// Save some work
	if ( blocks.length < 1 ) {
		return [];
	}

	const headingBlocks = blocks
		.filter( block => block.name === 'core/column' )
		// Columns with a ToC can't contain valid headers for anchoring
		.filter( block => {
			if ( ! block.innerBlocks ) {
				return false;
			}

			return ! block.innerBlocks.find(
				block => block.name === 'shiro/toc'
			);
		} )
		.map( block => block.innerBlocks || [] )
		.flat()
		// Filter for H2 heading blocks.
		.filter(
			block =>
				block.name === 'core/heading' && block.attributes.level === 2
		);

	return headingBlocks;
};

/**
 * Process an array of blocks to set anchor attributes.
 *
 * @param {Array} blocks Blocks to process.
 */
export const setHeadingAnchors = async blocks => {
	blocks.forEach( ( block, index ) => {
		const originalContent =
			block.originalContent !== undefined
				? block.originalContent.replace( /(<([^>]+)>)/gi, '' )
				: undefined;
		const updatedContent = block.attributes.content.replace(
			/(<([^>]+)>)/gi,
			''
		);
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
			headingAnchor.includes( makeId( originalContent ) ) ||
			headingAnchor.includes( makeId( updatedContent ) ) ||
			( previousContent !== undefined &&
				headingAnchor.includes( makeId( previousContent ) ) )
		) {
			// Create a code to prepend (to try to ensure we always have a unique id).
			const indexPrepender = 'a' + ( index + 1 ) + '-';

			// Add a new attribute to store updated content that hasn't been saved.
			block.attributes.previousContent = updatedContent;

			// Generate an anchor for the h2.
			block.attributes.anchor = indexPrepender + makeId( updatedContent );
		}
	} );

	return blocks;
};
