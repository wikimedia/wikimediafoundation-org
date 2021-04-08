/**
 * Block for inserting links to translated content.
 */

/**
 * WordPress dependencies
 */
import {
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

export const
	name = 'shiro/inline-languages',
	settings = {
		apiVersion: 2,
		title: __( 'Inline Languages', 'shiro' ),
		icon: 'translation',
		supports: {
			align: [ 'center', 'full' ],
		},
		/**
		 *
		 */
		edit: function EditInlineLanguagesBlock( { attributes } ) {
			const blockProps = useBlockProps();

			return (
				<div { ...blockProps }>
					<ServerSideRender
						attributes={ attributes }
						block={ name }
					/>
				</div>
			);
		},
		/**
		 * Save nothing, to allow for server-size rendering.
		 *
		 * @returns {null}
		 */
		save: function () {
			return null;
		},
	};
