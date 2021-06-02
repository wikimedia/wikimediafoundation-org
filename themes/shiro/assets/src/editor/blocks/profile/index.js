/**
 * Block for displaying an individual profile.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

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
		profileId: {
			type: 'integer',
			default: 0,
		},
	},
	/**
	 * Edit the profile block.
	 */
	edit: function EditProfileBlock( { attributes, setAttributes } ) {
		const { profileId } = attributes;
		const blockProps = useBlockProps( { className: 'profile' } );

		return (
			<div { ...blockProps }>
				{ profileId }
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
							value={ [ profileId ] }
							onChange={ record => {
								// This assumes we'll only ever have one item
								const ID = record[ 0 ]?.id;
								if ( ID ) {
									setAttributes( { profileId: ID } );
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
