/**
 * Handle old versions of this block.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/
 */
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import HeadingLinks from './HeadingLinks';

/**
 * Original version of ToC block, which rendered JS-side within the editor
 * and stored the rendered headings list in post content as well as in block
 * attributes. Transitioned in May 2023 to v2, where the block is entirely
 * server-rendered in PHP.
 */
const v1 = {
	supports: {
		inserter: false,
		multiple: false,
		reusable: false,
	},
	attributes: {
		headingBlocks: {
			type: 'array',
			default: [],
		},
	},
	/**
	 * Render save of the original v1 table of contents block.
	 */
	save( { attributes } ) {
		const blockProps = useBlockProps.save( {
			className: 'table-of-contents toc',
		} );

		return (
			<>
				{ attributes.headingBlocks.length > 0 && (
					<nav
						className="toc-nav"
						data-backdrop="inactive"
						data-dropdown="toc-nav"
						data-dropdown-content=".toc"
						data-dropdown-status="uninitialized"
						data-dropdown-toggle=".toc__button"
						data-sticky="false"
						data-toggleable="yes"
						data-trap="inactive"
						data-visible="false"
					>
						<h2 className="toc__title screen-reader-text">
							{ __( 'Table of Contents', 'shiro' ) }
						</h2>
						<button className="toc__button">
							<span className="btn-label-a11y">
								{ __(
									'Navigate within this page.',
									'shiro'
								) }
							</span>
							<span className="btn-label-active-item">
								{ attributes.headingBlocks[ 0 ].attributes.content.replace(
									/(<([^>]+)>)/gi,
									''
								) || __( 'Toggle menu', 'shiro' ) }
							</span>
						</button>
						<ul { ...blockProps }>
							<HeadingLinks
								blocks={ attributes.headingBlocks }
								edit={ false }
							/>
						</ul>
					</nav>
				) }
			</>
		);
	},
};

/**
 * A variant of the v1 markup where some posts are saved with aria labels
 * on the button element within the list markup.
 */
const v1WithAriaOnButton = {
	supports: {
		inserter: false,
		multiple: false,
		reusable: false,
	},
	attributes: {
		headingBlocks: {
			type: 'array',
			default: [],
		},
	},
	/**
	 * Render save of the original v1 table of contents block.
	 */
	save( { attributes } ) {
		const blockProps = useBlockProps.save( {
			className: 'table-of-contents toc',
		} );

		console.log( 'trying old save' ); // eslint-disable-line

		return (
			<>
				{ attributes.headingBlocks.length > 0 && (
					<nav
						className="toc-nav"
						data-backdrop="inactive"
						data-dropdown="toc-nav"
						data-dropdown-content=".toc"
						data-dropdown-status="uninitialized"
						data-dropdown-toggle=".toc__button"
						data-sticky="false"
						data-toggleable="yes"
						data-trap="inactive"
						data-visible="false"
					>
						<h2 className="toc__title screen-reader-text">
							{ __( 'Table of Contents', 'shiro' ) }
						</h2>
						<button
							aria-expanded="false"
							className="toc__button"
							hidden
						>
							<span className="btn-label-a11y">
								{ __(
									'Navigate within this page.',
									'shiro'
								) }
							</span>
							<span className="btn-label-active-item">
								{ attributes.headingBlocks[ 0 ].attributes.content.replace(
									/(<([^>]+)>)/gi,
									''
								) || __( 'Toggle menu', 'shiro' ) }
							</span>
						</button>
						<ul { ...blockProps }>
							<HeadingLinks
								blocks={ attributes.headingBlocks }
								edit={ false }
							/>
						</ul>
					</nav>
				) }
			</>
		);
	},
};

export default [
	v1,
	v1WithAriaOnButton,
];
