import React from 'react';

import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import './editorial-comment.scss';

/**
 * Define the editor interface for an editorial comment.
 *
 * @param {EditBlockComponentProps} props React component props.
 * @returns {React.ReactNode} Editor UI for the block.
 */
const EditEditorialComment = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( {
		className: 'simple-editorial-comment',
	} );

	return (
		<div { ...blockProps } data-note-text={ __( 'Note', 'simple-editorial-comments' ) }>
			<RichText
				className="simple-editorial-comment__comment-text"
				tagName="p"
				value={ attributes.comment }
				placeholder={ __( 'Leave an internal note about this article here...', 'simple-editorial-comments' ) }
				onChange={ ( comment ) => setAttributes( { comment } ) }
			/>
			<small>{ __( 'This comment will not render to users.', 'simple-editorial-comments' ) }</small>
		</div>
	);
};

export default EditEditorialComment;
