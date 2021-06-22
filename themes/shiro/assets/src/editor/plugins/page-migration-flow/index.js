import { useSelect, dispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Show a notice when the user is migrating accidentally to a full block editor page.
 */

/**
 * The name of this editor plugin. Required.
 */
export const name = 'page-migration-flow';

// The default template has an empty string as the template value.
const PAGE_TEMPLATE_DEFAULT = '';
const NOTICE_NAME = 'migration-flow-notice';

const LOCK_NAME = 'page-migration-flow-lock';
let isLocked = false;

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
					__( 'Proceed with caution: converting to the block editor will remove any legacy elements populated by custom fields. To complete the conversion to the block editor, change the page template to the default template and create blocks for any legacy content that you\'d like to migrate. Not ready to convert this page? Delete any blocks that aren\'t "Classic blocks" and make your changes within the existing classic block.', 'shiro-admin' ),
					{
						id: NOTICE_NAME,
						isDismissible: false,
					}
				);
				dispatch( 'core/editor' ).lockPostSaving( LOCK_NAME );
				isLocked = true;
			} else if ( isLocked ) {
				dispatch( 'core/notices' ).removeNotice( NOTICE_NAME );
				dispatch( 'core/editor' ).unlockPostSaving( LOCK_NAME );
				isLocked = false;
			}
		}, [ pageTemplate, hasNonClassicBlocks ] );

		return null;
	},
};
