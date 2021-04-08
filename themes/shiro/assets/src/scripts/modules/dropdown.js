/**
 * This is set outside of any method to 'cache' this value.
 * Methods shouldn't access this directly--they should use getBackdrop()--but
 * this allows us to only look it up once /and/ unset/reset it if necessary
 * i.e. potentially during HMR.
 *
 * @type {Element}
 */
let _backdrop = document.querySelector( '[data-dropdown-backdrop]' );

/**
 * Get an array of all elements that seem to be dropdowns.
 *
 * @returns {Element[]} All the potential dropdowns in this document.
 */
function getInstances() {
	return [ ...document.querySelectorAll( '[data-dropdown]' ) ];
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
			const { content, toggle } = el.dropdown;
			content.hidden = open !== 'true';

			toggle.setAttribute( 'aria-expanded', open );

			const backdrop = getBackdrop();
			if ( backdrop ) {
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
	getInstances().map( dropdown => {
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
 * Set up the dropdowns with support for HMR.
 *
 * If you /just/ want to set up dropdowns, import `setup`.
 *
 * @returns {void}
 */
function initialize() {
	if ( module.hot ) {
		module.hot.accept();
		module.hot.dispose( teardown );
		setup();
	} else {
		setup();
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

export default initialize;
export {
	teardown, setup,
};
