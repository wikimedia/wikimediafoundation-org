import initialize from '../util/initialize';

import { handleVisibleChange } from './dropdown';

const _tocNav = document.querySelector( '[data-dropdown="toc-nav"]' );

/**
 * @returns {IntersectionObserver} A configured observer, ready to observe
 */
function createObserver() {
	return new IntersectionObserver( handleIntersection );
}

/**
 * Manipulate TOC nav to have the viewport-correct behavior.
 *
 * The TOC nav behaves differently on "mobile" and "desktop"--it uses
 * the dropdown hide/show functionality on mobile, but not on desktop. Here,
 * we change change the appropriate states so that the menu behaves correctly,
 * using the visibility of the toggle button to tell us which viewport we're
 * on. By using IntersectionObserver, we don't have to fire the logic once
 * on load and again when the viewport size changes--all of that is handled for
 * us by the browser.
 *
 * @param {IntersectionObserverEntry} entry A thing that was observed intersecting
 */
function processEntry( entry ) {
	if ( ! entry.isIntersecting ) {
		// We're on the desktop
		_tocNav.dataset.visible = 'yes';
		_tocNav.dataset.toggleable = 'no';
		_tocNav.dataset.backdrop = 'inactive';
		_tocNav.dataset.trap = 'inactive';
	} else {
		// We're on mobile
		_tocNav.dataset.visible = 'no';
		_tocNav.dataset.toggleable = 'yes';
	}
}

/**
 * @param {IntersectionObserverEntry[]} entries Things that have been observed intersecting
 */
function handleIntersection( entries ) {
	entries.forEach( processEntry );
}

/**
 * Handles the mutation action of the dropdown.
 *
 * This expands on the base behavior of the dropdown's visibility handler.
 * It applies a class to disable body scrolling while the menu is open.
 *
 * @param {HTMLElement} dropdown A dropdown wrapper
 */
function handleTocNavVisibleChange( dropdown ) {
	handleVisibleChange( dropdown );
	const menuIsVisible = dropdown.dataset.visible === 'yes';
	const toggleIsVisible = dropdown.dropdown.toggle.offsetParent != null;
	const togglePosition = dropdown.offsetTop;
	const headerHeight = document
		.getElementsByClassName( 'site-header' )[ 0 ]
		.getBoundingClientRect()[ 'height' ];

	if ( menuIsVisible && toggleIsVisible ) {
		window.scrollTo( {
			top: togglePosition - headerHeight,
			left: 0,
			behavior: 'smooth',
		} );
		dropdown.style.setProperty(
			'--dropdown-bottom',
			dropdown.getBoundingClientRect()[ 'bottom' ] + 'px'
		);
		document.body.classList.add( 'disable-body-scrolling' );
	} else {
		document.body.classList.remove( 'disable-body-scrolling' );
	}
}

/**
 * Set up the TOC navigation.
 */
function initializeTocNav() {
	if ( _tocNav ) {
		// Ensure correct state on load
		handleTocNavVisibleChange( _tocNav );
		_tocNav.dropdown.handlers.visibleChange = handleTocNavVisibleChange;

		_tocNav.observer = createObserver();
		_tocNav.observer.observe( _tocNav.querySelector( '.toc__title' ) );
	}
}

/**
 * Tear down the TOC navigation.
 */
function teardownTocNav() {
	if ( _tocNav && _tocNav.observer ) {
		_tocNav.observer.disconnect();
	}
	_tocNav.dropdown.handlers.visibleChange = handleVisibleChange;
}

/**
 * Set up functionality for this module.
 */
function setup() {
	initializeTocNav();
}

/**
 * Tear down functionality and side-effects of this module.
 */
function teardown() {
	teardownTocNav();
}

export default initialize( setup, teardown );
export {
	setup, teardown,
};
