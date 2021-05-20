import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import { getHeadingBlocks, setHeadingAnchors } from './tocHelpers';

export const name = 'shiro/toc',
	settings = {
		apiVersion: 2,
		title: __( 'Table of Contents', 'shiro' ),
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
		attributes: {},

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

			const headingBlocks = getHeadingBlocks( topLevelBlocks );

			useEffect( () => {
				let debouncer = setTimeout( () => {
					setHeadingAnchors( headingBlocks );
				}, 1000 );
				return () => {
					clearTimeout( debouncer );
				};
			}, [ headingBlocks ] );

			return <div { ...blockProps }></div>;
		},

		/**
		 * Render save of the table of contents block.
		 */
		save: function SaveTableOfContentsBlock( { attributes } ) {
			const blockProps = useBlockProps.save( {
				className: 'table-of-contents toc',
			} );

			return <div { ...blockProps }></div>;
		},
	};
