/**
 * Responsive style adjustments for landing page hero blocks.
 */

import debounce from 'lodash.debounce';

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

const debouncedInit = debounce( init, 100 );

document.addEventListener( 'DOMReady', init );
window.addEventListener( 'resize', debouncedInit );

if ( module.hot ) {
	module.hot.dispose(
		() => window.removeEventListener( 'resize', debouncedInit )
	);
	module.hot.accept();
	init();
}
