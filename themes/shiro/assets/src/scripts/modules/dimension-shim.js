/**
 * This module provides a very tiny fix to handle unreliable behavior from
 * mobile browsers as well as the varying widths we get from scrollbars.
 *
 * @see https://css-tricks.com/the-trick-to-viewport-units-on-mobile/#css-custom-properties-the-trick-to-correct-sizing
 * @see https://stackoverflow.com/questions/37112218/css3-100vh-not-constant-in-mobile-browser
 */

/**
 * Used to hold our timeout.
 * Defined out here so that it will be available for all appropriate scopes.
 *
 * @type {number}
 */
let timeout = 0;

/**
 * Set any dimensions we need.
 */
function setDimensions() {
	// Calculates a "more accurate" pixel value for a single vh unit
	let vh = window.innerHeight * 0.01;
	document.documentElement.style.setProperty( '--vh', `${vh}px` );

	// Calculate the width of the scrollbar (if it exists).
	// Keep in mind this can have a value of 0px--i.e. please don't use it as a divisor.
	let scrollbar = window.innerWidth - document.documentElement.clientWidth;
	document.documentElement.style.setProperty( '--scrollbar', `${scrollbar}px` );
}

/**
 * Do something when the window resizes.
 */
function handleResize() {
	clearTimeout( timeout );
	timeout = setTimeout( setDimensions, 250 );
}

/**
 * Listen to resize events, but throttle any behavior.
 */
function watchResize() {
	window.addEventListener( 'resize', handleResize );
}

/**
 * Set up the behavior of this module
 */
function setup() {
	// Fire when setup, to set initial value.
	setDimensions();

	// Listen for resize events and update.
	watchResize();
}

/**
 * Remove the listener and reset.
 */
function teardown() {
	window.removeEventListener( 'resize', handleResize );
	document.documentElement.style.removeProperty( '--vh' );
	document.documentElement.style.removeProperty( '--scrollbar' );
}

/**
 * Initialize module with HMR support.
 *
 * If you /just/ want to setup it up, import `setup`.
 */
function initialize() {
	if ( module.hot ) {
		module.hot.accept();
		module.hot.dispose( teardown );
		setup();
	} else {
		setup();
	}
}

export default initialize;
export {
	teardown, setup,
};
