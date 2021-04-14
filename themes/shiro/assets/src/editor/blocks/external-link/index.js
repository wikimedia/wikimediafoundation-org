/**
 * Block for inserting links to external content with short descriptions.
 */

/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Local dependencies
 */
import './style.scss';
import SvgSprite from '../../components/svg-sprite';
import URLPicker from '../../components/url-picker';

export const
	name = 'shiro/external-link',
	settings = {
		apiVersion: 2,
		title: __( 'External Link', 'shiro' ),
		icon: 'external',
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

			const onChangeLink = useCallback( url => {
				setAttributes( {
					url,
				} );
			}, [ setAttributes ] );

			return (
				<div { ...blockProps }>
					<URLPicker
						isSelected
						url={ url }
						onChangeLink={ onChangeLink }
					/>
					<p className="external-link__heading">
						<RichText
							allowedFormats={ [ ] }
							className="external-link__link"
							placeholder={ __( 'Link heading', 'shiro' ) }
							tagName="span"
							value={ heading }
							onChange={ heading => setAttributes( { heading } ) }
						/>
						<SvgSprite
							className="external-link__icon"
							svg="open" />
					</p>
					<RichText
						className="external-link__text"
						placeholder={ __( 'Enter a description of this link', 'shiro' ) }
						tagName="p"
						value={ text }
						onChange={ text => setAttributes( { text } ) }
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
					<p className="external-link__heading">
						<a
							className="external-link__link"
							href={ url }>
							<span className="external-link__heading-text">{ heading }</span>
							<SvgSprite
								className="external-link__icon"
								svg="open" />
						</a>
					</p>
					<RichText.Content
						className="external-link__text"
						tagName="p"
						value={ text }
					/>
				</div>
			);
		},
	};
