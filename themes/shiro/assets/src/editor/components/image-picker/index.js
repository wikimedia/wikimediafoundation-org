import {
	MediaPlaceholder,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 *
 */
export default function ImagePicker( { attributes, setAttributes, noticeUI, noticeOperations, imageSize } ) {
	const { id } = attributes;

	const size = imageSize || 'image_16x9_small';

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
		return media?.media_details.sizes[size]?.source_url || media?.source_url || attributes.imageUrl;
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
		<>
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
		</>
	);
}
