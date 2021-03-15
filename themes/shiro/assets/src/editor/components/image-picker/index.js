import PropTypes from 'prop-types';

import {
	MediaPlaceholder,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { withNotices } from '@wordpress/components';
import { select } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Helper function for updating block attributes on selecting or removing a media attachment.
 *
 * @param {Function} setAttributes Parent block's `setAttributes` function.
 * @returns {Function} Function which can be used as onChange classback.
 */
export const onChange = setAttributes =>
	( { id, src, alt } ) => setAttributes( {
		id,
		src,
		alt,
	} );

/**
 * Render an editor image picker component to allow the user to select an image.
 *
 * @param {object}   props React props.
 * @param {number}   props.id Attachment ID of image.
 * @param {string}   props.className Class name to render on preview.
 * @param {string}   props.defaultSize The size the image picker should save.
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
		src,
		onChange,
		// Props provided by withNotices HOC.
		noticeUI,
		noticeOperations,
	} = props;

	/**
	 * Handle an upload error
	 */
	const onUploadError = message => {
		noticeOperations.removeAllNotices();
		noticeOperations.createErrorNotice( message );
	};

	// Query the API to get the correct URL for the image size.
	useEffect( () => {
		const media = select( 'core' ).getMedia( id );
		const cropSize = media?.media_details.sizes[ imageSize ]?.source_url || media?.source_url;

		if ( cropSize ) {
			onChange( {
				id,
				alt: media.alt,
				url: cropSize,
				media,
			} );
		}
	}, [ id, imageSize, onChange ] );

	/**
	 * Handle a newly-selected media attachment.
	 */
	const onSelect = function Select( media ) {
		noticeOperations.removeAllNotices();

		// If the selection is cleared, return early.
		if ( ! media || ! media.url ) {
			onChange( {
				id: undefined,
				src: undefined,
				alt: undefined,
				media: undefined,
			} );
		} else {
			const { id, alt, url, sizes } = media;

			// Call the update function now with the uploaded image object.
			onChange( {
				id,
				alt,
				url: sizes?.[ imageSize ]?.url || url,
				media,
			} );
		}

	};

	const mediaPreview = src && (
		<img
			alt={ __( 'Edit image' ) }
			className={ className }
			src={ src }
			title={ __( 'Edit image' ) }
		/>
	);

	return (
		<>
			{ mediaPreview }
			<MediaPlaceholder
				accept="image/*"
				allowedTypes={ [ 'image' ] }
				disableMediaButtons={ !! src }
				mediaPreview={ mediaPreview }
				notices={ noticeUI }
				value={ {
					id,
					src,
				} }
				onError={ onUploadError }
				onSelect={ onSelect }
			/>
			<BlockControls>
				{ !! src && (
					<MediaReplaceFlow
						accept="image/*"
						allowedTypes={ [ 'image' ] }
						mediaId={ id }
						mediaURL={ src }
						name={ __( 'Replace image', 'shiro' ) }
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
	defaultSize: PropTypes.string,
	src: PropTypes.string,
	onChange: PropTypes.func.isRequired,

	noticeOperations: PropTypes.object.isRequired,
	noticeUI: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.node ] ),
};

export default withNotices( ImagePicker );
