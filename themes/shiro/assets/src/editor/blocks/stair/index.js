import {
	InnerBlocks,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import './style.scss';
import CallToActionPicker from '../../components/cta';
import ImagePicker from '../../components/image-picker';

const template = [
	[ 'core/heading', { level: 3 } ],
];

export const
	name = 'shiro/stair',
	settings = {
		apiVersion: 2,
		title: __( 'Stair', 'shiro' ),
		attributes: {
			content: {
				type: 'string',
				source: 'html',
				selector: '.stair__body',
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
		},
		parent: [ 'shiro/stairs' ],

		/**
		 * Render edit of the stair block.
		 */
		edit: function EditStairBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'stair' } );
			const { imageId, imageUrl, content, linkText, linkUrl } = attributes;

			const onChange = useCallback( ( { id, alt, src } ) => {
				setAttributes( {
					imageId: id,
					imageAlt: alt,
					imageUrl: src,
				} );
			}, [ setAttributes ] );

			return (
				<div { ...blockProps }>
					<InnerBlocks
						template={ template }
						templateLock="all"
					/>
					<ImagePicker
						className="stair__image"
						id={ imageId }
						imageSize="image_16x9_small"
						src={ imageUrl }
						onChange={ onChange }
					/>
					<RichText
						className="stair__body"
						keepPlaceholderOnFocus
						placeholder={ __( 'Start writing your stair contents', 'shiro' ) }
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
			const { imageUrl, imageAlt, content, imageId, linkText, linkUrl } = attributes;

			return (
				<div { ...blockProps }>
					<InnerBlocks.Content />
					<ImagePicker.Content
						alt={ imageAlt }
						className={ 'stair__image' }
						id={ imageId }
						src={ imageUrl }
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
