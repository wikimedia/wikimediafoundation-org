import {
	RichText,
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/contact.svg';
import ContactIcon from '../../../svg/individual/contact.svg';
import CallToActionPicker from '../../components/cta';

import './style.scss';

const BLOCKS_TEMPLATE = [
	[ 'core/buttons', {}, [
		[ 'core/button', {
			text: 'Facebook',
			className: 'is-style-as-link has-icon has-icon-social-facebook-blue',
			linkTarget: '_blank',
			rel: 'noreferrer noopener',
			url: 'https://www.facebook.com/wikimediafoundation/',
		} ],
		[ 'core/button', {
			text: 'Twitter',
			className: 'is-style-as-link has-icon has-icon-social-twitter-blue',
			linkTarget: '_blank',
			rel: 'noreferrer noopener',
			url: 'https://twitter.com/wikimedia',
		} ],
		[ 'core/button', {
			text: 'Instagram',
			className: 'is-style-as-link has-icon has-icon-social-instagram-blue',
			linkTarget: '_blank',
			rel: 'noreferrer noopener',
			url: 'https://www.instagram.com/wikimediafoundation/',
		} ],
		[ 'core/button', {
			text: 'LinkedIn',
			className: 'is-style-as-link has-icon has-icon-social-linkedin-blue',
			linkTarget: '_blank',
			rel: 'noreferrer noopener',
			url: 'https://www.linkedin.com/company/wikimedia-foundation',
		} ],
	] ],
];

export const name = 'shiro/contact',
	settings = {
		apiVersion: 2,
		icon: BlockIcon,
		title: __( 'Contact', 'shiro-admin' ),
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

			return ( <div { ...blockProps }>
				<ContactIcon className="contact__icon" />
				<RichText
					className="contact__title"
					keepPlaceholderOnFocus
					placeholder={ __( 'Write contact title', 'shiro-admin' ) }
					tagName="h3"
					value={ title }
					onChange={ title => setAttributes( { title } ) }
				/>
				<RichText
					className="contact__description"
					keepPlaceholderOnFocus
					placeholder={ __( 'Write contact description', 'shiro-admin' ) }
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
					placeholder={ __( 'Write social links title', 'shiro-admin' ) }
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
				<ContactIcon className="contact__icon" />
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
