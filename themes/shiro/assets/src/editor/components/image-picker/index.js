import classNames from 'classnames';
import PropTypes from 'prop-types';

import {
	MediaPlaceholder,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { withNotices } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { useImageSize } from '../../hooks/media';

import './style.scss';

/**
 * Render an editor image picker component to allow the user to select an image.
 *
 * @param {object}   props React props.
 * @param {number}   props.id Attachment ID of image.
 * @param {string}   props.className Class name to render on preview.
 * @param {string}   props.imageSize The size the image picker should save.
 * @param {string}   props.src Image source URL.
 * @param {Function} props.onChange Function that is called when a user selects
 *                   an image in the media library or removes the image.
 */
function ImagePicker( props ) {
	const {
		// Props passed into the component.
		id,
		className,
		imageSize,
		onChange,
		// For customizing MediaPlaceholder's labels attributes
		labels,
		// Props provided by withNotices HOC.
		noticeUI,
		noticeOperations,
		width,
		height,
	} = props;

	let { src } = props;

	/**
	 * Handle an upload error
	 */
	const onUploadError = message => {
		noticeOperations.removeAllNotices();
		noticeOperations.createErrorNotice( message );
	};

	/*
	 * This combination makes sure that we:
	 * 1. Use the right image size using `useImageSize`
	 * 2. Show an image on page load using the passed `src`.
	 */
	const { src: imageSizeSrc } = useImageSize( id, imageSize, onChange );
	src = imageSizeSrc || src;

	/**
	 * Handle a newly-selected media attachment.
	 */
	const onSelect = media => {
		noticeOperations.removeAllNotices();

		// If the selection is cleared, unset attributes and return early.
		if ( ! media || ! media.url ) {
			onChange( {
				id: undefined,
				src: undefined,
				alt: undefined,
				media: undefined,
			} );
		} else {
			const { id, alt, url, sizes, width, height } = media;

			// Call the onChange now with the uploaded image object.
			onChange( {
				id,
				src: sizes?.[ imageSize ]?.url || url,
				width: sizes?.[ imageSize ]?.width || width,
				height: sizes?.[ imageSize ]?.height || height,
				alt,
				media,
			} );
		}
	};

	const mediaPreview = src && (
		<img
			alt={ __( 'Edit image' ) }
			className={ className }
			height={ height }
			src={ src }
			title={ __( 'Edit image' ) }
			width={ width }
		/>
	);

	return (
		<>
			{ mediaPreview }
			<MediaPlaceholder
				accept="image/*"
				allowedTypes={ [ 'image' ] }
				className="image-picker__placeholder"
				disableMediaButtons={ !! src }
				labels={ labels }
				notices={ noticeUI }
				value={ {
					id,
					src,
				} }
				onError={ onUploadError }
				onSelect={ onSelect }
			/>
			<BlockControls>
				{ src && (
					<MediaReplaceFlow
						accept="image/*"
						allowedTypes={ [ 'image' ] }
						mediaId={ id }
						mediaURL={ src }
						name={ __( 'Replace image', 'shiro-admin' ) }
						onError={ onUploadError }
						onSelect={ onSelect }
					/>
				) }
			</BlockControls>
		</>
	);
}

ImagePicker.propTypes = {
	id: PropTypes.number,
	className: PropTypes.string,
	imageSize: PropTypes.string,
	src: PropTypes.string,
	labels: PropTypes.object,
	onChange: PropTypes.func.isRequired,
	width: PropTypes.oneOfType( [ PropTypes.string, PropTypes.number ] ),
	height: PropTypes.oneOfType( [ PropTypes.string, PropTypes.number ] ),

	noticeOperations: PropTypes.object.isRequired,
	noticeUI: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.node ] ),
};

const ImagePickerWithNotices = withNotices( ImagePicker );

/**
 * Render image that has been picked for a block save function.
 */
ImagePickerWithNotices.Content = ( { id, imageSize, src, alt, className, ...props } ) => {
	if ( ! src ) {
		return null;
	}

	return (
		<img
			alt={ alt }
			className={
				classNames(
					{ [ `wp-image-${ id }` ]: id },
					{ [ `size-${ imageSize }` ]: imageSize },
					className
				)
			}
			src={ src }
			{ ...props }
		/>
	);
};

ImagePickerWithNotices.Content.propTypes = {
	alt: PropTypes.string,
	id: PropTypes.number,
	imageSize: PropTypes.string,
	src: PropTypes.string,
};

export default ImagePickerWithNotices;
