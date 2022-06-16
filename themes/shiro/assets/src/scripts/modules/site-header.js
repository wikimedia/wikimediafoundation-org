import initialize from '../util/initialize';

import {
	calculateFocusableElements,
	getFocusableInside,
	handleVisibleChange,
} from './dropdown';

const _primaryNav = document.querySelector( '[data-dropdown="primary-nav"]' );

// We need to know the language picker so we can disable/close it
const _languagePicker = document.querySelector(
	'[data-dropdown="language-switcher"]'
);

// Get all primary nav items with children.
const _subNavMenus = _primaryNav.querySelectorAll(
	'.menu-item[data-dropdown]'
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
	const { isIntersecting, target } = entry;

	if ( target === _primaryNav.dropdown.toggle ) {
		if ( ! isIntersecting ) {
			// We're on the desktop
			_primaryNav.dataset.visible = 'yes';
			_primaryNav.dataset.toggleable = 'no';
			_primaryNav.dataset.backdrop = 'inactive';
			_primaryNav.dataset.trap = 'inactive';

			// Make subnavs not toggleable.
			_subNavMenus.forEach( _subNavMenu => {
				_subNavMenu.dataset.toggleable = 'no';
				_subNavMenu.dropdown.toggle.hidden = true;
			} );
		} else {
			// We're on mobile
			_primaryNav.dataset.visible = 'no';
			_primaryNav.dataset.toggleable = 'yes';

			// Make subnavs toggleable.
			_subNavMenus.forEach( _subNavMenu => {
				_subNavMenu.dataset.toggleable = 'yes';
				_subNavMenu.dropdown.toggle.removeAttribute( 'hidden' );
			} );
		}
	} else if ( target.classList.contains( 'sub-menu' ) ) {
		target.closest( '[data-dropdown]' ).dataset.trap = 'inactive';
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
 * @param {HTMLElement} dropdown A dropdown wrapper
 */
function handlePrimaryNavVisibleChange( dropdown ) {
	handleVisibleChange( dropdown );
	const menuIsVisible = dropdown.dataset.visible === 'yes';
	const toggleIsVisible = dropdown.dropdown.toggle.offsetParent != null;

	if ( menuIsVisible && toggleIsVisible ) {
		// When the menu is open on mobile, disable body scrolling and close the language picker.
		document.body.classList.add( 'disable-body-scrolling' );
		if ( _languagePicker ) {
			_languagePicker.dataset.visible = 'no';
		}
	} else {
		// When the menu is closed or untoggleable, allow body scrolling.
		document.body.classList.remove( 'disable-body-scrolling' );

		// Primary nav and subnav interactions.
		_subNavMenus.forEach( _subNavMenu => {
			// Make sure open subnavs aren't triggering the backdrop.
			_subNavMenu.dataset.backdrop = 'inactive';

			// Check to see if any subnavs are active and set primary nav attrs.
			if (
				_subNavMenu.dataset.visible === 'yes' &&
				_primaryNav.dataset.toggleable === 'no'
			) {
				_primaryNav.dataset.subnavVisible = 'yes';
				_primaryNav.style.setProperty(
					'--subnav-margin-bottom',
					_subNavMenu.dropdown.content.offsetHeight
				);

				_primaryNav
					.querySelectorAll(
						'.current-menu-item, .current-menu-ancestor'
					)
					.forEach( _el => {
						if (
							_el.offsetTop + _el.offsetHeight >=
							_el.closest( 'ul' ).offsetHeight - _el.offsetHeight
						) {
							_el.classList.add( 'menu-item-bottom-line' );
						} else {
							_el.classList.remove( 'menu-item-bottom-line' );
						}
					} );
			}
		} );
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
 * @param {HTMLElement} dropdown A dropdown wrapper
 */
function handleLanguagePickerVisibleChange( dropdown ) {
	handleVisibleChange( dropdown );
	if ( _primaryNav ) {
		const menuIsVisible = dropdown.dataset.visible === 'yes';
		const {
			visible: navIsVisible,
			toggleable: navIsToggleable,
		} = _primaryNav.dataset;

		if ( menuIsVisible && navIsVisible && navIsToggleable === 'yes' ) {
			_primaryNav.dataset.visible = 'no';
		}
	}
}

/**
 * Set up the site header.
 */
function initializeSiteHeader() {
	if ( _primaryNav ) {
		// Ensure correct state on load
		handlePrimaryNavVisibleChange( _primaryNav );
		_primaryNav.dropdown.handlers.visibleChange = handlePrimaryNavVisibleChange;
		const headerContent = _primaryNav.querySelector( '.header-content' );
		const translationBar = _primaryNav.querySelector( '.translation-bar' );
		const skip = [
			...( translationBar ? getFocusableInside( translationBar ) : [] ),
			...( headerContent ? getFocusableInside( headerContent ) : [] ),
		];

		/**
		 * Get focusable elements for the primary navigation.
		 *
		 * @returns {{last, allowed, skip, first}} The necessary focusable collections
		 */
		_primaryNav.dropdown.getFocusable = () => {
			return calculateFocusableElements(
				getFocusableInside( _primaryNav ),
				skip
			);
		};

		// Observe the primary nav for desktop/mobile toggling.
		_primaryNav.observer = createObserver();
		_primaryNav.observer.observe( _primaryNav.dropdown.toggle );

		// Observe the submenus.
		_subNavMenus.forEach( _subNavMenu => {
			_primaryNav.observer.observe( _subNavMenu.dropdown.content );
		} );
	}

	if ( _languagePicker ) {
		// Ensure correct state on load
		handleLanguagePickerVisibleChange( _languagePicker );
		_languagePicker.dropdown.handlers.visibleChange = handleLanguagePickerVisibleChange;
	}
}

/**
 * Tear down the site header.
 */
function teardownSiteHeader() {
	if ( _primaryNav && _primaryNav.observer ) {
		_primaryNav.observer.disconnect();
	}
	_primaryNav.dropdown.handlers.visibleChange = handleVisibleChange;
}

/**
 * Set up functionality for this module.
 */
function setup() {
	initializeSiteHeader();
}

/**
 * Tear down functionality and side-effects of this module.
 */
function teardown() {
	teardownSiteHeader();
}

export default initialize( setup, teardown );
export {
	setup, teardown,
};
