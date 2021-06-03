/**
 * Block for displaying an individual profile.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
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
	icon: 'person',
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

		return (
			<div { ...blockProps }>
				<ServerSideRender
					attributes={ attributes }
					block={ name }
				/>
				<InspectorControls>
					<PanelBody title={ __( 'Sorting and filtering' ) }>
						<PostControl
							btnText={ __( 'Select Page' ) }
							label={ __( 'Linked Page.' ) }
							postSelectProps={ {
								postType: 'profile',
								maxPosts: 1,
								termFilters: [ 'role' ],
							} }
							// This expects an array, so we have to store a
							// 1-item-long array
							value={ [ profile_id ] }
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
