import initialize from '../util/initialize';
import { handleVisibleChange } from './dropdown';

const _primaryNav = document.querySelector( '[data-dropdown="primary-nav"]' );

// We need to know the language picker so we can disable/close it
const _languagePicker = document.querySelector(
	'[data-dropdown="language-switcher"]'
);

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
		// We're on the desktop
		_primaryNav.dataset.visible = 'yes';
		_primaryNav.dataset.toggleable = 'no';
		_primaryNav.dataset.backdrop = 'inactive';
		_primaryNav.dataset.trap = 'inactive';
	} else {
		// We're on mobile
		_primaryNav.dataset.visible = 'no';
		_primaryNav.dataset.toggleable = 'yes';
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
 * @param {MutationRecord} record The record to handle
 */
function handlePrimaryNavVisibleChange( record ) {
	handleVisibleChange( record );
	const el = record.target;
	const menuIsVisible = el.dataset.visible === 'yes';
	const toggleIsVisible = el.dropdown.toggle.offsetParent != null;

	if ( menuIsVisible && toggleIsVisible ) {
		document.body.classList.add( 'disable-body-scrolling' );
		if ( _languagePicker ) {
			_languagePicker.dataset.visible = 'no';
		}
	} else {
		document.body.classList.remove( 'disable-body-scrolling' );
	}
}

function handleLanguagePickerVisibleChange( record ) {
	handleVisibleChange( record );
	if ( _primaryNav ) {
		const el = record.target;
		const menuIsVisible = el.dataset.visible === 'yes';
		const {
			visible: navIsVisible,
			toggleable: navIsTogglable,
		} = _primaryNav.dataset;

		if ( menuIsVisible && navIsVisible && navIsTogglable ) {
			_primaryNav.dataset.visible = 'no';
		}
	}
}

/**
 * Set up the observer.
 */
function observe() {
	if ( _primaryNav ) {
		_primaryNav.dropdown.handlers.visibleChange = handlePrimaryNavVisibleChange;
		// IntersectionObserver
		_primaryNav.observer = createObserver();
		_primaryNav.observer.observe( _primaryNav.dropdown.toggle );
	}

	if (_languagePicker) {
		_languagePicker.dropdown.handlers.visibleChange = handleLanguagePickerVisibleChange;
	}
}

/**
 * Remove the observer.
 */
function unobserve() {
	if ( _primaryNav && _primaryNav.observer ) {
		_primaryNav.observer.disconnect();
	}
	_primaryNav.dropdown.handlers.visibleChange = handleVisibleChange;
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
export { setup, teardown };
