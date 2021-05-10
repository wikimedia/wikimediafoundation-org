/* global shiroEditorVariables */

import {
	RichText,
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import { RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import CallToActionPicker from '../../components/cta';

import './style.scss';

const iconUrl = shiroEditorVariables.themeUrl + '/assets/dist/icons.svg#contact';

const BLOCKS_TEMPLATE = [
	[ 'core/buttons', {}, [
		[ 'core/button', {
			text: 'Facebook',
			className: 'is-style-as-link has-icon has-icon-social-facebook',
			linkTarget: '_blank',
			rel: 'noreferrer noopener',
			url: 'https://facebook.com',
		} ],
		[ 'core/button', {
			text: 'Twitter',
			className: 'is-style-as-link has-icon has-icon-social-twitter-blue',
		} ],
		[ 'core/button', {
			text: 'Instagram',
			className: 'is-style-as-link has-icon has-icon-social-instagram',
		} ],
		[ 'core/button', {
			text: 'LinkedIn',
			className: 'is-style-as-link has-icon has-icon-social-linkedin',
		} ],
	] ],
];

export const name = 'shiro/contact',
	settings = {
		apiVersion: 2,
		// icon: '',
		title: __( 'Contact', 'shiro' ),
		category: 'wikimedia',
		attributes: {
			title: {
				type: 'string',
				source: 'html',
				selector: '.contact__title',
				default: __( 'Contact a human', 'shiro' ),
			},
			description: {
				type: 'string',
				source: 'html',
				selector: '.contact__description',
				default: __( 'Questions about the Wikimedia Foundation or our projects? Get in touch with our team.', 'shiro' ),
			},
			linkText: {
				type: 'string',
				source: 'attribute',
				selector: '.contact__call-to-action',
				default: __( 'Contact', 'shiro' ),
			},
			linkUrl: {
				type: 'string',
				source: 'attribute',
				selector: '.contact__call-to-action',
				attribute: 'href',
			},
			socialTitle: {
				type: 'string',
				source: 'html',
				selector: '.contact__social-title',
				default: __( 'Follow', 'shiro' ),
			},
		},

		/**
		 * Render edit of the contact block
		 */
		edit: function ContactBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'contact' } );
			const {
				title,
				description,
				linkUrl,
				linkText,
				socialTitle,
			} = attributes;

			/*
			<RichText
											keepPlaceholderOnFocus
											placeholder={ __( 'Write secondary heading', 'shiro' ) }
											tagName="span"
											value={ siteLanguageHeading.text }
											onChange={ partial( setHeadingAttribute, 'text', siteLanguageIndex ) }
											onFocus={ () => setActiveHeading( null ) }
										/>
			 */
			return ( <div { ...blockProps }>
				<svg className="i icon icon-mail">
					<use xlinkHref={ iconUrl } />
				</svg>
				<RichText
					className="contact__title"
					keepPlaceholderOnFocus
					placeholder={ __( 'Write contact title', 'shiro' ) }
					tagName="h3"
					value={ title }
					onChange={ title => setAttributes( { title } ) }
				/>
				<RichText
					className="contact__description"
					keepPlaceholderOnFocus
					placeholder={ __( 'Write contact description', 'shiro' ) }
					tagName="div"
					value={ description }
					onChange={ description => setAttributes( { description } ) }
				/>
				<CallToActionPicker
					className="contact__call-to-action"
					text={ linkText }
					url={ linkUrl }
					onChangeLink={ linkUrl => setAttributes( { linkUrl } ) }
					onChangeText={ linkText => setAttributes( { linkText } ) }
				/>
				<RichText
					className="contact__social-title"
					keepPlaceholderOnFocus
					placeholder={ __( 'Write social links title', 'shiro' ) }
					tagName="h4"
					value={ socialTitle }
					onChange={ socialTitle => setAttributes( { socialTitle } ) }
				/>
				<InnerBlocks
					allowedBlocks={ [ 'core/buttons' ] }
					template={ BLOCKS_TEMPLATE }
				/>
			</div> );
		},

		/**
		 * Render the save of the contact block
		 */
		save: function Save( { attributes, setAttributes } ) {
			const blockProps = useBlockProps.save( { className: 'contact' } );
			const {
				title,
				description,
				linkUrl,
				linkText,
				socialTitle,
			} = attributes;

			return ( <div { ...blockProps }>
				<svg className="i icon icon-mail">
					<RawHTML>
						{ '<use xlink:href="' + iconUrl + '" />' }
					</RawHTML>
				</svg>
				<RichText.Content
					className="contact__title"
					tagName="h3"
					value={ title }
				/>
				<RichText.Content
					className="contact__description"
					tagName="div"
					value={ description }
				/>
				<CallToActionPicker.Content
					className="contact__call-to-action"
					text={ linkText }
					url={ linkUrl }
				/>
				<RichText.Content
					className="contact__social-title"
					tagName="h4"
					value={ socialTitle }
				/>
				<InnerBlocks.Content
				/>
			</div> );
		},
	};
