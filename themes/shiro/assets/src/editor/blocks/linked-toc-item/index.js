/**
 * Block for inserting links to external content into the linked table of contents.
 */

/**
 * Dependencies
 */
import PropTypes from 'prop-types';
import React from 'react';

/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';
import { withFocusOutside } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Local dependencies
 */
import URLPicker from '../../components/url-picker';

const LinkedTOCItemWithFocusOutside = withFocusOutside(
	class extends React.Component {
		constructor( props ) {
			super( props );
			this.state = {
				showButtons: false,
			};
		}

		handleFocusOutside() {
			this.setState( { showButtons: false } );
		}

		render() {
			const { showButtons } = this.state;
			const {
				url,
				heading,
				setUrl,
				setHeading,
			} = this.props;

			return (
				<>
					<URLPicker
						isSelected={ showButtons }
						url={ url }
						onChangeLink={ setUrl }
					/>
					<RichText
						allowedFormats={ [ ] }
						className="linked-toc__link toc__link"
						placeholder={ __( 'Link heading', 'shiro-admin' ) }
						tagName="span"
						unstableOnFocus={ () => this.setState( { showButtons: true } ) }
						value={ heading }
						onChange={ setHeading }
						onFocus={ () => this.setState( { showButtons: true } ) }
					/>
				</>
			);
		}
	}
);

LinkedTOCItemWithFocusOutside.propTypes = {
	url: PropTypes.string,
	heading: PropTypes.string,
	postId: PropTypes.string,
	setUrl: PropTypes.func.isRequired,
	setHeading: PropTypes.func.isRequired,
};

/**
 * Provide a simple content structure.
 */
LinkedTOCItemWithFocusOutside.Content = ( { url, heading, postId } ) => {
	return (
		<>
			<a
				className="linked-toc__link toc__link"
				data-post-id={ postId }
				href={ url }>
				<span className="linked-toc__heading-text">{ heading }</span>
			</a>
		</>
	);
};

LinkedTOCItemWithFocusOutside.Content.propTypes = {
	url: PropTypes.string,
	heading: PropTypes.string,
};

export const
	name = 'shiro/linked-toc-item',
	settings = {
		apiVersion: 2,
		title: __( 'Linked Table Of Contents Item', 'shiro-admin' ),
		icon: 'external',
		category: 'wikimedia',
		parent: [ 'shiro/linked-toc' ],
		supports: {
			inserter: true,
			multiple: true,
			reusable: false,
		},
		attributes: {
			url: {
				type: 'string',
				source: 'attribute',
				selector: '.linked-toc__link',
				attribute: 'href',
			},
			heading: {
				type: 'string',
				source: 'html',
				selector: '.linked-toc__heading-text',
			},
			postId: {
				type: 'string',
				source: 'attribute',
				selector: '.linked-toc__link',
				attribute: 'data-post-id',
			},
		},
		/**
		 * Edit the external links block content.
		 */
		edit: function EditLinkedTOCItemsBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'toc__item linked-toc__item' } );
			const {
				url,
				heading,
			} = attributes;

			return (
				<li { ...blockProps }>
					<LinkedTOCItemWithFocusOutside
						heading={ heading }
						setHeading={ heading => setAttributes( { heading } ) }
						setUrl={ ( url, link ) => {
							setAttributes( { url } );
							if ( link && link.id ) {
								setAttributes( { postId: link.id } );
							}
							if ( link && link.title ) {
								setAttributes( { heading: link.title } );
							}
						} }
						url={ url }
					/>
				</li>
			);
		},
		/**
		 * Save content for the external links block.
		 */
		save: function SaveLinkedTOCItemsBlock( { attributes } ) {
			const blockProps = useBlockProps.save( { className: 'toc__item linked-toc__item' } );
			const {
				url,
				heading,
				postId,
			} = attributes;

			return (
				<li { ...blockProps }>
					<LinkedTOCItemWithFocusOutside.Content
						heading={ heading }
						postId={ postId }
						url={ url }
					/>
				</li>
			);
		},
	};
