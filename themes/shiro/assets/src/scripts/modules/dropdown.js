/**
 * The shared backdrop element.
 *
 * @type {Element}
 * @private
 */
const _backdrop = document.querySelector( '[data-dropdown-backdrop]' );
/**
 * A collection of all dropdowns on the page.
 *
 * @type {Element[]}
 * @private
 */
const _instances = [ ...document.querySelectorAll( '[data-dropdown]' ) ];

/**
 * Options to be passed to the MutationObserver.
 *
 * The comments next to each attribute reflect the possible values it might
 * have.
 *
 * @type {MutationObserverInit}
 */
const mutationObserverOptions = {
	attributeFilter: [
		'data-visible', // 'yes'/'no'
		'data-backdrop', // 'active'/'inactive'
		'data-trap', // 'active'/'inactive'
		'data-toggleable', // 'yes'/'no'
	],
	attributeOldValue: true,
};

/**
 * Dispatch MutationRecord to appropriate handler function.
 *
 * @param {MutationRecord} record Emitted by a MutationObserver
 */
function processMutationRecord( record ) {
	const { target } = record;

	if ( ! target.dropdown ) {
		// This isn't an actual dropdown
		return;
	}

	/**
	 * Because all of these handlers are defined on the DOM element, they can
	 * be dynamically replaced, i.e.:
	 * _dropdown.dropdown.handlers.visibleChange = () => console.log('do nothing')
	 */
	const {
		visibleChange,
		backdropChange,
		trapChange,
		toggleableChange,
	} = target.dropdown.handlers;

	switch ( record.attributeName ) {
		case 'data-visible':
			visibleChange( record );
			break;
		case 'data-backdrop':
			backdropChange( record );
			break;
		case 'data-trap':
			trapChange( record );
			break;
		case 'data-toggleable':
			toggleableChange( record );
			break;
		default:
			break;
	}
}

/**
 * Iterate over the list of MutationRecords and pass them to processor.
 *
 * @param {MutationRecord[]} list Collection of records from observed event
 */
function handleMutation( list ) {
	list.forEach( processMutationRecord );
}

/**
 * Begin observing element for specific data-attribute mutations.
 *
 * @param {Element} dropdown The wrapper element for the dropdown
 * @returns {MutationObserver} The observer watching element
 */
function observeMutations( dropdown ) {
	const observer = new MutationObserver( handleMutation );
	observer.observe( dropdown, mutationObserverOptions );
	return observer;
}

/**
 * Do any actions required by changes to data-visible.
 *
 * @param {MutationRecord} record Emitted by a MutationObserver
 */
function handleVisibleChange( record ) {
	const dropdown = record.target;
	const {
		content: contentElement,
		toggle: toggleElement,
	} = dropdown.dropdown;
	const { toggleable, visible } = dropdown.dataset;
	const contentIsVisible = visible === 'yes';

	// Handle content visibility
	if ( contentIsVisible ) {
		contentElement.removeAttribute( 'hidden' );
	} else {
		contentElement.hidden = true;
	}

	// Handle toggle aria
	if ( toggleable === 'yes' ) {
		toggleElement.setAttribute(
			'aria-expanded',
			contentIsVisible ? 'true' : 'false'
		);
	}

	// Handle tab trap
	if ( contentIsVisible && toggleable === 'yes' ) {
		dropdown.dataset.trap = 'active';
	} else {
		dropdown.dataset.trap = 'inactive';
	}

	// Handle backdrop change
	dropdown.dataset.backdrop =
		contentIsVisible && toggleable === 'yes' ? 'active' : 'inactive';
}

/**
 * Do any actions required by changes to data-backdrop.
 *
 * @param {MutationRecord} record Emitted by a MutationObserver
 */
function handleBackdropChange( record ) {
	if ( ! _backdrop ) {
		return;
	}

	const activeDropdowns = _instances
		.filter( instance => instance.dataset.backdrop === 'active' )
		.map( instance => instance.dataset.dropdown );

	_backdrop.dataset.activeDropdowns = activeDropdowns.join( ' ' );
	_backdrop.dataset.dropdownBackdrop =
		activeDropdowns.length < 1 ? 'inactive' : 'active';
}

/**
 * Do any actions require by changes to data-trap.
 *
 * @param {MutationRecord} record Emitted by a MutationObserver
 */
function handleTrapChange( record ) {
	const el = record.target;
	if ( el.dataset.trap === 'active' ) {
		el.addEventListener( 'keydown', el.dropdown.handlers.keydown );
	} else {
		el.removeEventListener( 'keydown', el.dropdown.handlers.keydown );
	}
}

/**
 * Do any actions required by changes to data-toggleable.
 *
 * @param {MutationRecord} record Emitted by a MutationObserver
 */
function handleToggleableChange( record ) {
	const el = record.target;
	if ( el.dataset.toggleable === 'no' ) {
		el.dropdown.toggle.disabled = true;
		// If the dropdown can't be toggled, we should always show it
		el.dataset.visible = 'yes';
	} else {
		el.dropdown.toggle.removeAttribute( 'disabled' );
	}
}

/**
 * Close all dropdowns with an active backdrop.
 */
function handleBackdropClick() {
	_instances
		.filter( instance => instance.dataset.backdrop === 'active' )
		.map( instance => ( instance.dataset.visible = 'no' ) );
}

/**
 * Create a function specific to the passed dropdown to fire when the toggle is clicked.
 *
 * @param {Element} dropdown The wrapper element for a dropdown.
 * @returns {Function} To be executed when the toggle is clicked.
 */
function buildHandleToggleClick( dropdown ) {
	return e => {
		dropdown.dataset.visible =
			dropdown.dataset.visible === 'yes' ? 'no' : 'yes';
	};
}

/**
 * Create a function specific to the passed dropdown to fire when keys are pressed.
 *
 * It does the following:
 * - Tabbing (or shift-tabbing) pst the first (or last) element loops around.
 * - Pressing "ESC" hides the dropdown
 *
 * This should not be active when the menu is hidden, *or* when the menu is
 * visible but not toggleable.
 *
 * @param {Element} dropdown The wrapper element for a dropdown.
 * @returns {Function} To be executed when the keydown event occurs.
 */
function buildHandleKeydown( dropdown ) {
	return e => {
		const { first, last } = dropdown.dropdown.focusable;
		let isTabPressed = e.key === 'Tab' || e.keyCode === 9;
		let isEscPressed = e.key === 'Escape' || e.keyCode === 27;

		if ( ! isTabPressed && ! isEscPressed ) {
			return;
		}

		if ( isEscPressed ) {
			dropdown.dataset.visible = 'no';
			return;
		}

		if ( e.shiftKey ) {
			// if shift key pressed for shift + tab combination
			if ( document.activeElement === first ) {
				last.focus(); // add focus for the last focusable element
				e.preventDefault();
			}
		} else {
			// if tab key is pressed
			if ( document.activeElement === last ) {
				// if focused has reached to last focusable element then focus first focusable element after pressing tab
				first.focus(); // add focus for the first focusable element
				e.preventDefault();
			}
		}
	};
}

/**
 * Return all focusable elements in dropdown wrapper.
 *
 * @param {Element} dropdown The wrapper for a dropdown
 * @returns {Array} All potentially focusable elements in the dropdown
 */
function getFocusable( dropdown ) {
	return Array.from(
		dropdown.querySelectorAll(
			'a, button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
		)
	);
}

/**
 * Set up the dropdown that this element contains.
 *
 * @param {HTMLElement} element The wrapper for a dropdown
 */
function initializeDropdown( element ) {
	const { dropdownContent, dropdownToggle } = element.dataset;

	const content = element.querySelector( dropdownContent );
	const toggle = element.querySelector( dropdownToggle );
	const observer = observeMutations( element );
	const focusable = getFocusable( element );

	// Toggle should always be first focusable item
	if ( focusable[ 0 ] !== toggle ) {
		focusable.unshift( toggle );
	}

	element.dropdown = {
		content,
		toggle,
		observer,
		focusable: {
			first: focusable[ 0 ],
			last: focusable[ focusable.length - 1 ],
			all: focusable,
		},
		handlers: {
			backdropChange: handleBackdropChange,
			toggleableChange: handleToggleableChange,
			trapChange: handleTrapChange,
			visibleChange: handleVisibleChange,
			keydown: buildHandleKeydown( element ),
			toggleClick: buildHandleToggleClick( element ),
		},
	};

	toggle.addEventListener( 'click', element.dropdown.handlers.toggleClick );
	if ( _backdrop ) {
		_backdrop.addEventListener( 'click', handleBackdropClick );
	}
}

/**
 * Activate all dropdown instances on the page.
 */
function setup() {
	_instances.map( initializeDropdown );
}

export default setup;
export {
	_backdrop as backdrop,
	_instances as instances,
	handleTrapChange,
	handleVisibleChange,
	handleToggleableChange,
	handleBackdropChange,
};
