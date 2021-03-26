/**
 * Block for implementing the banner component.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import Cta from '../../components/cta/index';
import ImagePicker from '../../components/image-picker/index.js';
import sharedStyles from '../../helpers/block-styles';
import './style.scss';

export const name = 'shiro/banner',
	styles = sharedStyles;

export const settings = {
	title: __( 'Banner', 'shiro' ),

	category: 'wikimedia',

	apiVersion: 2,

	icon: 'cover-image',

	description: __(
		'Banner with an image and call to action.',
		'shiro'
	),

	attributes: {
		imageID: {
			type: 'integer',
		},
		imageSrc: {
			type: 'string',
			source: 'attribute',
			selector: '.banner__image',
			attribute: 'src',
		},
		imageAlt: {
			type: 'string',
			source: 'attribute',
			selector: '.banner__image',
			attribute: 'alt',
		},
		align: {
			type: 'string',
			default: 'wide',
		},
		heading: {
			type: 'string',
			source: 'html',
			selector: '.banner__heading',
		},
		text: {
			type: 'string',
			source: 'html',
			selector: '.banner__text',
		},
		url: {
			type: 'string',
			source: 'attribute',
			selector: '.banner__cta',
			attribute: 'href',
		},
		buttonText: {
			type: 'string',
			source: 'html',
			selector: '.banner__cta',
		},
	},

	example: {
		attributes: {
			imageID: 0,
			// This is the same image and source that the core Image block uses
			// @see https://github.com/WordPress/gutenberg/blob/trunk/packages/block-library/src/image/index.js#L32
			imageSrc: 'https://s.w.org/images/core/5.3/MtBlanc1.jpg',
			imageAlt: '',
			align: 'wide',
			heading: 'Banner Heading',
			text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			url: 'https://wikimediafoundation.org/',
			buttonText: 'Call to Action',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function BannerEdit( { attributes, setAttributes } ) {
		const {
			heading,
			text,
			buttonText,
			url,
			imageID,
			imageSrc,
		} = attributes;

		const blockProps = useBlockProps( {
			className: 'banner',
		} );

		const onImageChange = useCallback( ( { id, src, alt } ) => {
			setAttributes( {
				imageID: id,
				imageSrc: src,
				imageAlt: alt,
			} );
		}, [ setAttributes ] );

		const onChangeLink = useCallback( url => {
			setAttributes( {
				url,
			} );
		}, [ setAttributes ] );

		const onChangeText = useCallback( text => {
			setAttributes( {
				buttonText: text,
			} );
		}, [ setAttributes ] );

		return (
			<div { ...blockProps } >
				<div className="banner__content">
					<RichText
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
						className="banner__heading is-style-h4"
						keepPlaceholderOnFocus
						placeholder={ __( 'Heading for banner', 'shiro' ) }
						tagName="h2"
						value={ heading }
						onChange={ heading => setAttributes( { heading } ) }
					/>
					<RichText
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
						className="banner__text"
						placeholder={ __( 'Enter the message for this banner.', 'shiro' ) }
						tagName="p"
						value={ text }
						onChange={ text => setAttributes( { text } ) }
					/>
					<Cta
						className="banner__cta"
						text={ buttonText }
						url={ url }
						onChangeLink={ onChangeLink }
						onChangeText={ onChangeText }
					/>
				</div>
				<figure
					className="banner__image-wrapper" >
					<ImagePicker
						className="banner__image"
						id={ imageID }
						imageSize={ 'medium_large' }
						src={ imageSrc }
						onChange={ onImageChange }
					/>
				</figure>
			</div>
		);
	},

	/**
	 * Save the banner
	 */
	save: function BannerSave( { attributes } ) {
		const {
			heading,
			text,
			buttonText,
			url,
			imageSrc,
			imageAlt,
			imageID,
		} = attributes;

		const blockProps = useBlockProps.save( {
			className: 'banner',
		} );

		return (
			<div { ...blockProps } >
				<div className="banner__content">
					<RichText.Content
						className="banner__heading"
						tagName="h4"
						value={ heading }
					/>
					<RichText.Content
						className="banner__text"
						tagName="p"
						value={ text }
					/>
					<Cta.Content
						className="banner__cta"
						text={ buttonText }
						url={ url }
					/>
				</div>
				<figure
					className="banner__image-wrapper" >
					<ImagePicker.Content
						alt={ imageAlt }
						className="banner__image"
						id={ imageID }
						imageSize={ 'medium_large' }
						src={ imageSrc }
					/>
				</figure>
			</div>
		);
	},
};
