import { PanelRow, ToggleControl } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

// Check if site is the main site in the network.
const wmfIsMainSite = shiroEditorVariables.wmfIsMainSite; // eslint-disable-line no-undef

/**
 * Fetch the post meta and pass it to our component as props.
 */
const mapSelectToProps = select => {
	return {
		metaInProgress: select( 'core/editor' )
			.getEditedPostAttribute( 'meta' )
			._translation_in_progress,
		metaComplete: select( 'core/editor' )
			.getEditedPostAttribute( 'meta' )
			._translation_complete,
	};
};

/**
 * Update the component props with the post meta value.
 */
const mapDispatchToProps = dispatch => {
	return {
		/**
		 * Translation in progress post meta.
		 */
		setmetaInProgress: value => {
			dispatch( 'core/editor' ).editPost( {
				meta: {
					_translation_in_progress: value,
				},
			} );
		},
		/**
		 * Translation complete post meta.
		 */
		setmetaComplete: value => {
			dispatch( 'core/editor' ).editPost( {
				meta: {
					_translation_complete: value,
				},
			} );
		},
	};
};

/**
 * Define settings panel controls.
 */
const MetaBlockField = props => {
	return (
		<PanelRow>
			<div>
				<ToggleControl
					checked={ props.metaComplete }
					label={ __( 'Translation completed', 'shiro-admin' ) }
					onChange={ value => {
						if ( value && props.metaInProgress ) {
							props.setmetaInProgress( 0 );
						} else {
							props.setmetaInProgress( 1 );
						}

						props.setmetaComplete( value );
					} }
				/>
			</div>
		</PanelRow>
	);
};

const MetaBlockFieldWithData = withSelect( mapSelectToProps )( MetaBlockField );
const MetaBlockFieldWithDataAndActions = withDispatch( mapDispatchToProps )( MetaBlockFieldWithData );

registerPlugin( 'shiro-translation-status', {
	/**
	 * Register our custom sidebar plugin.
	 */
	render() {
		if ( ! wmfIsMainSite ) {
			return (
				<PluginDocumentSettingPanel
					className="shiro-translation-status-panel"
					icon="translation"
					name="shiro-translation-status-panel"
					title={ __( 'Translation Status', 'shiro-admin' ) }
				>
					<div className="shiro-translation-status-content">
						<MetaBlockFieldWithDataAndActions />
					</div>
				</PluginDocumentSettingPanel>
			);
		} else {
			return null;
		}
	},
} );
