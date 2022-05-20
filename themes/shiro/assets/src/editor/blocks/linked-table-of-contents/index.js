import React from 'react';

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

export const name = 'shiro/linked-toc',
	settings = {
		apiVersion: 2,
		title: __( 'Linked Table of Contents', 'shiro-admin' ),
		category: 'wikimedia',
		icon: 'menu-alt2',
		description: __(
			'A table of contents menu for the sidebar that links to other pages.',
			'shiro-admin'
		),
		supports: {
			inserter: false,
			multiple: false,
			reusable: false,
		},

		/**
		 * Render edit of the table of contents block.
		 */
		edit: function EditTableOfContentsBlock( { attributes, setAttributes, clientId } ) {
			const blockProps = useBlockProps( {
				className: 'linked-table-of-contents table-of-contents linked-toc toc',
			} );
			const permalink = useSelect( select => select( 'core/editor' ).getPermalink() );

			return (
				<div className="toc-nav linked-toc-nav">
					<ul { ...blockProps }>
						<InnerBlocks
							allowedBlocks={ [ 'shiro/linked-toc-item' ] }
							template={ [
								[ 'shiro/linked-toc-item', {
									url: permalink,
								} ],
								[ 'shiro/linked-toc-item' ],
							] }
						/>
					</ul>
				</div>
			);
		},

		/**
		 * Render save of the table of contents block.
		 */
		save: function SaveTableOfContentsBlock( { attributes } ) {
			const blockProps = useBlockProps.save( {
				className: 'linked-table-of-contents table-of-contents linked-toc toc',
			} );

			return (
				<div className="toc-nav linked-toc-nav">
					<ul { ...blockProps }>
						<InnerBlocks.Content />
					</ul>
				</div>
			);
		},
	};
