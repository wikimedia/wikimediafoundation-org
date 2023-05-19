import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Panel, PanelBody, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import deprecated from './deprecations';
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
			includeH3s: {
				type: 'boolean',
				default: false,
			},
		},

		// Support automatic transitioning from old block version.
		deprecated,

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
			const potentialHeadingsContainer = useSelect( ( select ) => {
				const parents = select( 'core/block-editor' ).getBlockParents(
					clientId
				);

				if ( parents.length > 0 ) {
					const wrappingColumns = parents
						.reverse()
						.find(
							( id ) =>
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
						setHeadingAnchors( headingBlocks ).then( ( blocks ) => {
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
					<InspectorControls>
						<Panel header={ __( 'Structure', 'shiro-admin' ) } initialOpen>
							<PanelBody>
								<ToggleControl
									label={ __( 'Third-level headings', 'shiro-admin' ) }
									help={
										attributes.includeH3s
											? __( 'Include h3 headings.', 'shiro-admin' )
											: __( 'Omit h3 headings.', 'shiro-admin' )
									}
									checked={ attributes.includeH3s }
									onChange={ () => {
										setAttributes( {
											includeH3s: ! attributes.includeH3s,
										} );
									} }
								/>
							</PanelBody>
						</Panel>
					</InspectorControls>
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
		save() {
			// Server-rendered.
			return null;
		},
	};
