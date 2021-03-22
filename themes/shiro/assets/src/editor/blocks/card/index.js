/**
 * Card block that makes a call to action with an image and text.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import CallToActionPicker from '../../components/cta';
import ImagePicker from '../../components/image-picker';

/**
 * Internal dependencies
 */
import './style.scss';

export const name = 'shiro/card';

export const settings = {
	apiVersion: 2,

	title: __( 'Card', 'shiro' ),

	description: __(
		'Card creates a call to action with an image, heading and paragraph.',
		'shiro'
	),

	attributes: {
		imageId: {
			type: 'number',
		},
		imageSrc: {
			type: 'string',
			source: 'attribute',
			selector: '.new-card__image',
			attribute: 'src',
		},
		imageAlt: {
			type: 'string',
			source: 'attribute',
			selector: '.new-card__image',
			attribute: 'alt',
		},
		heading: {
			type: 'string',
			source: 'html',
			selector: '.new-card__heading',
		},
		body: {
			type: 'string',
			source: 'html',
			selector: '.new-card__body',
		},
		linkText: {
			type: 'string',
			source: 'html',
			selector: '.new-card__cta',
		},
		linkUrl: {
			type: 'string',
			source: 'attribute',
			selector: '.new-card__cta',
			attribute: 'href',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function CardBlock( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'new-card' } );
		const { imageId, imageSrc, heading, body, linkText, linkUrl } = attributes;

		const onSelectImage = useCallback( ( { id, url, alt } ) => {
			setAttributes( {
				imageId: id,
				imageSrc: url,
				imageAlt: alt,
			} );
		}, [ setAttributes ] );

		return (
			<div { ...blockProps }>
				<ImagePicker
					className="new-card__image"
					id={ imageId }
					imageSize="image_16x9_small"
					src={ imageSrc }
					onChange={ onSelectImage }
				/>
				<div className="new-card__contents">
					<RichText
						className="new-card__heading is-style-h3"
						keepPlaceholderOnFocus
						placeholder={ __( 'Heading of the card', 'shiro' ) }
						tagName="h2"
						value={ heading }
						onChange={ heading => setAttributes( { heading } ) }
					/>
					<RichText
						className="new-card__body has-small-font-size"
						keepPlaceholderOnFocus
						placeholder={ __( 'Body of the card', 'shiro' ) }
						tagName="p"
						value={ body }
						onChange={ body => setAttributes( { body } ) }
					/>
					<CallToActionPicker
						className="new-card__cta arrow-link"
						text={ linkText }
						url={ linkUrl }
						onChangeLink={ linkUrl => setAttributes( { linkUrl } ) }
						onChangeText={ linkText => setAttributes( { linkText } ) }
					/>
				</div>
			</div>
		);
	},

	/**
	 * Render the frontend representation of the card block.
	 */
	save: function Save( { attributes } ) {
		const blockProps = useBlockProps.save( { className: 'new-card click-to-call-to-action' } );
		const { imageId, imageSrc, heading, body, linkText, linkUrl } = attributes;

		return (
			<div { ...blockProps }>
				<ImagePicker.Content
					className="new-card__image"
					id={ imageId }
					imageSize="image_16x9_small"
					src={ imageSrc }
				/>
				<div className="new-card__contents">
					<RichText.Content
						className="new-card__heading is-style-h3"
						tagName="h2"
						value={ heading }
					/>
					<RichText.Content
						className="new-card__body has-small-font-size"
						tagName="p"
						value={ body }
					/>
					<CallToActionPicker.Content
						className="new-card__cta call-to-action"
						text={ linkText }
						url={ linkUrl }
					/>
				</div>
			</div>
		);
	},
};
