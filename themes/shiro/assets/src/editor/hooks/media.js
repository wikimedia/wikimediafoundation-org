import { noop } from 'lodash';

import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Hook to select a certain image size from a given media ID.
 *
 * @param {number} id The ID of the media item to get the image size for.
 * @param {string} size The size to return
 * @param {Function} onChange Callback is called when new data is available.
 *                   Useful for setAttributes calls.
 * @returns {object} Data about the media item, including the correctly sized URL.
 */
export const useImageSize = ( id, size, onChange = noop ) => {
	// Query the API to get the correct URL for the image size.
	const media = useSelect( select => {
		return select( 'core' ).getMedia( id );
	} );
	const sizeData = media?.media_details.sizes[ size ];
	const url = sizeData?.source_url || media?.source_url;
	const width = sizeData?.width || media?.width;
	const height = sizeData?.height || media?.height;

	// Call the on change handler only when any of the inputs change.
	useEffect( () => {
		if ( url ) {
			onChange( {
				id,
				alt: media?.alt,
				src: url,
				width,
				height,
				media,
			} );
		}
	}, [ id, size, onChange, url, media, width, height ] );

	return {
		alt: media?.alt,
		src: url,
		media,
	};
};
