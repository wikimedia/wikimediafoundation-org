import initialize from '../util/initialize';

/**
 * This module applies a class to the site header when it is not at the top
 * of the viewport, allowing us to adjust styling and CSS behavior when it is
 * at the top/not at the top.
 */
const siteHeaderSelector = 'site-header';
const pinnedClass = `${ siteHeaderSelector }--pinned`;

/**
 * This is set outside of any method to 'cache' its value.
 *
 * @type {Element}
 */
const _siteHeaderBar = document.querySelector( `.${ siteHeaderSelector }` );

/**
 * Handles everything this module needs to do when a scroll event happens.
 */
function handleScroll() {
	if ( document.documentElement.scrollTop > 0 ) {
		_siteHeaderBar.classList.add( pinnedClass );
	} else {
		_siteHeaderBar.classList.remove( pinnedClass );
	}
}

/**
 * Run any tasks necessary to start up this module.
 */
function setup() {
	const throttle = require( 'lodash/throttle' );
	window.addEventListener(
		'scroll',
		throttle( handleScroll, 100, { trailing: true } )
	);
}

/**
 * Remove the (side-)effects of this module, i.e. for HMR.
 * Probably not used in production.
 */
function teardown() {
	window.removeEventListener( 'scroll', handleScroll );
	if ( _siteHeaderBar ) {
		_siteHeaderBar.classList.remove( pinnedClass );
	}
}

export default initialize( setup, teardown );

export {
	teardown,
	setup,
};
