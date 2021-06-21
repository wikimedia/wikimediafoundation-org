import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import HeadingLinks from './HeadingLinks';
import { getHeadingBlocks, setHeadingAnchors } from './tocHelpers';

import './style.scss';

export const name = 'shiro/toc',
	settings = {
		apiVersion: 2,
		title: __( 'Table of Contents', 'shiro-admin' ),
		category: 'wikimedia',
		icon: 'menu-alt2',
		description: __(
			'A table of contents menu for the sidebar on list template pages.',
			'shiro-admin'
		),
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
		 * Render edit of the table of contents block.
		 */
		edit: function EditTableOfContentsBlock( {
			attributes,
			setAttributes,
			clientId,
		} ) {
			const blockProps = useBlockProps( {
				className: 'table-of-contents toc',
			} );

			/**
			 * This will look for the `core/columns` block containing this ToC
			 * and its adjacent content. It does this by climbing up the block
			 * tree from where it finds itself, so you may get odd results
			 * if it isn't inside of a `core/columns` block.
			 */
			const potentialHeadingsContainer = useSelect( select => {
				const parents = select( 'core/block-editor' ).getBlockParents(
					clientId
				);

				if ( parents.length > 0 ) {
					const wrappingColumns = parents
						.reverse()
						.find(
							id =>
								select( 'core/block-editor' ).getBlockName(
									id
								) === 'core/columns'
						);

					if ( wrappingColumns ) {
						const { innerBlocks } = select(
							'core/block-editor'
						).getBlock( wrappingColumns );
						return innerBlocks;
					}
				}

				// Send back a sensible "fail" value.
				return [];
			} );

			// Only update things when there's a change.
			useEffect( () => {
				let debouncer = setTimeout( () => {
					const headingBlocks = getHeadingBlocks(
						potentialHeadingsContainer
					);
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
			}, [ potentialHeadingsContainer, attributes, setAttributes ] );

			return (
				<>
					{ attributes.headingBlocks.length > 0 ? (
						<nav className="toc-nav">
							<ul { ...blockProps }>
								<HeadingLinks
									blocks={ attributes.headingBlocks }
									edit
								/>
							</ul>
						</nav>
					) : (
						<p { ...blockProps }>
							{ __(
								'Links will appear here when you\'ve added some h2 blocks in the other column.',
								'shiro-admin'
							) }
						</p>
					) }
				</>
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
