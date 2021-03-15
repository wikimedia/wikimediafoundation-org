/**
 * Block for implementing the banner component.
 */

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import './style.scss';

import ImagePicker from '../../components/image-picker/index.js';

const BLOCKS_TEMPLATE = [
	[ 'core/heading', { level: 4 } ],
	[ 'core/paragraph' ],
	[ 'core/button' ],
];

/**
 * Split a single string into an array containing individual classes.
 *
 * Guaranteed to return an array.
 */
const parseClassName = className => {
	return className?.split( ' ' ) || [];
};

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
			type: 'integer',
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
	edit: function BannerEdit( props ) {
		const { attributes, setAttributes, clientId } = props;
		const blockProps = useBlockProps( { className: 'banner' } );
		const bannerClassNames = parseClassName( attributes.className );
		const usesLightButton = bannerClassNames
			.filter( x => [ 'is-style-blue-vibrant', 'is-style-red-vibrant' ].includes( x ) )
			.length > 0;
		const bannerButtonStyle = usesLightButton ? 'is-style-normal' : 'is-style-primary';

		// Change the styling on buttons to match the banner styling
		useEffect( () => {
			const buttons = wp.data.select( 'core/block-editor' ).getBlock( clientId )?.innerBlocks
				.filter( block => block.name === 'core/button' );
			if ( buttons && buttons.length > 0 ) {
				buttons.map( button => {
					const buttonStyle = parseClassName( button.attributes.className )
						.filter( className => className.indexOf( 'is-style' ) === 0 );

					// We already have the right style
					if ( bannerButtonStyle !== buttonStyle ) {
						wp.data.dispatch( 'core/block-editor' ).updateBlockAttributes( button.clientId, { className: bannerButtonStyle } );
					}

					return button;
				} );
			}
		} );

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
				<img alt={ attributes.imageAlt } className={ 'banner__image' } src={ attributes.imageSrc } />
			</div>
		);
	},
};
