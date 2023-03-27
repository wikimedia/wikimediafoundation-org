import { ToggleControl } from '@wordpress/components';
import { select } from '@wordpress/data';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';

import useMeta from '../../hooks/useMeta';

/**
 * Render a toggle control to hide the page title.
 */
function TogglePageTitleControl( { label, help } ) {
	const [ hideTitle, setHideTitle ] = useMeta( 'wmf_hide_title', false );

	return (
		<ToggleControl
			label={ label }
			help={ help }
			checked={ hideTitle }
			onChange={ setHideTitle }
		/>
	);
}

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

		if ( postType !== 'page' ) {
			return null;
		}

		return  (
			<PluginPostStatusInfo>
				<TogglePageTitleControl
					label={ __( 'Hide this page title', 'shiro-admin' ) }
					help={ __( 'Use this option to avoid redundant titles, for example if the page hero contains its title.', 'shiro-admin' ) }
				/>
			</PluginPostStatusInfo>
		);
	},
};
