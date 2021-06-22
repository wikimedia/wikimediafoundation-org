/**
 * Block for displaying a list of profiles.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { useCallback, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import './style.scss';

const { PostControl } = hm.controls;

export const name = 'shiro/profile-list';

export const settings = {
	title: __( 'Profile List', 'shiro-admin' ),
	category: 'wikimedia',
	apiVersion: 2,
	icon: 'groups',
	description: __( 'Show a list of profile cards', 'shiro-admin' ),
	attributes: {
		profile_ids: {
			type: 'array',
			default: [],
		},
	},
	/**
	 * Edit the profile block.
	 */
	edit: function EditProfileListBlock( { attributes, setAttributes } ) {
		const { profile_ids } = attributes;
		const blockProps = useBlockProps( { className: 'profile-list-block' } );

		// Open the sidebar when we insert this block
		useEffect( () => {
			wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'edit-post/block' );
		}, [] );

		/**
		 * No profiles selected message.
		 */
		const noProfiles = () => (
			<div className="profile-list profile-list--empty">
				<div className="profile-list--empty__message">Please select profile(s) to display from the block settings in the sidebar.</div>
			</div>
		);

		const MemoizedServerSideRender = useCallback(
			() => (
				<ServerSideRender
					attributes={ { profile_ids } }
					block={ name }
					EmptyResponsePlaceholder={ noProfiles }
				/>
			),
			[ profile_ids ]
		);

		return (
			<div { ...blockProps }>
				<MemoizedServerSideRender />
				<InspectorControls>
					<PanelBody initialOpen title={ __( 'Profiles' ) }>
						<PostControl
							btnText={ __( 'Select Profiles' ) }
							label={ __(
								'Choose the profiles to be displayed.'
							) }
							postSelectProps={ {
								postType: 'profile',
								termFilters: [ 'role' ],
							} }
							value={ profile_ids }
							onChange={ profiles => {
								const profile_ids = profiles.map(
									profile => profile.id
								);
								setAttributes( { profile_ids } );
							} }
						/>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},
	/**
	 * Save no structure; this is a dynamic block.
	 */
	save: function SaveProfileListBlock( { attributes } ) {
		return null;
	},
};
