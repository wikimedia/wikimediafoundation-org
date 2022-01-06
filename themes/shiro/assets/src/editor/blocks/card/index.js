/**
 * Card block that makes a call to action with an image and text.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/card.svg';
import CallToActionPicker from '../../components/cta';
import ImagePicker from '../../components/image-picker';

/**
 * Internal dependencies
 */
import './style.scss';

export const name = 'shiro/card';

export const settings = {
	apiVersion: 2,

	icon: BlockIcon,

	title: __( 'Card', 'shiro-admin' ),

	category: 'wikimedia',

	description: __(
		'Card creates a call to action with an image, heading and paragraph.',
		'shiro-admin'
	),

	attributes: {
		imageId: {
			type: 'number',
		},
		imageSrc: {
			type: 'string',
			source: 'attribute',
			selector: '.content-card__image',
			attribute: 'src',
		},
		imageAlt: {
			type: 'string',
			source: 'attribute',
			selector: '.content-card__image',
			attribute: 'alt',
		},
		imageWidth: {
			type: 'string',
			source: 'attribute',
			selector: '.content-card__image',
			attribute: 'width',
		},
		imageHeight: {
			type: 'string',
			source: 'attribute',
			selector: '.content-card__image',
			attribute: 'height',
		},
		heading: {
			type: 'string',
			source: 'html',
			selector: '.content-card__heading',
		},
		body: {
			type: 'string',
			source: 'html',
			selector: '.content-card__body',
		},
		linkText: {
			type: 'string',
			source: 'html',
			selector: '.content-card__call-to-action',
		},
		linkUrl: {
			type: 'string',
			source: 'attribute',
			selector: '.content-card__call-to-action',
			attribute: 'href',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function CardBlock( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'content-card' } );
		const {
			imageId,
			imageSrc,
			heading,
			body,
			linkText,
			linkUrl,
			imageWidth,
			imageHeight,
		} = attributes;

		const onSelectImage = useCallback( ( { id, src, alt, width, height } ) => {
			setAttributes( {
				imageId: id,
				imageSrc: src,
				imageAlt: alt,
				imageWidth: width,
				imageHeight: height,
			} );
		}, [ setAttributes ] );

		return (
			<div { ...blockProps }>
				<div className="content-card__contents">
					<RichText
						className="content-card__heading is-style-h3"
						keepPlaceholderOnFocus
						placeholder={ __( 'Heading of the card', 'shiro-admin' ) }
						tagName="h2"
						value={ heading }
						onChange={ heading => setAttributes( { heading } ) }
					/>
					<RichText
						className="content-card__body has-small-font-size"
						keepPlaceholderOnFocus
						placeholder={ __( 'Body of the card', 'shiro-admin' ) }
						tagName="p"
						value={ body }
						onChange={ body => setAttributes( { body } ) }
					/>
					<CallToActionPicker
						className="content-card__call-to-action arrow-link"
						text={ linkText }
						url={ linkUrl }
						onChangeLink={ linkUrl => setAttributes( { linkUrl } ) }
						onChangeText={ linkText => setAttributes( { linkText } ) }
					/>
				</div>
				<ImagePicker
					className="content-card__image"
					height={ imageHeight }
					id={ imageId }
					imageSize="image_16x9_small"
					src={ imageSrc }
					width={ imageWidth }
					onChange={ onSelectImage }
				/>
			</div>
		);
	},

	/**
	 * Render the frontend representation of the card block.
	 */
	save: function Save( { attributes } ) {
		const blockProps = useBlockProps.save( { className: 'content-card click-to-call-to-action' } );
		const {
			imageId,
			imageSrc,
			heading,
			body,
			linkText,
			linkUrl,
			imageWidth,
			imageHeight,
		} = attributes;

		return (
			<div { ...blockProps }>
				<div className="content-card__contents">
					<RichText.Content
						className="content-card__heading is-style-h3"
						tagName="h2"
						value={ heading }
					/>
					<RichText.Content
						className="content-card__body has-small-font-size"
						tagName="p"
						value={ body }
					/>
					<CallToActionPicker.Content
						className="content-card__call-to-action call-to-action"
						text={ linkText }
						url={ linkUrl }
					/>
				</div>
				<ImagePicker.Content
					className="content-card__image"
					height={ imageHeight }
					id={ imageId }
					imageSize="image_16x9_small"
					src={ imageSrc }
					width={ imageWidth }
				/>
			</div>
		);
	},
};
