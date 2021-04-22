import { useSelect, dispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Show a notice when the user is migrating to a full block editor page.
 */

/**
 * The name of this editor plugin. Required.
 */
export const name = 'page-migration-flow';

// The default template has an empty string as the template value.
const PAGE_TEMPLATE_DEFAULT = '';

export const settings = {
	/**
	 * "Render" component for this plugin.
	 *
	 * Returns nothing, just has side effects to show the user a notice.
	 */
	render: function PageMigrationFlow() {
		const { pageTemplate, blocks } = useSelect( select => {
			return {
				pageTemplate: select( 'core/editor' ).getEditedPostAttribute( 'template' ),
				blocks: select( 'core/block-editor' ).getBlocks(),
			};
		} );

		const classicBlocks = blocks.filter( block => block.name === 'core/freeform' );
		const hasNonClassicBlocks = blocks.length !== classicBlocks.length;

		useEffect( () => {
			if ( pageTemplate !== PAGE_TEMPLATE_DEFAULT && hasNonClassicBlocks ) {
				dispatch( 'core/notices' ).createNotice(
					'error',
					__( 'Inserting a non-classic block will remove any legacy pre-block-editor element from the page. Change the page template to the default template to complete conversion to the block editor.', 'shiro' ),
					{
						id: 'migration-flow-notice',
						isDismissible: false,
					}
				);
				dispatch( 'core/editor' ).lockPostSaving();
			} else {
				dispatch( 'core/notices' ).removeNotice( 'migration-flow-notice' );
				dispatch( 'core/editor' ).unlockPostSaving();
			}
		}, [ pageTemplate, hasNonClassicBlocks ] );

		return null;
	},
};
