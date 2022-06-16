/**
 * Block for implementing the spotlight component.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/spotlight.svg';
import Cta from '../../components/cta/index';
import ImageFilter, { DEFAULT_IMAGE_FILTER } from '../../components/image-filter';
import ImagePicker from '../../components/image-picker/index.js';
import sharedStyles from '../../helpers/block-styles';
import './style.scss';

export const name = 'shiro/spotlight',
	styles = sharedStyles;

export const settings = {
	title: __( 'Spotlight', 'shiro-admin' ),

	category: 'wikimedia',

	apiVersion: 2,

	icon: BlockIcon,

	description: __(
		'Spotlight with an image and call to action.',
		'shiro-admin'
	),

	attributes: {
		imageID: {
			type: 'integer',
		},
		imageSrc: {
			type: 'string',
			source: 'attribute',
			selector: '.spotlight__image',
			attribute: 'src',
		},
		imageAlt: {
			type: 'string',
			source: 'attribute',
			selector: '.spotlight__image',
			attribute: 'alt',
		},
		imageFilter: {
			type: 'string',
			default: DEFAULT_IMAGE_FILTER,
		},
		heading: {
			type: 'string',
			source: 'html',
			selector: '.spotlight__heading',
		},
		text: {
			type: 'string',
			source: 'html',
			selector: '.spotlight__text',
		},
		url: {
			type: 'string',
			source: 'attribute',
			selector: '.spotlight__cta',
			attribute: 'href',
		},
		buttonText: {
			type: 'string',
			source: 'html',
			selector: '.spotlight__cta',
		},
	},

	example: {
		attributes: {
			imageID: 0,
			// This is the same image and source that the core Image block uses
			// @see https://github.com/WordPress/gutenberg/blob/trunk/packages/block-library/src/image/index.js#L32
			imageSrc: 'https://s.w.org/images/core/5.3/MtBlanc1.jpg',
			imageAlt: '',
			align: 'full',
			heading: 'Spotlight Heading',
			text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
			url: 'https://wikimediafoundation.org/',
			buttonText: 'Call to Action',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function SpotlightEdit( { attributes, setAttributes } ) {
		const {
			heading,
			text,
			buttonText,
			url,
			imageID,
			imageSrc,
			imageFilter,
		} = attributes;

		const blockProps = useBlockProps( {
			className: 'spotlight alignfull',
			'data-align': 'full',
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
				<div className="spotlight__inner">
					<div className="spotlight__content">
						<RichText
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
							className="spotlight__heading is-style-h1"
							keepPlaceholderOnFocus
							placeholder={ __( 'Heading for spotlight', 'shiro-admin' ) }
							tagName="h2"
							value={ heading }
							onChange={ heading => setAttributes( { heading } ) }
						/>
						<RichText
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
							className="spotlight__text"
							placeholder={ __( 'Enter the message for this spotlight.', 'shiro-admin' ) }
							tagName="p"
							value={ text }
							onChange={ text => setAttributes( { text } ) }
						/>
						<Cta
							className="spotlight__cta"
							text={ buttonText }
							url={ url }
							onChangeLink={ onChangeLink }
							onChangeText={ onChangeText }
						/>
					</div>
					<ImageFilter
						className="spotlight__image-wrapper"
						value={ imageFilter }
						onChange={ imageFilter => setAttributes( { imageFilter } ) }>
						<ImagePicker
							className="spotlight__image"
							id={ imageID }
							imageSize={ 'medium_large' }
							src={ imageSrc }
							onChange={ onImageChange }
						/>
					</ImageFilter>
				</div>
			</div>
		);
	},

	/**
	 * Save the spotlight
	 */
	save: function SpotlightSave( { attributes } ) {
		const {
			heading,
			text,
			buttonText,
			url,
			imageSrc,
			imageAlt,
			imageID,
			imageFilter,
		} = attributes;

		const blockProps = useBlockProps.save( {
			className: 'spotlight alignfull',
		} );

		return (
			<div { ...blockProps } >
				<div className="spotlight__inner">
					<div className="spotlight__content">
						<RichText.Content
							className="spotlight__heading is-style-h1"
							tagName="h2"
							value={ heading }
						/>
						<RichText.Content
							className="spotlight__text"
							tagName="p"
							value={ text }
						/>
						<Cta.Content
							className="spotlight__cta"
							text={ buttonText }
							url={ url }
						/>
					</div>
					<ImageFilter.Content
						className="spotlight__image-wrapper"
						value={ imageFilter }>
						<ImagePicker.Content
							alt={ imageAlt }
							className="spotlight__image"
							id={ imageID }
							imageSize={ 'medium_large' }
							src={ imageSrc }
						/>
					</ImageFilter.Content>
				</div>
			</div>
		);
	},
};
