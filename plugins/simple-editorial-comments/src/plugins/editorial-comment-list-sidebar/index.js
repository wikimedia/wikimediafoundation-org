import React from 'react';

import { Panel, PanelBody, PanelRow } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { PluginSidebar } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';

import { name as editorialCommentBlock } from '../../blocks/editorial-comment';

export const name = 'simple-editorial-comments-list-sidebar';

/**
 * Reducer function to recursively select all comment blocks.
 *
 * @param {object[]} memo  Reducer carry object, a list of matching blocks.
 * @param {object}   block The block being checked, may have inner blocks.
 * @returns {object[]} Comment blocks.
 */
const findNestedCommentBlocks = ( memo, block ) => {
	if ( block.name === editorialCommentBlock ) {
		memo.push( block );
		return memo;
	}
	if ( Array.isArray( block.innerBlocks ) && block.innerBlocks.length ) {
		return block.innerBlocks.reduce( findNestedCommentBlocks, memo );
	}
	return memo;
};

/**
 * Render the comment list sidebar.
 *
 * @returns {React.ReactNode} Block editor sidebar React element.
 */
const EditorialCommentListSidebar = () => {
	const { selectBlock } = useDispatch( 'core/editor' );
	const comments = useSelect( ( select ) => {
		return select( 'core/editor' ).getBlocks().reduce( findNestedCommentBlocks, [] );
	} );

	return (
		<PluginSidebar
			name={ name }
			icon="edit-large"
			title={ __( 'Simple Editorial Comments', 'simple-editorial-comments' ) }
		>
			<div className="plugin-sidebar-content">
				<Panel>
					<PanelBody title={ __( 'Comment List', 'simple-editorial-comments' ) }>
						{ comments.map( ( commentBlock ) => {
							const message = ( commentBlock.attributes.comment || '' ).replace( /<[^>]+>/g, '' );
							const buttonText = message.length > 25
								? `${ message.substr( 0, 25 ) }...`
								: message;
							return (
								<PanelRow key={ `comment-link-${ commentBlock.clientId }` }>
									{ buttonText || __( '(no content)', 'simple-editorial-comments' ) }
									<button onClick={ () => selectBlock( commentBlock.clientId ) }>
										{ __( 'Select', 'simple-editorial-comments' ) }
									</button>
								</PanelRow>
							);
						} ) }
					</PanelBody>
				</Panel>
			</div>
		</PluginSidebar>
	);
};

export const settings = {
	render: EditorialCommentListSidebar,
};
