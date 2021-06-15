/**
 * Process an array of blocks to output a list of links.
 *
 * @param {object} props Props
 * @param {Array} props.blocks Blocks to process.
 * @param {boolean} props.edit Are we in the editor?
 */
const HeadingLinks = ( { blocks, edit } ) => {
	const headingLinkList = blocks.map( ( block, i ) => (
		<li key={ i } className="toc__item">
			{ edit ? (
				<span className="toc__link">{ block.attributes.content }</span>
			) : (
				<a className="toc__link" href={ '#' + block.attributes.anchor }>
					{ block.attributes.content }
				</a>
			) }
		</li>
	) );

	return headingLinkList;
};

export default HeadingLinks;
