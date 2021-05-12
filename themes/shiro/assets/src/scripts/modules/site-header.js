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
 * Manipulate primary nav to have the viewport-correct behavior.
 *
 * The primary nav behaves differently on "mobile" and "desktop"--it uses
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
 * This expands on the base behavior of the dropdown's visibility handler.
 * It applies a class to disable body scrolling while the menu is open, and
 * provides half of the logic that allows the language switcher and primary nav
 * to close one another (for the other half,
 * see handleLanguagePickerVisibleChange()).
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

/**
 * Handles the mutation action of the language picker.
 *
 * This expands on the base behavior of the dropdown's visibility handler.
 * It applies half of the logic that allows the language switcher and primary
 * nav to close one another (for the other half,
 * see handlePrimaryNavVisibleChange()).
 *
 * @param {MutationRecord} record The record to handle
 */
function handleLanguagePickerVisibleChange( record ) {
	handleVisibleChange( record );
	if ( _primaryNav ) {
		const el = record.target;
		const menuIsVisible = el.dataset.visible === 'yes';
		const {
			visible: navIsVisible,
			toggleable: navIsToggleable,
		} = _primaryNav.dataset;

		if ( menuIsVisible && navIsVisible && navIsToggleable ) {
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
		_primaryNav.observer = createObserver();
		_primaryNav.observer.observe( _primaryNav.dropdown.toggle );
	}

	if ( _languagePicker ) {
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
export {
	setup,
	teardown,
};
