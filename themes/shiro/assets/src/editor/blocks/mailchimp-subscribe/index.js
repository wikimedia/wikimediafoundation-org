import { InnerBlocks, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DEFAULT_MAILCHIMP_ACTION = 'https://wikimediafoundation.us11.list-manage.com/subscribe/post?u=7e010456c3e448b30d8703345&amp;id=246cd15c56';

const BLOCKS_TEMPLATE = [
	[ 'core/heading', {
		content: __( 'Get email updates', 'shiro' ),
		level: 2,
	} ],
	[ 'core/paragraph', { content: __( 'Subscribe to news about ongoing projects and initiatives', 'kps' ) } ],
	[ 'core/columns', {}, [
		[ 'core/column', { width: 66.66 }, [
			[ 'core/paragraph', { content: __( 'Input field goes here...', 'shiro' ) } ],
		] ],
		[ 'core/column', { width: 33.33 }, [
			[ 'core/button', { text: __( 'Subscribe', 'shiro' ) } ],
		] ],
	] ],
	[ 'core/paragraph', { content: __( 'This mailing list is powered by MailChimp. The Wikimedia Foundation will handle your personal information in accordance with this site\'s privacy policy.', 'shiro' ) } ],
];

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

			/**
			 * @param {string} newAction New action to save in the attributes.
			 */
			const handleActionChange = newAction => {
				setAttributes( { action: newAction } );
			};

			return (
				<>
					<div { ...blockProps }>
						<InnerBlocks
							template={ BLOCKS_TEMPLATE }
							templateLock={ false } />
					</div>
					<InspectorControls>
						<PanelBody initialOpen title={ __( 'Mailchimp settings', 'shiro' ) }>
							<TextControl
								help={ __( 'Leave empty to use the default Mailchimp list', 'shiro' ) }
								label={ __( 'Mailchimp action URL', 'shiro' ) }
								value={ attributes.action }
								onChange={ handleActionChange }
							/>
						</PanelBody>
					</InspectorControls>
				</>
			);
		},

		/**
		 * Render mailchimp subscribe for the frontend
		 */
		save: function MailChimpSubscribeSave( { attributes } ) {
			const blockProps = useBlockProps.save();
			let { action } = attributes;

			action = action || DEFAULT_MAILCHIMP_ACTION;

			return (
				<form { ...blockProps } action={ action } method="POST">
					<InnerBlocks.Content />
					<input id="mce-group[4037]-4037-1" name="group[4037]" type="hidden" value="2" />
				</form>
			);
		},
	};
