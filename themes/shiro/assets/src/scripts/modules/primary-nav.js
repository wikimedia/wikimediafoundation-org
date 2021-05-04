import initialize from '../util/initialize';

import { getBackdrop } from './dropdown';

const dropdownSelector = '[data-dropdown="primary-nav"]';
const buttonSelector = '[data-dropdown-toggle="primary-nav"]';
const contentSelector = '[data-dropdown-content="primary-nav"]';

const _dropdown = document.querySelector( dropdownSelector );
const _toggle = document.querySelector( buttonSelector );
const _content = document.querySelector( contentSelector );

/**
 * @returns {IntersectionObserver} A configured observer, ready to observe
 */
function createObserver() {
	return new IntersectionObserver( handleIntersection );
}

/**
 * @param {IntersectionObserverEntry} entry A thing that was observed intersecting
 */
function processEntry( entry ) {
	/**
	 * The observer will only trigger when the toggle becomes visible or
	 * stops being visible: Since we know the circumstances and "direction"
	 * each time that happens, we can reasonably assume:
	 * 1) If it is intersecting, we moved from 'desktop' to 'mobile' and the
	 *    menu should therefore be closed.
	 * 2) If it is *not* intersection, we moved from 'mobile' to 'desktop' and
	 *    the menu should therefore be opened.
	 *
	 * While visually we could do whatever we want with CSS, this manipulation
	 * of the dom is necessary in order to maintain the most accessible version
	 * of the menu.
	 */
	_dropdown.dataset.open = entry.isIntersecting ? 'false' : 'true';

	/**
	 * Normally the backdrop appears when a dropdown is open, but in this case
	 * we don't want that.
	 */
	if ( ! entry.isIntersecting ) {
		/**
		 * setTimeout is necessary here because otherwise this executes at the
		 * same time as the above, with the result that the backdrop is not
		 * removed.
		 */
		window.setTimeout( () => {
			const backdrop = getBackdrop();
			backdrop.dataset.dropdownBackdrop = 'inactive';
		}, 1 );
	}
}

/**
 * @param {IntersectionObserverEntry[]} entries This that have been observed intersecting
 */
function handleIntersection( entries ) {
	entries.forEach( processEntry );
}

/**
 * Handles the mutation action of the dropdown.
 *
 * @param {MutationRecord} record The record to handle
 */
function handleMutation( record ) {
	if ( record.target.dataset.open === 'true' ) {
		document.body.classList.add( 'primary-nav-is-open' );
	} else {
		document.body.classList.remove( 'primary-nav-is-open' );
	}
}

/**
 * Set up the observer.
 */
function observe() {
	if ( _dropdown && _toggle && _content ) {
		// IntersectionObserver
		_dropdown.observer = createObserver();
		_dropdown.observer.observe( _toggle );

		// MutationObserver
		_dropdown.dropdown.customHandler = handleMutation;
	}
}

/**
 * Remove the observer.
 */
function unobserve() {
	if ( _dropdown && _dropdown.observer ) {
		_dropdown.observer.disconnect();
		delete _dropdown.dropdown.customHandler;
	}
}

/**
 * Set up functionality for this module.
 */
function setup() {
	observe();
}

/**
 * Tear down functionality and side-effects of this module.
 */
function teardown() {
	unobserve();
}

export default initialize( setup, teardown );
export {
	setup,
	teardown,
};
