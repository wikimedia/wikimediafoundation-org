import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import HeadingLinks from './HeadingLinks';
import { getHeadingBlocks, setHeadingAnchors } from './tocHelpers';

export const name = 'shiro/toc',
	settings = {
		apiVersion: 2,
		title: __( 'Table of Contents', 'shiro-admin' ),
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
		attributes: {
			headingBlocks: {
				type: 'array',
				default: [],
			},
		},

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

			const [ ...topLevelBlocks ] = useSelect( select =>
				select( 'core/block-editor' ).getBlocks()
			);

			// Only update things when there's a change.
			useEffect( () => {
				let debouncer = setTimeout( () => {
					const headingBlocks = getHeadingBlocks( topLevelBlocks );
					const headingBlocksFromAttributes =
						attributes.headingBlocks;

					if (
						JSON.stringify( headingBlocks ) !==
						JSON.stringify( headingBlocksFromAttributes )
					) {
						setHeadingAnchors( headingBlocks ).then( blocks => {
							setAttributes( { headingBlocks: blocks } );
						} );
					}
				}, 1000 );
				return () => {
					clearTimeout( debouncer );
				};
			}, [ topLevelBlocks, attributes, setAttributes ] );

			return (
				<nav
					className="toc-nav"
					data-backdrop="inactive"
					data-dropdown="toc-nav"
					data-dropdown-content=".toc"
					data-dropdown-status="uninitialized"
					data-dropdown-toggle=".toc__button"
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
							{ __( 'Navigate within this page.', 'shiro' ) }
						</span>
						<span className="btn-label-active-item">
							{ attributes.headingBlocks[ 0 ].attributes.content }
						</span>
					</button>
					<ul { ...blockProps }>
						<HeadingLinks blocks={ attributes.headingBlocks } />
					</ul>
				</nav>
			);
		},

		/**
		 * Render save of the table of contents block.
		 */
		save: function SaveTableOfContentsBlock( { attributes } ) {
			const blockProps = useBlockProps.save( {
				className: 'table-of-contents toc',
			} );

			return (
				<nav
					className="toc-nav"
					data-backdrop="inactive"
					data-dropdown="toc-nav"
					data-dropdown-content=".toc"
					data-dropdown-status="uninitialized"
					data-dropdown-toggle=".toc__button"
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
							{ __( 'Navigate within this page.', 'shiro' ) }
						</span>
						<span className="btn-label-active-item">
							{ attributes.headingBlocks[ 0 ].attributes.content }
						</span>
					</button>
					<ul { ...blockProps }>
						<HeadingLinks blocks={ attributes.headingBlocks } />
					</ul>
				</nav>
			);
		},
	};
