import React from 'react';

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';

import './style.scss';

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

			return (
				<div className="toc-nav linked-toc-nav">
					<div { ...blockProps }>
						<InnerBlocks
							allowedBlocks={ [ 'shiro/external-link' ] }
							template={ [
								[ 'shiro/external-link', {
									url: '#',
									heading: __( 'WIKIMEDIA IN EDUCATION', 'shiro' ),
								} ],
								[ 'shiro/external-link' ],
							] }
						/>
					</div>
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
				<div
					className="toc-nav linked-toc-nav"
				>
					<div { ...blockProps }>
						<InnerBlocks.Content />
					</div>
				</div>
			);
		},
	};
