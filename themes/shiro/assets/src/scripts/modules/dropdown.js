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
			// Set visibility of content
			content.hidden = open !== 'true';

			// Set aria attributes
			toggle.setAttribute( 'aria-expanded', open );

			// Capture or release tab
			if ( open === 'true' ) {
				enterContent( el );
			} else {
				exitContent( el );
			}

			/**
			 * Make it easier to hook custom code into the observer.
			 */
			if ( customHandler ) {
				customHandler( r );
			}

			const backdrop = getBackdrop();
			/**
			 * Track which dropdowns have opened, and close the backdrop when
			 * there are no open dropdowns.
			 */
			if ( backdrop ) {
				const activeDropdownsArray = backdrop.dataset.activeDropdowns ? backdrop.dataset.activeDropdowns.split( ' ' ) : [];
				const activeDropdowns = new Set( activeDropdownsArray );
				const name = el.dataset.dropdown;

				if ( open === 'true' ) {
					activeDropdowns.add( name );
				} else {
					activeDropdowns.delete( name );
				}

				backdrop.dataset.dropdownBackdrop = activeDropdowns.size < 1 ? 'inactive' : 'active';
				backdrop.dataset.activeDropdowns = Array.from( activeDropdowns.values() ).join( ' ' );
			}
		}
	} );
}

/**
 * Returns a function suitable for attachment to the 'keydown' event.
 *
 * @param {Element} el The dropdown wrapper
 * @returns {Function} The function to attach
 */
function keywatcher( el ) {
	return e => {
		const {
			first,
			last,
		} = el.dropdown.focusable;
		let isTabPressed = e.key === 'Tab' || e.keyCode === 9;
		let isEscPressed = e.key === 'Escape' || e.keyCode === 27;

		if ( ! isTabPressed && ! isEscPressed ) {
			return;
		}

		if ( isEscPressed ) {
			el.dataset.open = 'false';
			return;
		}

		if ( e.shiftKey ) { // if shift key pressed for shift + tab combination
			if ( document.activeElement === first ) {
				last.focus(); // add focus for the last focusable element
				e.preventDefault();
			}
		} else { // if tab key is pressed
			if ( document.activeElement === last ) { // if focused has reached to last focusable element then focus first focusable element after pressing tab
				first.focus(); // add focus for the first focusable element
				e.preventDefault();
			}
		}
	};
}

/**
 * Adds special "in-dropdown" accessibility logic and moves focus to first focusable element in content.
 *
 * @param {Element} el The dropdown wrapper
 */
function enterContent( el ) {
	const { skip, all } = el.dropdown.focusable;
	/**
	 * To prevent tabbing to elements in the 'tab path' that we don't want to
	 * be accessible when the dropdown is open, we temporarily set their
	 * tabindex to -1. This is later reverted by `exitContent()`.
	 */
	skip.forEach( toSkip => {
		if ( toSkip.tabIndex ) {
			toSkip.dataset.tabindex = toSkip.tabIndex;
		}
		toSkip.tabIndex = -1;
	} );
	document.addEventListener( 'keydown', keywatcher( el ) );
	// Focus the first element, not the toggle
	all[1].focus();
}

/**
 * Removes special "in-dropdown" accessibility logic and returns focus to toggle.
 *
 * @param  {Element} el The dropdown wrapper
 */
function exitContent( el ) {
	const { toggle } = el.dropdown;
	const { skip } = el.dropdown.focusable;
	document.removeEventListener( 'keydown', keywatcher( el ) );
	/**
	 * Resets (or removes) and tabindex that were modified by
	 * `enterContent()` to remove them from the 'tab path'.
	 */
	skip.forEach( toSkip => {
		if ( toSkip.dataset.tabindex ) {
			toSkip.tabIndex = toSkip.dataset.tabindex;
		} else {
			toSkip.removeAttribute( 'tabindex' );
		}
	} );
	toggle.focus();
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
		attributeFilter: [ 'data-open' ],
	} );

	const typeCanBeFocused = 'a, button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])';
	const allFocusable = Array.from( el.querySelectorAll( typeCanBeFocused ) );
	const allow = Array.from( content.querySelectorAll( typeCanBeFocused ) );
	// The toggle should *always* be accessible
	allow.unshift( toggle );
	// Get elements that would normally be in the 'tab path' but which we want
	// to skip when tabbing through the element
	const skip = allFocusable.filter( potentiallySkippable => {
		return ! allow.includes( potentiallySkippable );
	} );
	const focusable = {
		first: allow[0],
		last: allow[allow.length - 1],
		all: allow,
		skip,
	};

	el.dropdown = {
		name,
		content,
		toggle,
		observer,
		focusable,
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
		// Remove any content modifications
		exitContent( el );
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
	exitContent,
	enterContent,
};
