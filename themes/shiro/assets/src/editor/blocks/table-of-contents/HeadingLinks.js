/**
 * Process an array of blocks to output a list of links.
 *
 * @param {object} props Props
 * @param {Array} props.blocks Blocks to process.
 */
const HeadingLinks = ( { blocks } ) => {
	const headingLinkList = blocks.map( ( block, i ) => (
		<li key={ i } className="toc__item">
			<a href={ '#' + block.attributes.anchor }>
				{ block.attributes.content }
			</a>
		</li>
	) );

	return headingLinkList;
};

export default HeadingLinks;
