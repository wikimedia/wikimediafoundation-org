import initialize from '../util/initialize';

const dropdownSelector = '[data-dropdown="primary-nav"]';
const buttonSelector = '[data-dropdown-toggle="primary-nav"]';
const contentSelector = '[data-dropdown-content="primary-nav"]';

const _dropdown = document.querySelector( dropdownSelector );
const _toggle = document.querySelector( buttonSelector );
const _content = document.querySelector( contentSelector );

// We need to know the language picker so we can disable/close it
const _languagePicker = document.querySelector( '[data-dropdown="language-switcher"]' );

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
	 * The following is some shuffling that is necessary in order to allow the
	 * behavior of the primary navigation to make sense if someone resizes
	 * the window while the menu is open.
	 */
	if ( ! entry.isIntersecting ) {
		/**
		 * The toggle is not visible; we can reasonably conclude that
		 * the full menu should be shown since it cannot possibly be
		 * toggled.
		 */
		_content.removeAttribute( 'hidden' );

		/**
		 * The language picker should also be enabled, since it can no longer
		 * conflict with the primary nav.
		 */
		_languagePicker?.dropdown.toggle.removeAttribute( 'disabled' );
	} else if ( _dropdown.dataset.open === 'false' ) {
		/**
		 * The toggle is visible, and the state of the dropdown is
		 * "not open"; we can reasonably conclude that a) we have
		 * transitioned from the "desktop" viewport and b) the mobile
		 * menu was last in a "closed" state, so we should return it
		 * visually and semantically to that state.
		 */
		_content.hidden = true;
	} else if ( _dropdown.dataset.open === 'true' ) {
		/**
		 * If the primary nav is open when we transition to mobile, then we
		 * need to make sure the language picker is disabled.
		 */
		if ( _languagePicker ) {
			_languagePicker.dropdown.toggle.disabled = true;
		}
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
		if ( _languagePicker ) {
			_languagePicker.dataset.open = 'false';
			_languagePicker.dropdown.toggle.disabled = true;
		}
	} else {
		document.body.classList.remove( 'primary-nav-is-open' );
		_languagePicker?.dropdown.toggle.removeAttribute( 'disabled' );
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
