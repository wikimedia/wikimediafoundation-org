/**
 * @returns Element[]
 */
function getInstances() {
	const dropdowns = document.querySelectorAll( '[data-dropdown]' );
	if ( dropdowns && dropdowns.length > 0 ) {
		return Array.from( dropdowns );
	}

	return [];
}

/**
 * @param list
 * @param observer
 */
function observe( list, observer ) {
	list.forEach( r => {
		const el = r.target;
		const { content } = el.dropdown;
		if ( el.dataset.open === 'true' ) {
			content.classList.add( 'dropdown__content--open' );
		} else {
			content.classList.remove( 'dropdown__content--open' );
		}
	} );
}

/**
 * @param el
 */
function intialize( el ) {
	const name = el.dataset.dropdown;
	const content = el.querySelector( '.dropdown__content' );
	const toggles = Array.from( document.querySelectorAll( `[data-dropdown-toggle='${name}']` ) );
	/**
	 * Handle toggling the dropdown.
	 */
	const toggleAction = () => {
		el.dataset.open = String( el.dataset.open !== 'true' );
	};

	toggles.map( toggle => {
		toggle.addEventListener( 'click', toggleAction );

		return toggle;
	} );

	const observer = new MutationObserver( observe );
	observer.observe( el, {
		attributes: true,
		childList: false,
		subtree: false,
	} );

	el.dropdown = {
		name: name,
		content: content,
		toggles: toggles,
		observer: observer,
		toggleAction: toggleAction,
	};

	return el;
}

/**
 * @param el
 */
function destroy( el ) {
	if ( el.dropdown ) {
		// Stop watching mutations
		el.dropdown.observer.disconnect();
		// Remove all event listeners
		el.dropdown.toggles.map( toggle => {
			toggle.removeEventListener( 'click', el.dropdown.toggleAction );
		} );
		// Remove all data
		el.dropdown = null;
	}

	return el;
}

/**
 * @returns void
 */
function setup() {
	const dropdowns = getInstances();
	dropdowns.map( intialize );
}

/**
 *
 */
function teardown() {
	const dropdowns = getInstances();
	dropdowns.map( destroy );
}

export default setup;
export { teardown };
