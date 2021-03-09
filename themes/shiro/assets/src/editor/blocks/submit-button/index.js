import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import { name as mailchimpSubscribe } from '../mailchimp-subscribe';

// The submit button should have the same styles as the regular button.
export { styles } from '../button';

export const
	name = 'shiro/submit-button',
	settings = {
		apiVersion: 2,
		title: __( 'Submit button', 'shiro' ),
		parent: [ mailchimpSubscribe ],
		attributes: {
			text: {
				type: 'string',
				source: 'html',
				selector: 'button',
			},
		},
		/**
		 * Render edit for an submit button block
		 */
		edit: function EditSubmitButton( { attributes, setAttributes } ) {
			const blockProps = useBlockProps();

			return (
				<RichText
					{ ...blockProps }
					tagName="button"
					value={ attributes.text }
					onChange={ text => setAttributes( { text } ) }
				/>
			);
		},
		/**
		 * Render save for an submit button block
		 */
		save: function SaveSubmitButton( { attributes } ) {
			const blockProps = useBlockProps.save();

			return (
				<RichText.Content
					{ ...blockProps }
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
					tagName="button"
					value={ attributes.text }
				/>
			);
		},
	};
