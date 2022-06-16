import { InnerBlocks, useBlockProps, RichText } from '@wordpress/block-editor';
import { RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import './style.scss';

import BlockIcon from '../../../svg/blocks/mailchimp.svg';
import EmailIcon from '../../../svg/individual/email.svg';

const BLOCKS_TEMPLATE = [
	[ 'core/heading', {
		content: __( 'Get email updates', 'shiro' ),
		level: 3,
	} ],
	[ 'core/paragraph', { content: __( 'Subscribe to news about ongoing projects and initiatives', 'shiro' ) } ],
];

export const
	name = 'shiro/mailchimp-subscribe',
	settings = {
		apiVersion: 2,

		icon: BlockIcon,

		title: __( 'Mailchimp subscription form', 'shiro-admin' ),

		category: 'wikimedia',

		attributes: {
			description: {
				source: 'html',
				type: 'string',
				default: __( 'This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site’s privacy policy.', 'shiro' ),
				selector: '.mailchimp-subscribe__description',
			},
			buttonText: {
				source: 'html',
				type: 'string',
				default: __( 'Subscribe', 'shiro' ),
				selector: '.wp-block-shiro-button',
			},
			inputPlaceholder: {
				type: 'string',
				default: __( 'Email address', 'shiro' ),
			},
		},

		/**
		 * Render mailchimp subscribe for the editor
		 */
		edit: function MailChimpSubscribeEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'mailchimp-subscribe' } );
			const { description, buttonText, inputPlaceholder } = attributes;

			return (
				<>
					<div { ...blockProps }>
						<EmailIcon className="i icon icon-mail" />
						<InnerBlocks
							template={ BLOCKS_TEMPLATE }
							templateLock={ false } />
						<div className="mailchimp-subscribe__input-container">
							<div className="mailchimp-subscribe__column-input">
								<RichText
									allowedFormats={ [] }
									className="mailchimp-subscribe__input-field"
									tagName="div"
									value={ inputPlaceholder }
									onChange={ inputPlaceholder => setAttributes( { inputPlaceholder } ) }
								/>
							</div>
							<div className="mailchimp-subscribe__column-button">
								<RichText
									allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
									className="wp-block-shiro-button"
									tagName="div"
									value={ buttonText }
									onChange={ buttonText => setAttributes( { buttonText } ) }
								/>
							</div>
						</div>
						<RichText
							className="has-base-30-color has-text-color has-small-font-size"
							tagName="p"
							value={ description }
							onChange={ description => setAttributes( { description } ) }
						/>
					</div>
				</>
			);
		},

		/**
		 * Render mailchimp subscribe for the frontend
		 */
		save: function MailChimpSubscribeSave( { attributes } ) {
			const blockProps = useBlockProps.save( { className: 'mailchimp-subscribe' } );
			const { description, buttonText } = attributes;

			return (
				<div { ...blockProps }>
					<EmailIcon className="i icon icon-mail" />
					<InnerBlocks.Content />
					<div className="mailchimp-subscribe__input-container">
						<div
							className="mailchimp-subscribe__column-input"
						>
							<RawHTML>{ '<!-- input_field -->' }</RawHTML>
						</div>
						<div className="mailchimp-subscribe__column-button">
							<RichText.Content
								className="wp-block-shiro-button"
								tagName="button"
								type="submit"
								value={ buttonText }
							/>
						</div>
					</div>
					<RichText.Content
						className="mailchimp-subscribe__description has-base-30-color has-text-color has-small-font-size"
						tagName="p"
						value={ description }
					/>
				</div>
			);
		},
	};
