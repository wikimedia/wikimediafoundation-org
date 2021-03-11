import {
	InnerBlocks,
	RichText,
	MediaPlaceholder,
	useBlockProps,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

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
			linkUrl: {
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

		/**
		 * Render edit of the card block.
		 */
		edit: function EditCardBlock( { attributes, setAttributes, noticeUI, noticeOperations } ) {
			const blockProps = useBlockProps();
			const { id } = attributes;

			/**
			 * Save selected image to the attributes.
			 */
			const handleSelectImage = media => {
				if ( ! media || ! media.url ) {
					setAttributes( {
						imageUrl: undefined,
						imageAlt: undefined,
						id: undefined,
					} );
					return;
				}

				setAttributes( {
					id: media.id,
					imageAlt: media.alt,
					imageUrl: media.url,
				} );
			};

			/**
			 * Handle an upload error
			 */
			const handleUploadError = message => {
				noticeOperations.removeAllNotices();
				noticeOperations.createErrorNotice( message );
			};

			/**
			 * Retrieve the right image size from the media store.
			 */
			const imageUrl = useSelect( select => {
				let media = select( 'core' ).getMedia( id );
				return media?.media_details.sizes.image_16x9_small?.source_url || media?.source_url || attributes.imageUrl;
			} );

			useEffect( () => {
				if ( imageUrl !== attributes.imageUrl ) {
					setAttributes( { imageUrl } );
				}
			} );

			const mediaPreview = !! imageUrl && (
				<img
					alt={ __( 'Edit image' ) }
					className={ 'wp-block-shiro-card__image' }
					src={ imageUrl }
					title={ __( 'Edit image' ) }
				/>
			);

			return (
				<div { ...blockProps }>
					<InnerBlocks
						template={ template }
						templateLock="all"
					/>
					<div className="wp-block-shiro-card__image-preview">
						{ mediaPreview }
						<MediaPlaceholder
							accept="image/*"
							allowedTypes={ [ 'image' ] }
							disableMediaButtons={ imageUrl }
							mediaPreview={ mediaPreview }
							notices={ noticeUI }
							value={ {
								id,
								src: imageUrl,
							} }
							onError={ handleUploadError }
							onSelect={ handleSelectImage }
						/>
						<BlockControls>
							{ !! imageUrl && (
								<MediaReplaceFlow
									accept="image/*"
									allowedTypes={ [ 'image' ] }
									mediaId={ id }
									mediaURL={ imageUrl }
									name={ __( 'Replace image', 'shiro' ) }
									onError={ handleUploadError }
									onSelect={ handleSelectImage }
								/>
							) }
						</BlockControls>
					</div>
					<RichText
						className="wp-block-shiro-card__body"
						placeholder={ __( 'Start writing your card contents', 'shiro' ) }
						tagName="p"
						value={ attributes.content }
						onChange={ content => setAttributes( { content } ) }
					/>
				</div>
			);
		},

		/**
		 * Render save of the card block.
		 */
		save: function SaveCardBlock( { attributes } ) {
			const blockProps = useBlockProps.save();
			const { imageUrl } = attributes;

			const image = !! imageUrl && (
				<img
					alt={ __( 'Edit image' ) }
					className={ 'wp-block-shiro-card__image' }
					src={ imageUrl }
					title={ __( 'Edit image' ) }
				/>
			);

			return (
				<div { ...blockProps }>
					<InnerBlocks.Content />
					{ image }
					<RichText.Content
						className="wp-block-shiro-card__body"
						tagName="p"
						value={ attributes.content }
					/>
				</div>
			);
		},
	};
