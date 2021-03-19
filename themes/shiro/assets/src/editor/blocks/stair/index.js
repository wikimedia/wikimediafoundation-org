import {
	InnerBlocks,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import './style.scss';
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
				selector: 'p',
			},
			linkText: {
				type: 'string',
			},
			imageUrl: {
				type: 'string',
			},
			imageAlt: {
				type: 'string',
			},
			id: {
				type: 'integer',
			},
		},
		parent: [ 'shiro/stairs' ],

		/**
		 * Render edit of the stair block.
		 */
		edit: function EditStairBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps();
			const { id, imageUrl, content, linkText } = attributes;

			const onChange = useCallback( ( { id, alt, url } ) => {
				setAttributes( {
					id,
					imageAlt: alt,
					imageUrl: url,
				} );
			}, [ setAttributes ] );

			return (
				<div { ...blockProps }>
					<InnerBlocks
						template={ template }
						templateLock="all"
					/>
					<ImagePicker
						className="wp-block-shiro-stair__image"
						id={ id }
						imageSize="image_16x9_small"
						src={ imageUrl }
						onChange={ onChange }
					/>
					<RichText
						className="wp-block-shiro-stair__body"
						keepPlaceholderOnFocus
						placeholder={ __( 'Start writing your stair contents', 'shiro' ) }
						tagName="p"
						value={ content }
						onChange={ content => setAttributes( { content } ) }
					/>
					<RichText
						allowedFormats={ [ 'core/link' ] }
						className="wp-block-shiro-stair__read-more arrow-link"
						keepPlaceholderOnFocus
						placeholder={ __( 'Link to other content', 'shiro' ) }
						tagName="div"
						value={ linkText }
						onChange={ linkText => setAttributes( { linkText } ) }
					/>
				</div>
			);
		},

		/**
		 * Render save of the stair block.
		 */
		save: function SaveStairBlock( { attributes } ) {
			const blockProps = useBlockProps.save();
			const { imageUrl, imageAlt, content, linkText } = attributes;

			return (
				<div { ...blockProps }>
					<InnerBlocks.Content />
					<ImagePicker.Content
						alt={ imageAlt }
						className={ 'wp-block-shiro-stair__image' }
						src={ imageUrl }
					/>
					<RichText.Content
						className="wp-block-shiro-stair__body"
						tagName="p"
						value={ content }
					/>
					{ ! RichText.isEmpty( linkText ) && ( <RichText.Content
						className="wp-block-shiro-stair__read-more arrow-link"
						tagName="div"
						value={ linkText }
					/> ) }
				</div>
			);
		},
	};
