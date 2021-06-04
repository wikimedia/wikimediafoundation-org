import initialize from '../util/initialize';

import { handleVisibleChange } from './dropdown';

const _tocNav = document.querySelector( '[data-dropdown="toc-nav"]' );
const headerHeight = document
	.getElementsByClassName( 'site-header' )[ 0 ]
	.getBoundingClientRect()[ 'height' ];

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
 * using the visibility of the hidden title to tell us which viewport we're
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

	if ( menuIsVisible && toggleIsVisible ) {
		scrollHelper( dropdown );
		document.body.classList.add( 'disable-body-scrolling' );
	} else {
		document.body.classList.remove( 'disable-body-scrolling' );
	}
}

/**
 * Process active link item.
 *
 * @param {HTMLElement} item Active link item to process.
 * @param {string} hash Hash to set after scroll.
 * @param {number} heightOffset Height offset for scroll function.
 */
function processActiveLink( item, hash = false, heightOffset = 0 ) {
	// Remove existing active classes.
	_tocNav
		.querySelectorAll( '.toc__link' )
		.forEach( link => link.classList.remove( 'toc__link--active' ) );

	// Add the active class to the current item.
	item.classList.add( 'toc__link--active' );

	// Scroll to the right position.
	const activeContentItem = getActiveContent( item );
	if ( activeContentItem ) {
		scrollHelper( activeContentItem, hash, heightOffset );
	}
}

/**
 * Get the active TOC content based on the acitve link
 *
 * @param {HTMLElement} item Active link item to process.
 *
 * @returns {HTMLElement} the active content element.
 */
function getActiveContent( item ) {
	const activeTocLink = item.getAttribute( 'href' );
	const activeContentItem = document.querySelector(
		`h2[id="${ activeTocLink.replace( '#', '' ) }"]`
	);

	return activeContentItem;
}

/**
 * Handle scrolling to the right position
 *
 * @param {HTMLElement} item Element we want to scroll to.
 * @param {string} hash Hash to set after scroll.
 * @param {number} heightOffset Height offset for scroll function.
 */
function scrollHelper( item, hash = false, heightOffset = 0 ) {
	let scrollTimeout;
	const windowScrollY = window.scrollY;
	const itemScrollTop = item.getBoundingClientRect()[ 'top' ];
	const scrollPosition =
		windowScrollY + itemScrollTop + heightOffset - headerHeight - 20;

	window.scrollTo( {
		top: scrollPosition,
		left: 0,
		behavior: 'smooth',
	} );

	/**
	 * Listen for when the page finishes scrolling.
	 */
	function scrollListener() {
		clearTimeout( scrollTimeout );
		scrollTimeout = setTimeout( function () {
			history.replaceState( null, null, hash );
			removeEventListener( 'scroll', scrollListener );
		}, 100 );
	}

	if ( hash ) {
		addEventListener( 'scroll', scrollListener );
	}
}

/**
 * Handle clicks on the TOC links.
 *
 * @param {Event} event Click event on a TOC link.
 */
function handleTocLinkClick( event ) {
	/*
	 * Prevent default so that the page doesn't
	 * scroll to the wrong place.
	 */
	event.preventDefault();

	// Get the new hash for this item.
	const item = event.target;
	const hash = item.getAttribute( 'href' );

	// Process the active link.
	processActiveLink( item, hash );

	// Close the menu if we're on mobile.
	if (
		_tocNav.dataset.toggleable === 'yes' &&
		_tocNav.dataset.visible === 'yes'
	) {
		_tocNav.dataset.visible = 'no';
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

		// Observe the intersection of the title with the page
		_tocNav.observer = createObserver();
		_tocNav.observer.observe( _tocNav.querySelector( '.toc__title' ) );

		// Add event listeners to the links
		_tocNav.querySelectorAll( '.toc__link' ).forEach( link => {
			link.addEventListener( 'click', handleTocLinkClick );
		} );

		if ( location.hash ) {
			const hash = location.hash;
			const navHeight = document
				.getElementsByClassName( 'primary-nav__drawer' )[ 0 ]
				.getBoundingClientRect()[ 'height' ];
			let heightOffset = 0;

			// If we're on desktop and the nav hasn't loaded yet, adjust the numbers.
			if ( window.innerWidth > 781 && navHeight === 0 ) {
				heightOffset = 44;
			}

			/*
			 * Temporarily blank the hash so that the page doesn't
			 * scroll to the wrong place on load.
			 */
			location.hash = '';

			// Process the active link.
			const activeTocItem = _tocNav.querySelector(
				`a[href="${ hash }"]`
			);
			if ( activeTocItem ) {
				processActiveLink( activeTocItem, hash, heightOffset );
			}
		}
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
