import initialize from '../util/initialize';

import { handleVisibleChange } from './dropdown';

const _tocNav = document.querySelector( '[data-dropdown="toc-nav"]' );
const _tocNavTitle = _tocNav?.querySelector( '.toc__title' );
const _tocNavUl = _tocNav?.querySelector( '.toc' );
const _contentColumn = _tocNav
	?.closest( '.toc__section' )
	?.querySelector( '.toc__content' );
const headerHeight = document
	.querySelector( '.site-header' )
	.getBoundingClientRect()[ 'height' ];

/**
 * @returns {IntersectionObserver} A configured observer, ready to observe.
 *
 * @param {object} options The options to use with the observer.
 */
function createObserver( options ) {
	return new IntersectionObserver( handleIntersection, options );
}

/**
 * Manage various intersections of content with the viewport.
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
	const { intersectionRatio, isIntersecting, target } = entry;

	// Handle the entry based on the target.
	if ( target === _tocNavUl ) {
		const nav = target.parentElement;

		if (
			isIntersecting &&
			intersectionRatio === 1 &&
			nav.dataset.toggleable === 'no'
		) {
			nav.dataset.sticky = 'yes';
		}
		if ( entry.rootBounds.height < entry.boundingClientRect.height ) {
			nav.dataset.sticky = 'no';
		}
	} else if ( target === _tocNavTitle ) {
		const nav = target.parentElement;
		if ( ! isIntersecting ) {
			// We're on the desktop
			nav.dataset.visible = 'yes';
			nav.dataset.toggleable = 'no';
			nav.dataset.sticky = 'yes';
			nav.dataset.backdrop = 'inactive';
			nav.dataset.trap = 'inactive';
		} else {
			// We're on mobile
			nav.dataset.visible = 'no';
			nav.dataset.toggleable = 'yes';
			nav.dataset.sticky = 'no';
		}
	} else if ( [ 'H2', 'P', 'LI' ].includes( target.tagName ) ) {
		if (
			isIntersecting &&
			intersectionRatio >= 0.5 &&
			target.tagName === 'H2'
		) {
			target.dataset.visible = 'yes';
		} else if ( target.tagName === 'H2' ) {
			target.dataset.visible = 'no';
		}

		if ( _tocNav.dataset.observeScroll === 'yes' ) {
			const allH2s = Object.values(
				_contentColumn.querySelectorAll( 'h2[id]' )
			);
			const firstH2 = allH2s.filter(
				h2 => h2.dataset.visible === 'yes'
			)[ 0 ];

			if ( ! firstH2 ) {
				return;
			}

			const firstH2Index = allH2s.indexOf( firstH2 );
			const firstH2Top = firstH2.getBoundingClientRect()[ 'top' ];
			const windowHeight = window.innerHeight;

			const activeH2 =
				windowHeight / 4 > firstH2Top
					? firstH2
					: allH2s[ firstH2Index - 1 ];
			const activeLink = activeH2 ? getActiveLink( activeH2 ) : null;

			if ( activeLink ) {
				// Process the active link.
				processActiveLink( activeLink );
			}
		}
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
 * @param {boolean} scroll Whether to scroll the content.
 */
function processActiveLink(
	item,
	hash = item.getAttribute( 'href' ),
	scroll = false
) {
	const parentList = item.closest( '.toc__nested' );
	const parentItem = parentList ? parentList.previousElementSibling : false;
	let toggleText = parentItem ? parentItem.innerText : item.innerText;
	toggleText =
		toggleText.length > 0
			? toggleText
			: _tocNav.dropdown.toggle.querySelector( '.btn-label-a11y' )
				.innerText;

	// Remove existing active classes.
	_tocNav
		.querySelectorAll( '.toc__link' )
		.forEach( link => link.classList.remove( 'toc__link--active' ) );

	// Add the active class to the current item.
	item.classList.add( 'toc__link--active' );

	// Update the toggle button text with the active link text.
	_tocNav.dropdown.toggle.querySelector(
		'.btn-label-active-item'
	).textContent = toggleText;

	// Scroll to the right position.
	const activeContentItem = getActiveContent( item );
	if ( activeContentItem && scroll ) {
		scrollHelper( activeContentItem, hash );
	} else {
		history.replaceState( null, null, hash );
	}
}

/**
 * Get the active TOC content based on the active link.
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
 * Get the active TOC link based on the active content.
 *
 * @param {HTMLElement} item Active content item to process.
 *
 * @returns {HTMLElement} the active link element.
 */
function getActiveLink( item ) {
	const activeContentId = item.id;
	const activeLinkItem = document.querySelector(
		`.toc a.toc__link[href="#${ activeContentId }"]`
	);

	return activeLinkItem;
}

/**
 * Handle scrolling to the right position.
 *
 * @param {HTMLElement} item Element we want to scroll to.
 * @param {string} hash Hash to set after scroll.
 */
function scrollHelper( item, hash = false ) {
	let scrollTimeout;

	// Disable the setting of active links on scroll.
	_tocNav.dataset.observeScroll = 'no';
	if ( hash ) {
		history.replaceState( null, null, hash );
	}
	item.scrollIntoView( { behavior: 'smooth' } );
	addEventListener( 'scroll', scrollListener );

	/**
	 * Listen for when the page finishes scrolling.
	 */
	function scrollListener() {
		clearTimeout( scrollTimeout );
		scrollTimeout = setTimeout( function () {
			_tocNav.dataset.observeScroll = 'yes';
			removeEventListener( 'scroll', scrollListener );
		}, 100 );
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
	processActiveLink( item, hash, true );

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
		// Ensure correct state on load.
		handleTocNavVisibleChange( _tocNav );
		_tocNav.dropdown.handlers.visibleChange = handleTocNavVisibleChange;

		// Observe the intersection of the title with the page.
		_tocNav.observer = createObserver( {
			root: null,
			rootMargin: headerHeight + 'px 0px 0px 0px',
			threshold: [ 0, 0.25, 0.5, 0.75, 1 ],
		} );
		_tocNav.observer.observe( _tocNavTitle );
		_tocNav.observer.observe( _tocNavUl );

		// Add event listeners to the links.
		_tocNav.querySelectorAll( '.toc__link[href^="#"]' ).forEach( link => {
			link.addEventListener( 'click', handleTocLinkClick );
		} );

		// Add observers to the h2s.
		if ( _contentColumn ) {
			_contentColumn.observer = createObserver( {
				root: null,
				rootMargin: headerHeight + 'px 0px 0px 0px',
				threshold: [ 0, 0.25, 0.5, 0.75, 1 ],
			} );
			_contentColumn.querySelectorAll( 'h2, p, li' ).forEach( _el => {
				_contentColumn.observer.observe( _el );
			} );
		}

		// Handle hash on initial load.
		if ( location.hash ) {
			const hash = location.hash;

			// Process the active link.
			const activeTocItem = _tocNav.querySelector(
				`a[href="${ hash }"]`
			);
			if ( activeTocItem ) {
				processActiveLink( activeTocItem, hash );
			}

			// Turn on sticky nav so that it can do it's own detections.
			if ( _tocNav.dataset.toggleable === 'no' ) {
				_tocNav.dataset.sticky = 'yes';
			}
		}

		/**
		 * When the page is finished loading, start observing the h2 intersections.
		 *
		 * @param {Event} event Window load event.
		 */
		window.onload = event => {
			_tocNav.dataset.observeScroll = 'yes';
		};
	}
}

/**
 * Tear down the TOC navigation and h2 observers.
 */
function teardownTocNav() {
	if ( _tocNav && _tocNav.observer ) {
		_tocNav.observer.disconnect();
	}
	_tocNav.dropdown.handlers.visibleChange = handleVisibleChange;

	if ( _contentColumn && _contentColumn.observer ) {
		_contentColumn.observer.disconnect();
	}
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
