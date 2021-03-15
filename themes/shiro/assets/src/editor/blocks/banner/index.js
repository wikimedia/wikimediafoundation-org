/**
 * Block for implementing the banner component.
 */

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import './style.scss';

import ImagePicker from '../../components/image-picker/index.js';

const BLOCKS_TEMPLATE = [
	[ 'core/heading', { level: 4 } ],
	[ 'core/paragraph' ],
	[ 'core/button' ],
];

export const name = 'shiro/banner',
	styles = [
		{
			name: 'light',
			label: __( 'Light', 'shiro' ),
			isDefault: true,
		},
		{
			name: 'gray',
			label: __( 'Gray', 'shiro' ),
		},
		{
			name: 'dark',
			label: __( 'Dark', 'shiro' ),
		},
		{
			name: 'blue-fade',
			label: __( 'Blue - Faded', 'shiro' ),
		},
		{
			name: 'blue-vibrant',
			label: __( 'Blue - Vibrant', 'shiro' ),
		},
		{
			name: 'red-fade',
			label: __( 'Red - Faded', 'shiro' ),
		},
		{
			name: 'red-vibrant',
			label: __( 'Red - Vibrant', 'shiro' ),
		},
		{
			name: 'yellow-fade',
			label: __( 'Yellow - Faded', 'shiro' ),
		},
		{
			name: 'yellow-vibrant',
			label: __( 'Yellow - Vibrant', 'shiro' ),
		},
	];

export const settings = {
	title: __( 'Banner', 'shiro' ),

	apiVersion: 2,

	icon: 'cover-image',

	description: __(
		'Banner with an image and call to action.',
		'shiro'
	),

	supports: {
		align: [ 'wide', 'full' ],
	},

	attributes: {
		imageID: {
			type: 'interger',
		},
		imageSrc: {
			type: 'string',
		},
		imageAlt: {
			type: 'string',
		},
		align: {
			type: 'string',
			default: 'wide',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function BannerEdit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'banner' } );

		return (
			<div { ...blockProps } >
				<div className="banner__content">
					<InnerBlocks
						template={ BLOCKS_TEMPLATE }
						templateLock/>
				</div>
				<ImagePicker
					className={ 'banner__image' }
					id={ attributes.imageID }
					src={ attributes.imageSrc }
					onChange={ ( { id, src, alt, sizes } ) => setAttributes( {
						imageID: id,
						imageSrc: sizes?.medium.url || src,
						imageAlt: alt,
					} ) }
				/>
			</div>
		);
	},

	/**
	 * Save the banner
	 */
	save: function BannerSave( { attributes } ) {
		const blockProps = useBlockProps.save();

		return (
			<div { ...blockProps } >
				<div className="banner__content">
					<InnerBlocks.Content/>
				</div>
				<img alt={ attributes.imageAlt } class={ 'banner__image' } src={ attributes.imageSrc } />
			</div>
		);
	},
};
