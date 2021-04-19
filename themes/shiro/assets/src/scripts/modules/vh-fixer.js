/**
 * This module provides a very tiny fix to handle unreliable behavior from
 * mobile browsers.
 *
 * @see https://css-tricks.com/the-trick-to-viewport-units-on-mobile/#css-custom-properties-the-trick-to-correct-sizing
 * @see https://stackoverflow.com/questions/37112218/css3-100vh-not-constant-in-mobile-browser
 */

/**
 * Set a --vh custom prop that is equal to the right height.
 */
function setHeight() {
	let vh = window.innerHeight * 0.01;
	document.documentElement.style.setProperty( '--vh', `${vh}px` );
}

/**
 * Fire setHeight when the window resizes.
 */
function setup() {
	// Fire when setup, to set initial value.
	setHeight();
	// Listen for resize events and update.
	window.addEventListener( 'resize', setHeight );
}

/**
 * Remove the listener and reset.
 */
function teardown() {
	window.removeEventListener( 'resize', setHeight );
	document.documentElement.style.removeProperty( '--vh' );
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
