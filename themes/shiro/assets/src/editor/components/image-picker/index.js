import PropTypes from 'prop-types';

import {
	MediaPlaceholder,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { withNotices } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Return the id of the image being picked (or undefined).
 */
const defaultGetId = ( { attributes } ) => {
	return attributes.id;
};

/**
 * Return the method used to update the block when an image is selected.
 */
const defaultMakeOnSelectImage = ( { setAttributes } ) => {
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
 * Return the method used to generate the URL for the image.
 */
const defaultMakeGetImageUrl = ( id, { attributes }, defaultSize = 'medium_large' ) => {
	return select => {
		const media = select( 'core' ).getMedia( id );

		const defaultSizeObject = media?.media_details.sizes[ defaultSize ];

		return defaultSizeObject?.source_url || media?.source_url || attributes.imageUrl;
	};
};

/**
 * Return the method used to make sure the image URL is correctly set.
 */
const defaultMakeUpdateImageUrl = ( imageUrl, { attributes, setAttributes } ) => {
	return () => {
		if ( imageUrl !== attributes.imageUrl ) {
			setAttributes( { imageUrl } );
		}
	};
};

/**
 * Return an element containing the image preview (or undefined, if no image).
 */
const defaultRenderPreview = imageUrl => {
	return !! imageUrl && (
		<img
			alt={ __( 'Edit image' ) }
			className={ 'wp-block-shiro__image-preview' }
			src={ imageUrl }
			title={ __( 'Edit image' ) }
		/>
	);
};

/**
 * Render an editor image picker component to allow the user to select an image.
 *
 * By default the image data is saved into `id`, `imageUrl` and `imageAlt`
 * respectively. This can be changed by overriding the factory functions
 * that make the functions that do this.
 *
 * @param {object}   props React props.
 * @param {string}   props.defaultSize The size the image picker should save.
 *                   This will only be used when the default `makeGetImageUrl`
 *                   is used.
 * @param {Function} props.getId Function to retrieve the image id from the
 *                   attributes.
 * @param {Function} props.makeOnSelectImage Function that is called when a user
 *                   selects an image in the media library. Should set the image
 *                   attributes that saves the image.
 * @param {Function} props.makeGetImageUrl Function that should return a
 *                   selector which returns the image URL based on id and
 *                   attributes.
 * @param {Function} props.makeUpdateImageUrl Function that should return an
 *                   effect that will update the imageUrl attribute. Is used to
 *                   make sure the imageUrl is up to date if getImageUrl is
 *                   async.
 * @param {Function} props.renderPreview Render an image preview to the user. Is
 *                   passed the image URL returned by makeGetImageUrl
 */
function ImagePicker( props ) {
	const {
		noticeUI,
		noticeOperations,
	} = props;

	const getId = props.getId || defaultGetId;
	const makeOnSelectImage = props.makeOnSelectImage || defaultMakeOnSelectImage;
	const makeGetImageUrl = props.makeGetImageUrl || defaultMakeGetImageUrl;
	const makeUpdateImageUrl = props.makeUpdateImageUrl || defaultMakeUpdateImageUrl;
	const renderPreview = props.renderPreview || defaultRenderPreview;

	const id = getId( props );
	const onSelectImage = makeOnSelectImage( props );
	const getImageUrl = makeGetImageUrl( id, props, props.defaultSize );
	/**
	 * Retrieve the right image size from the media store.
	 */
	const imageUrl = useSelect( getImageUrl );
	const updateImageUrl = makeUpdateImageUrl( imageUrl, props );
	useEffect( updateImageUrl );
	const mediaPreview = renderPreview( imageUrl );

	/**
	 * Handle an upload error
	 */
	const onUploadError = message => {
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
				onError={ onUploadError }
				onSelect={ onSelectImage }
			/>
			<BlockControls>
				{ !! imageUrl && (
					<MediaReplaceFlow
						accept="image/*"
						allowedTypes={ [ 'image' ] }
						mediaId={ id }
						mediaURL={ imageUrl }
						name={ __( 'Replace image', 'shiro' ) }
						onError={ onUploadError }
						onSelect={ onSelectImage }
					/>
				) }
			</BlockControls>
		</>
	);
}

ImagePicker.propTypes = {
	attributes: PropTypes.object.isRequired,
	setAttributes: PropTypes.func.isRequired,
	noticeOperations: PropTypes.object.isRequired,
	noticeUI: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.node ] ),

	defaultSize: PropTypes.string,

	getId: PropTypes.func,
	makeOnSelectImage: PropTypes.func,
	makeGetImageUrl: PropTypes.func,
	makeUpdateImageUrl: PropTypes.func,
	renderPreview: PropTypes.func,
};

export default withNotices( ImagePicker );
