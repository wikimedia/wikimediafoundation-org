import PropTypes from 'prop-types';

import {
	MediaPlaceholder,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { withNotices } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

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

	const mediaPreview = (
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
				onSelect={ onChange }
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
						onSelect={ onChange }
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
