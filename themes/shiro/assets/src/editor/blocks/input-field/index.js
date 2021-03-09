import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import { name as mailchimpSubscribe } from '../mailchimp-subscribe';

export const
	name = 'shiro/input-field',
	settings = {
		apiVersion: 2,
		title: __( 'Input field', 'shiro' ),
		parent: [ mailchimpSubscribe ],
		/**
		 * Render edit for an input field block
		 */
		edit: function EditInputField() {
			const blockProps = useBlockProps();

			return (
				<input
					{ ...blockProps }
					id="wmf-subscribe-input-email"
					name="EMAIL"
					placeholder={ __( 'Email address', 'shiro' ) }
					required=""
					type="email" />
			);
		},
		/**
		 * Render save for an input field block
		 */
		save: function SaveInputField() {
			const blockProps = useBlockProps.save();

			return (
				<input
					{ ...blockProps }
					id="wmf-subscribe-input-email"
					name="EMAIL"
					placeholder={ __( 'Email address', 'shiro' ) }
					required=""
					type="email" />
			);
		},
	};
