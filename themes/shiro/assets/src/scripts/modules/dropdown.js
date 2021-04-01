/**
 * Get an array of all elements that seem to be dropdowns.
 *
 * @returns {Element[]} All the potential dropdowns in this document.
 */
function getInstances() {
	const dropdowns = document.querySelectorAll( '[data-dropdown]' );
	if ( dropdowns && dropdowns.length > 0 ) {
		return Array.from( dropdowns );
	}

	return [];
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
		}
	} );
}

/**
 * Add dropdown functionality to a specific element.
 *
 * @param {Element} el The element to upgrade and instantiate.
 *
 * @returns {Element} Upgraded and instantiated element.
 */
function instantiate( el ) {
	const name = el.dataset.dropdown;
	const content = el.querySelector( '.dropdown__content' );
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
}

/**
 * Remove functionality from all dropdowns on page.
 *
 * @returns {void}
 */
function teardown() {
	getInstances().map( destroy );
}

export default initialize;
export {
	teardown, setup,
};
