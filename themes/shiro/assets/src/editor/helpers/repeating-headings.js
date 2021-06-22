import { isBoolean, last } from 'lodash';

import { RichText } from '@wordpress/block-editor';

/**
 * Ensure an empty heading is present at the end of the array.
 *
 * This allows the user to add new headings and to keep focus in the RichText.
 *
 * @param {Array} headings The original list of headings
 * @returns {Array} The modified list of headings
 */
export const ensureEmptyHeading = headings => {
	if ( headings.length === 0 || ! RichText.isEmpty( last( headings ).text ) ) {
		headings = [
			...headings,
			{
				text: '',
			},
		];
	}

	return headings;
};

/**
 * Prepare headings for use in the save & edit functions.
 *
 * @param {Array} headings Headings as they are saved in the attributes.
 * @returns {Array} Headings for use in the render functions.
 */
export const prepareHeadings = headings => {
	// This allows the user to 'delete' headings, by leaving them empty
	headings = headings.filter( heading => ! RichText.isEmpty( heading.text ) );
	headings = headings.map( heading => {
		return {
			...heading,
			switchRtl: isBoolean( heading.switchRtl ) ?
				heading.switchRtl :
				(
					heading.classNames || ''
				).includes( 'rtl-switch' ),
		};
	} );

	return headings;
};
