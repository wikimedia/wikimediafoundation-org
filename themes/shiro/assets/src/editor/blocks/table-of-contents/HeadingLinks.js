import { __ } from '@wordpress/i18n';

/**
 * Process an array of blocks to output a list of links.
 *
 * @param {object} props Props
 * @param {Array} props.blocks Blocks to process.
 * @param {boolean} props.edit Are we in the editor?
 */
const HeadingLinks = ( { blocks, edit } ) => {
	const headingLinkList = blocks.map( ( block, i ) => {
		const textContent = block.attributes.content.replace(
			/(<([^>]+)>)/gi,
			''
		);
		return (
			<li key={ i } className="toc__item">
				{ edit ? (
					<span className="toc__link">
						{ textContent.length > 0
							? textContent
							: __( 'H2 with no text content.', 'shiro-admin' ) }
					</span>
				) : (
					<a
						className="toc__link"
						href={ '#' + block.attributes.anchor }
					>
						{ textContent }
					</a>
				) }
			</li>
		);
	} );

	return headingLinkList;
};

export default HeadingLinks;
