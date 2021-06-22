/**
 * Block for displaying an individual profile.
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

export const name = 'shiro/profile';

export const settings = {
	title: __( 'Profile', 'shiro-admin' ),
	category: 'wikimedia',
	apiVersion: 2,
	icon: 'admin-users',
	description: __(
		'Show the photo, name and description of a person',
		'shiro-admin'
	),
	attributes: {
		profile_id: {
			type: 'integer',
			default: 0,
		},
	},
	/**
	 * Edit the profile block.
	 */
	edit: function EditProfileBlock( { attributes, setAttributes } ) {
		const { profile_id } = attributes;
		const blockProps = useBlockProps( { className: 'profile-block' } );

		// Open the sidebar when we insert this block
		useEffect( () => {
			wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'edit-post/block' );
		}, [] );

		/**
		 * No profile selected message
		 */
		const noProfile = () => (
			<div className="profile profile--empty">
				Please select a profile to display from the block settings in the sidebar.
			</div>
		);

		const MemoizedServerSideRender = useCallback(
			() => (
				<ServerSideRender
					attributes={ { profile_id } }
					block={ name }
					EmptyResponsePlaceholder={ noProfile }
				/>
			),
			[ profile_id ]
		);

		return (
			<div { ...blockProps }>
				<MemoizedServerSideRender />
				<InspectorControls>
					<PanelBody initialOpen title={ __( 'Individual Profile' ) }>
						<PostControl
							btnText={ __( 'Select Profile' ) }
							label={ __(
								'Choose the person to appear in the block.'
							) }
							postSelectProps={ {
								postType: 'profile',
								maxPosts: 1,
								termFilters: [ 'role' ],
							} }
							// This expects an array, so we have to store a
							// 1-item-long array
							value={ profile_id > 0 ? [ profile_id ] : [] }
							onChange={ record => {
								// This assumes we'll only ever have one item,
								// which should be guaranteed by maxPosts: 1
								const ID = record[ 0 ]?.id;
								if ( ID ) {
									setAttributes( { profile_id: ID } );
								}
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
	save: function SaveProfileBlock( { attributes } ) {
		return null;
	},
};
