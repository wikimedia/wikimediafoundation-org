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
import './style.scss';

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

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function BannerEdit( { attributes, setAttributes, isSelected } ) {
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

		const onChange = useCallback( ( { id, url, alt } ) => {
			setAttributes( {
				imageID: id,
				imageSrc: url,
				imageAlt: alt,
			} );
		}, [ setAttributes ] );

		return (
			<>
				<div { ...blockProps } >
					<div className="banner__content">
						<RichText
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
							className="banner__heading"
							keepPlaceholderOnFocus
							placeholder={ __( 'Heading for banner', 'shiro' ) }
							tagName="h4"
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
							setAttributes={ setAttributes }
							text={ buttonText }
							url={ url }
						/>
					</div>
					<ImagePicker
						className={ 'banner__image' }
						id={ imageID }
						src={ imageSrc }
						imageSize={ 'medium_large' }
						onChange={ onChange }
					/>
				</div>
			</>
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

		const blockProps = useBlockProps.save();

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
				<ImagePicker.Content
					alt={ imageAlt }
					className={ 'banner__image' }
					id={ imageID }
					imageSize={ 'medium_large' }
					src={ imageSrc }
				/>
			</div>
		);
	},
};
