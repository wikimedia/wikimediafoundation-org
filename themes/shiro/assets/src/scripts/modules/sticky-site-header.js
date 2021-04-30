import initialize from '../util/initialize';
/**
 * This module applies a class to the site header when it is not at the top
 * of the viewport, allowing us to adjust styling and CSS behavior when it is
 * at the top/not at the top.
 */

const siteHeaderSelector = 'site-header';
const pinnedClass = `${siteHeaderSelector}--pinned`;

/**
 * This is set outside of any method to 'cache' its value.
 *
 * Don't access it directly unless you need to reset it to something else
 * (i.e. you're modifying the DOM w/ JavaScript). Use its get method
 * (getSiteHeaderBar) instead.
 *
 * @type {Element}
 */
let _siteHeaderBar = document.querySelector( `.${siteHeaderSelector}` );

/**
 * Get the element that is the site header.
 *
 * @returns {Element} The element that represents the site header.
 */
function getSiteHeaderBar() {
	return _siteHeaderBar;
}

/**
 * Handles everything this module needs to do when a scroll event happens.
 */
function handleScroll() {
	const el = getSiteHeaderBar();
	if ( document.documentElement.scrollTop > 0 ) {
		el.classList.add( pinnedClass );
	} else {
		el.classList.remove( pinnedClass );
	}
}

/**
 * Run any tasks necessary to start up this module.
 *
 * @todo It would be nice if we could somehow avoid running this on *every* event while maintaining a smooth experience.
 */
function setup() {
	window.addEventListener( 'scroll', handleScroll );
}

/**
 * Remove the (side-)effects of this module, i.e. for HMR.
 * Probably not used in production.
 */
function teardown() {
	window.removeEventListener( 'scroll', handleScroll );
	const el = getSiteHeaderBar();
	if ( el ) {
		el.classList.remove( pinnedClass );
	}
}

export default initialize( setup, teardown );

export {
	teardown, setup,
};
