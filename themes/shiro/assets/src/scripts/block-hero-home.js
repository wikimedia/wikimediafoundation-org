/**
 * Functionality for rotating the rotating headings in the hero home
 */

/**
 * Bootstrap frontend functionality.
 *
 * - This file is loaded at the bottom of the body, so we don't need an onready.
 */
const NO_CYCLING_HEADING_COUNT = 1;
const CYCLE_TIME = 5000;
const OPACITY_TRANSITION_TIME = 750;
const BROWSER_PAINT_WAIT = 20;
const headings = document.querySelectorAll( '.hero-home__heading' );
let currentHeadingIndex = 0,
	previousHeadingIndex = 0;
let currentHeading = headings[ 0 ];
let previousHeading = headings[ 0 ];
let timeout = null;

const targetLink = document.querySelector( '.hero-home__link' );

/**
 * Setup variables for fading in and out.
 *
 * @returns {void}
 */
function cycleHeading() {
	if ( ! targetLink || targetLink !== document.activeElement ) {
		previousHeadingIndex = currentHeadingIndex;
		currentHeadingIndex = ++currentHeadingIndex % headings.length;

		currentHeading = headings[ currentHeadingIndex ];
		previousHeading = headings[ previousHeadingIndex ];

		if ( targetLink ) {
			const targetLinkScreenReaderText = targetLink.querySelector( '.screen-reader-text' );
			if ( targetLinkScreenReaderText ) {
				targetLinkScreenReaderText.textContent = currentHeading.textContent;
			}
		}

		fadeOutPreviousHeading();
	} else {
		// Setup the next cycle
		timeout = setTimeout( cycleHeading, CYCLE_TIME );
	}
}

/**
 * Fade out previous heading.
 *
 * @returns {void}
 */
function fadeOutPreviousHeading() {
	previousHeading.classList.add( 'hero-home__heading--transparent' );
	timeout = setTimeout( fadeInCurrentHeading, OPACITY_TRANSITION_TIME );
}

/**
 * Fade in the current heading and set display values.
 *
 * @returns {void}
 */
function fadeInCurrentHeading() {
	previousHeading.classList.add( 'hero-home__heading--hidden' );
	currentHeading.classList.remove( 'hero-home__heading--hidden' );

	// Allow the browser to display the element with opacity 0 before setting it to 1.
	setTimeout( function () {
		currentHeading.classList.remove( 'hero-home__heading--transparent' );
	}, BROWSER_PAINT_WAIT );

	// Setup the next cycle
	timeout = setTimeout( cycleHeading, CYCLE_TIME );
}

if ( headings.length > NO_CYCLING_HEADING_COUNT ) {
	timeout = setTimeout( cycleHeading, CYCLE_TIME );
}

if ( module.hot ) {
	module.hot.dispose( () => timeout && clearTimeout( timeout ) );
	module.hot.accept();
}
