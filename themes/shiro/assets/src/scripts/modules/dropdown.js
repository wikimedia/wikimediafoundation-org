import initialize from '../util/initialize';

/**
 * These are set outside of any method to 'cache' their values.
 *
 * Don't access these directly unless you need to reset them to something else
 * (i.e. you're modifying the DOM w/ JavaScript). Use their get methods
 * (getBackdrop and getInstances) instead.
 *
 * @type {Element}
 */
let _backdrop = document.querySelector( '[data-dropdown-backdrop]' );
let _instances = [ ...document.querySelectorAll( '[data-dropdown]' ) ];

/**
 * Get an array of all elements that seem to be dropdowns.
 *
 * @returns {Element[]} All the potential dropdowns in this document.
 */
function getInstances() {
	return _instances;
}

/**
 * Change content classes when dropdown state changes.
 *
 * @param {MutationRecord[]} list List of MutationRecords
 * @param {MutationObserver} observer The MutationObserver instance
 *
 * @returns {void}
 */
function handleMutation( list, observer ) {
	list.forEach( r => {
		if ( r.attributeName === 'data-open' ) {
			const el = r.target;
			const open = el.dataset.open;
			const { content, toggle, customHandler } = el.dropdown;
			content.hidden = open !== 'true';

			toggle.setAttribute( 'aria-expanded', open );

			/**
			 * Make it easier to hook custom code into the observer.
			 */
			if ( customHandler ) {
				customHandler( r );
			}

			const backdrop = getBackdrop();
			// Only modify the backdrop if it exists /and/ there are no other
			// open dropdowns.
			if ( backdrop && getInstances()
				.filter( dropdown => {
					if ( dropdown === el ) {
						return false; // Skip *this* dropdown
					}
					return dropdown.dataset.open === 'true';
				} ).length < 1 ) {
				backdrop.dataset.dropdownBackdrop = open === 'true' ? 'active' : 'inactive';
			}
		}
	} );
}

/**
 * Add dropdown functionality to a specific element.
 *
 * The dropdown wrapper is considered the single source of truth for the
 * content and toggle it contains. If you want to know or change the state of
 * the dropdown, look at this element.
 *
 * @param {Element} el The element to upgrade and instantiate.
 *
 * @returns {Element} Upgraded and instantiated element.
 */
function instantiate( el ) {
	const name = el.dataset.dropdown;
	const content = el.querySelector( `[data-dropdown-content='${name}']` );
	const toggle = document.querySelector( `[data-dropdown-toggle='${name}']` );

	/**
	 * Swap content state when click happens.
	 */
	const handleClick = () => {
		el.dataset.open = el.dataset.open === 'true' ? 'false': 'true';
	};

	toggle.addEventListener( 'click', handleClick );

	const observer = new MutationObserver( handleMutation );
	observer.observe( el, {
		attributes: true,
		childList: false,
		subtree: false,
	} );

	el.dropdown = {
		name,
		content,
		toggle,
		observer,
		handleClick,
	};

	return el;
}

/**
 * Remove the dropdown functionality from a specific element.
 *
 * @param {Element} el The element to remove dropdown functionality from
 *
 * @returns {Element} The element, with dropdown functionality removed
 */
function destroy( el ) {
	if ( el.dropdown ) {
		// Stop watching mutations
		el.dropdown.observer.disconnect();
		// Remove click watcher
		el.dropdown.toggle.removeEventListener( 'click', el.dropdown.handleClick );
		// Remove all data
		el.dropdown = null;
	}

	return el;
}

/**
 * Returns the backdrop element.
 *
 * This will only ever return one, even if multiple backdrops exist in the HTML.
 *
 * @returns {Element} Backdrop element (if found)
 */
function getBackdrop() {
	return _backdrop;
}

/**
 * Handles clicks on the backdrop.
 */
function handleBackdropClick() {
	getInstances().forEach( dropdown => {
		dropdown.dataset.open = 'false';
	} );
}

/**
 * Sets up the backdrop, which closes all active dropdowns when clicked.
 *
 * If it can't find any backdrops, this fails quietly.
 */
function initializeBackdrop() {
	const backdrop = getBackdrop();
	if ( backdrop ) {
		backdrop.addEventListener( 'click', handleBackdropClick );
	}
}

/**
 * Set up all dropdowns on the page.
 *
 * @returns {void}
 */
function setup() {
	getInstances().map( instantiate );
	initializeBackdrop();
}

/**
 * Remove functionality from all dropdowns on page.
 *
 * @returns {void}
 */
function teardown() {
	getInstances().map( destroy );
	const backdrop = getBackdrop();
	if ( backdrop ) {
		backdrop.removeEventListener( 'click', handleBackdropClick );
		// Probably don't want it left open
		backdrop.dataset.dropdownBackdrop = 'inactive';
	}
}

export default initialize( setup, teardown );

export {
	teardown,
	setup,
	getBackdrop,
	getInstances,
};
