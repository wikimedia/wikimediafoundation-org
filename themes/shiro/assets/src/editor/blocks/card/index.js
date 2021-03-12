import {
	InnerBlocks,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import './style.scss';
import ImagePicker from '../../components/image-picker';

const template = [
	[ 'core/heading', { level: 3 } ],
];

export const
	name = 'shiro/card',
	settings = {
		apiVersion: 2,
		title: __( 'Card', 'shiro' ),
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
		parent: [ 'shiro/cards' ],

		/**
		 * Render edit of the card block.
		 */
		edit: function EditCardBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps();
			const { content, linkText } = attributes;

			return (
				<div { ...blockProps }>
					<InnerBlocks
						template={ template }
						templateLock="all"
					/>
					<ImagePicker
						attributes={ attributes }
						defaultSize="image_16x9_small"
						setAttributes={ setAttributes }
					/>
					<RichText
						className="wp-block-shiro-card__body"
						placeholder={ __( 'Start writing your card contents', 'shiro' ) }
						tagName="p"
						value={ content }
						onChange={ content => setAttributes( { content } ) }
					/>
					<RichText
						allowedFormats={ [ 'core/link' ] }
						className="wp-block-shiro-card__read-more arrow-link"
						placeholder={ __( 'Link to other content', 'shiro' ) }
						tagName="div"
						value={ linkText }
						onChange={ linkText => setAttributes( { linkText } ) }
					/>
				</div>
			);
		},

		/**
		 * Render save of the card block.
		 */
		save: function SaveCardBlock( { attributes } ) {
			const blockProps = useBlockProps.save();
			const { imageUrl, imageAlt, content, linkText } = attributes;

			const image = !! imageUrl && (
				<img
					alt={ imageAlt }
					className={ 'wp-block-shiro-card__image' }
					src={ imageUrl }
				/>
			);

			return (
				<div { ...blockProps }>
					<InnerBlocks.Content />
					{ image }
					<RichText.Content
						className="wp-block-shiro-card__body"
						tagName="p"
						value={ content }
					/>
					{ ! RichText.isEmpty( linkText ) && ( <RichText.Content
						className="wp-block-shiro-card__read-more arrow-link"
						tagName="div"
						value={ linkText }
					/> ) }
				</div>
			);
		},
	};
