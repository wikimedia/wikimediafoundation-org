import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import './style.scss';
import CallToActionPicker from '../../components/cta';
import ImagePicker from '../../components/image-picker';

export const
	name = 'shiro/stair',
	settings = {
		apiVersion: 2,
		title: __( 'Stair', 'shiro-admin' ),
		category: 'wikimedia',
		attributes: {
			content: {
				type: 'string',
				source: 'html',
				selector: '.stair__body',
			},
			heading: {
				type: 'string',
				source: 'html',
				selector: '.stair__heading',
			},
			linkText: {
				type: 'string',
				source: 'html',
				selector: '.stair__read-more',
			},
			linkUrl: {
				type: 'string',
				source: 'attribute',
				selector: '.stair__read-more',
				attribute: 'href',
			},
			imageUrl: {
				type: 'string',
				source: 'attribute',
				selector: '.stair__image',
				attribute: 'src',
			},
			imageAlt: {
				type: 'string',
				source: 'attribute',
				selector: '.stair__image',
				attribute: 'alt',
			},
			imageId: {
				type: 'integer',
			},
			imageWidth: {
				type: 'string',
				source: 'attribute',
				selector: '.stair__image',
				attribute: 'width',
			},
			imageHeight: {
				type: 'string',
				source: 'attribute',
				selector: '.stair__image',
				attribute: 'height',
			},
		},
		parent: [ 'shiro/stairs' ],

		/**
		 * Render edit of the stair block.
		 */
		edit: function EditStairBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'stair' } );
			const {
				imageId,
				imageUrl,
				content,
				linkText,
				linkUrl,
				heading,
				imageWidth,
				imageHeight,
			} = attributes;

			const onChange = useCallback( ( { id, alt, src, width, height } ) => {
				setAttributes( {
					imageId: id,
					imageAlt: alt,
					imageUrl: src,
					imageWidth: width,
					imageHeight: height,
				} );
			}, [ setAttributes ] );

			return (
				<div { ...blockProps }>
					<RichText
						className="stair__heading is-style-h3"
						keepPlaceholderOnFocus
						placeholder={ __( 'Write heading', 'shiro-admin' ) }
						tagName="h2"
						value={ heading }
						onChange={ heading => setAttributes( { heading } ) }
					/>
					<ImagePicker
						className="stair__image"
						height={ imageHeight }
						id={ imageId }
						imageSize="image_16x9_small"
						src={ imageUrl }
						width={ imageWidth }
						onChange={ onChange }
					/>
					<RichText
						className="stair__body"
						keepPlaceholderOnFocus
						placeholder={ __( 'Start writing your stair contents', 'shiro-admin' ) }
						tagName="p"
						value={ content }
						onChange={ content => setAttributes( { content } ) }
					/>
					<CallToActionPicker
						className="stair__read-more arrow-link"
						text={ linkText }
						url={ linkUrl }
						onChangeLink={ linkUrl => setAttributes( { linkUrl } ) }
						onChangeText={ linkText => setAttributes( { linkText } ) }
					/>
				</div>
			);
		},

		/**
		 * Render save of the stair block.
		 */
		save: function SaveStairBlock( { attributes } ) {
			const blockProps = useBlockProps.save( { className: 'stair' } );
			const {
				imageUrl,
				imageAlt,
				content,
				imageId,
				linkText,
				linkUrl,
				heading,
				imageWidth,
				imageHeight,
			} = attributes;

			return (
				<div { ...blockProps }>
					<RichText.Content
						className="stair__heading is-style-h3"
						tagName="h2"
						value={ heading }
					/>
					<ImagePicker.Content
						alt={ imageAlt }
						className="stair__image"
						height={ imageHeight }
						id={ imageId }
						imageSize="image_16x9_small"
						src={ imageUrl }
						width={ imageWidth }
					/>
					<RichText.Content
						className="stair__body"
						tagName="p"
						value={ content }
					/>
					<CallToActionPicker.Content
						className="stair__read-more arrow-link"
						text={ linkText }
						url={ linkUrl }
					/>
				</div>
			);
		},
	};
