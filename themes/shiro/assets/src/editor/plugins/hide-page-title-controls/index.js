import { ToggleControl } from '@wordpress/components';
import { select } from '@wordpress/data';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';

import useMeta from '../../hooks/useMeta';

/**
 * Adds a toggle control to hide the page title, in the post status box.
 */

/**
 * The name of this editor plugin. Required.
 */
export const name = 'hide-page-title-control';

export const settings = {
	/**
	 * "Render" component for this plugin.
	 *
	 * Renders a toggle control in the post status sidebar panel to hide the page title.
	 */
	render: function Render() {
		const postType = select( 'core/editor' ).getCurrentPostType();

		const [ hideTitle, setHideTitle ] = useMeta( '_wmf_hide_title', false );

		if ( postType !== 'page' ) {
			return;
		}

		return  (
			<PluginPostStatusInfo>
				<ToggleControl
					label={ __( 'Hide this page title', 'shiro-admin' ) }
					help={ __( 'Use this option to avoid redundant titles, for example if the page hero contains its title.', 'shiro-admin' ) }
					checked={ hideTitle }
					onChange={ setHideTitle }
				/>
			</PluginPostStatusInfo>
		);
	},
};
