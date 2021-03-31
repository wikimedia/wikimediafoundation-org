/**
 * Responsive style adjustments for landing page hero blocks.
 */

import { debounce } from 'lodash';

/**
 * Kick off all functionality in this module.
 *
 * @returns {undefined}
 */
const init = () => [ ...document.querySelectorAll( '.hero' ) ].forEach( balanceColumns );

/**
 * Balance the vertical rule beside the standfirst paragraph with the bottom of
 * the text in the header column.
 *
 * @param {HTMLElement} block Block being balanced.
 */
const balanceColumns = block => {
	const { bottom: headerBottom } = block.querySelector( '.hero__header' ).getBoundingClientRect();
	const { bottom: textColBottom } = block.querySelector( '.hero__text-column' ).getBoundingClientRect();

	block.style.setProperty( '--padding-bottom', `${ headerBottom - textColBottom }px` );
};

// Store a debounced version of the setup function, so that it can be removed if necessary.
const debouncedInit = debounce( init, 100 );

window.addEventListener( 'load', debouncedInit );
window.addEventListener( 'resize', debouncedInit );

if ( module.hot ) {
	module.hot.dispose(
		() => window.removeEventListener( 'resize', debouncedInit )
	);
	module.hot.accept();
	init();
}
