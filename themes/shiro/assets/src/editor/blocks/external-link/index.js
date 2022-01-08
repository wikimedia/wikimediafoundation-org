/**
 * Block for inserting links to external content with short descriptions.
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
import './style.scss';
import ExternalLinkIcon from '../../../svg/individual/open.svg';
import URLPicker from '../../components/url-picker';

const ExternalLinkWithFocusOutside = withFocusOutside(
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
				text,
				setUrl,
				setHeading,
				setText,
			} = this.props;

			return (
				<>
					<URLPicker
						isSelected={ showButtons }
						url={ url }
						onChangeLink={ setUrl }
					/>
					<p className="external-link__heading">
						<RichText
							allowedFormats={ [ ] }
							className="external-link__link"
							placeholder={ __( 'Link heading', 'shiro-admin' ) }
							tagName="span"
							value={ heading }
							onChange={ setHeading }
							onFocus={ () => this.setState( { showButtons: true } ) }
						/>
						<ExternalLinkIcon
							className="external-link__icon" />
					</p>
					<RichText
						className="external-link__text"
						placeholder={ __( 'Enter a description of this link', 'shiro-admin' ) }
						tagName="p"
						value={ text }
						onChange={ setText }
					/>
				</>
			);
		}
	}
);

ExternalLinkWithFocusOutside.propTypes = {
	url: PropTypes.string,
	heading: PropTypes.string,
	text: PropTypes.string,
	setUrl: PropTypes.func.isRequired,
	setHeading: PropTypes.func.isRequired,
	setText: PropTypes.func.isRequired,
};

/**
 * Provide a simple content structure.
 */
ExternalLinkWithFocusOutside.Content = ( { url, heading, text } ) => {
	return (
		<>
			<p className="external-link__heading">
				<a
					className="external-link__link"
					href={ url }>
					<span className="external-link__heading-text">{ heading }</span>
					<ExternalLinkIcon
						className="external-link__icon" />
				</a>
			</p>
			<RichText.Content
				className="external-link__text"
				tagName="p"
				value={ text }
			/>
		</>
	);
};

ExternalLinkWithFocusOutside.Content.propTypes = {
	url: PropTypes.string,
	heading: PropTypes.string,
	text: PropTypes.string,
};

export const
	name = 'shiro/external-link',
	settings = {
		apiVersion: 2,
		title: __( 'External Link', 'shiro-admin' ),
		icon: 'external',
		category: 'wikimedia',
		attributes: {
			url: {
				type: 'string',
				source: 'attribute',
				selector: '.external-link__link',
				attribute: 'href',
			},
			heading: {
				type: 'string',
				source: 'html',
				selector: '.external-link__heading-text',
			},
			text: {
				type: 'string',
				source: 'html',
				selector: '.external-link__text',
			},
		},
		/**
		 * Edit the external links block content.
		 */
		edit: function EditExternalLinksBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'external-link' } );
			const {
				url,
				heading,
				text,
			} = attributes;

			return (
				<div { ...blockProps }>
					<ExternalLinkWithFocusOutside
						heading={ heading }
						setHeading={ heading => setAttributes( { heading } ) }
						setText={ text => setAttributes( { text } ) }
						setUrl={ url => setAttributes( { url } ) }
						text={ text }
						url={ url }
					/>
				</div>
			);
		},
		/**
		 * Save content for the external links block.
		 */
		save: function SaveExternalLinksBlock( { attributes } ) {
			const blockProps = useBlockProps.save( { className: 'external-link' } );
			const {
				url,
				heading,
				text,
			} = attributes;

			return (
				<div { ...blockProps }>
					<ExternalLinkWithFocusOutside.Content
						heading={ heading }
						text={ text }
						url={ url }
					/>
				</div>
			);
		},
	};
