/* global shiroEditorVariables */

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { RawHTML } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const BLOCKS_TEMPLATE = [
	[ 'core/heading', {
		content: __( 'Get email updates', 'shiro' ),
		level: 2,
	} ],
	[ 'core/paragraph', { content: __( 'Subscribe to news about ongoing projects and initiatives', 'kps' ) } ],
	[ 'core/columns', {}, [
		[ 'core/column', { width: 66.66 }, [
			[ 'shiro/input-field' ],
		] ],
		[ 'core/column', { width: 33.33 }, [
			[ 'shiro/submit-button', { text: __( 'Subscribe', 'shiro' ) } ],
		] ],
	] ],
	[ 'core/paragraph', {
		content: __( 'This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site\'s privacy policy.', 'shiro' ),
		textColor: 'base30',
		fontSize: 'small',
	} ],
];

const iconUrl = shiroEditorVariables.themeUrl + '/assets/dist/icons.svg#email';

export const
	name = 'shiro/mailchimp-subscribe',
	settings = {
		apiVersion: 2,

		title: __( 'Mailchimp subscription form', 'shiro' ),

		attributes: {
			action: {
				type: 'string',
			},
		},

		/**
		 * Render mailchimp subscribe for the editor
		 */
		edit: function MailChimpSubscribeEdit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps();

			return (
				<>
					<div { ...blockProps }>
						<svg className="i icon icon-mail">
							<use xlinkHref={ iconUrl } />
						</svg>
						<InnerBlocks
							template={ BLOCKS_TEMPLATE }
							templateLock={ false } />
					</div>
				</>
			);
		},

		/**
		 * Render mailchimp subscribe for the frontend
		 */
		save: function MailChimpSubscribeSave() {
			const blockProps = useBlockProps.save();

			return (
				<div { ...blockProps }>
					<svg className="i icon icon-mail">
						<RawHTML>
							{ '<use xlink:href="' + iconUrl + '" />' }
						</RawHTML>
					</svg>
					<RawHTML>{ '<!-- form_start -->' }</RawHTML>
					<InnerBlocks.Content />
					<RawHTML>{ '<!-- additional_fields -->' }</RawHTML>
					<RawHTML>{ '<!-- form_end -->' }</RawHTML>
				</div>
			);
		},
	};
