import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { ToggleControl, PanelRow } from '@wordpress/components';

const WikipediaPreviewPostMetaDetectLinks = ( { postMeta, setPostMeta } ) => {
	return (
		<PluginDocumentSettingPanel
			title={ __( 'Wikipedia Preview', 'wikipedia-preview' ) }
			initialOpen="false"
		>
			<PanelRow>
				<ToggleControl
					label={ __(
						'Enable Preview on Wikipedia Links',
						'wikipedia-preview'
					) }
					onChange={ ( value ) =>
						setPostMeta( { wikipediapreview_detectlinks: value } )
					}
					checked={ postMeta.wikipediapreview_detectlinks }
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
};

export default compose( [
	withSelect( ( select ) => {
		return {
			postMeta: select( 'core/editor' ).getEditedPostAttribute( 'meta' ),
			postType: select( 'core/editor' ).getCurrentPostType(),
		};
	} ),
	withDispatch( ( dispatch ) => {
		return {
			setPostMeta( newMeta ) {
				dispatch( 'core/editor' ).editPost( { meta: newMeta } );
			},
		};
	} ),
] )( WikipediaPreviewPostMetaDetectLinks );
