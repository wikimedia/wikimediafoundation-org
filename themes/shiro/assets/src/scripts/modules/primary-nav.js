import initialize from '../util/initialize';

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
	if ( ! entry.isIntersecting ) {
		/**
		 * The toggle is not visible; we can reasonably conclude that
		 * the full menu should be shown since it cannot possibly be
		 * toggled.
		 */
		_content.removeAttribute( 'hidden' );
	} else if ( _dropdown.dataset.open === 'false' ) {
		/**
		 * The toggle is visible, and the state of the dropdown is
		 * "not open"; we can reasonably conclude that a) we have
		 * transitioned from the "desktop" viewport and b) the mobile
		 * menu was last in a "closed" state, so we should return it
		 * visually and semantically to that state.
		 */
		_content.hidden = true;
	}
}

/**
 * @param {IntersectionObserverEntry[]} entries This that have been observed intersecting
 */
function handleIntersection( entries ) {
	entries.forEach( processEntry );
}

/**
 * Set up the observer.
 */
function observe() {
	if ( _dropdown && _toggle && _content ) {
		_dropdown.observer = createObserver();
		_dropdown.observer.observe( _toggle );
	}
}

/**
 * Remove the observer.
 */
function unobserve() {
	if ( _dropdown && _dropdown.observer ) {
		_dropdown.observer.disconnect();
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
