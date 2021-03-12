import {
	MediaPlaceholder,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Returns the id of the image being picked (or undefined).
 */
const defaultGetId = ( { attributes } ) => {
	return attributes.id;
};

/**
 * Returns the method used to update the block when an image is selected.
 */
const defaultMakeHandleSelectImage = ( { setAttributes } ) => {
	return media => {
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
};

/**
 * Returns the method used to generate the URL for the image.
 */
const defaultMakeGetImageUrl = ( id, { attributes } ) => {
	return select => {
		let media = select( 'core' ).getMedia( id );
		return media?.media_details.sizes.medium_large?.source_url || media?.source_url || attributes.imageUrl;
	};
};

/**
 * Returns the method used to make sure the image URL is correctly set.
 */
const defaultMakeUpdateImageUrl = ( imageUrl, { attributes, setAttributes } ) => {
	return () => {
		if ( imageUrl !== attributes.imageUrl ) {
			setAttributes( { imageUrl } );
		}
	};
};

/**
 * Returns an element containing the image preview (or undefined, if no image).
 */
const defaultGetPreview = imageUrl => {
	return !! imageUrl && (
		<img
			alt={ __( 'Edit image' ) }
			className={ 'wp-block-shiro-card__image' }
			src={ imageUrl }
			title={ __( 'Edit image' ) }
		/>
	);
};

/**
 *
 */
export default function ImagePicker( props ) {
	const { noticeUI, noticeOperations, getId, makeHandleSelectImage, makeGetImageUrl, makeUpdateImageUrl, getPreview } = props;

	const finallyGetId = getId || defaultGetId;
	const finallyMakeHandleSelectImage = makeHandleSelectImage || defaultMakeHandleSelectImage;
	const finallyMakeGetImageUrl = makeGetImageUrl || defaultMakeGetImageUrl;
	const finallyMakeUpdateImageUrl = makeUpdateImageUrl || defaultMakeUpdateImageUrl;
	const finallyGetPreview = getPreview || defaultGetPreview;

	const id = finallyGetId( props );
	const handleSelectImage = finallyMakeHandleSelectImage( props );
	const getImageUrl = finallyMakeGetImageUrl( id, props );
	/**
	 * Retrieve the right image size from the media store.
	 */
	const imageUrl = useSelect( getImageUrl );
	const updateImageUrl = finallyMakeUpdateImageUrl( imageUrl, props );
	useEffect( updateImageUrl );
	const mediaPreview = finallyGetPreview( imageUrl );

	/**
	 * Handle an upload error
	 */
	const handleUploadError = message => {
		noticeOperations.removeAllNotices();
		noticeOperations.createErrorNotice( message );
	};

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
