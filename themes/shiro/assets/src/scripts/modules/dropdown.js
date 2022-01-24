import initialize from '../util/initialize';

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
			visibleChange( record.target );
			break;
		case 'data-backdrop':
			backdropChange( record.target );
			break;
		case 'data-trap':
			trapChange( record.target );
			break;
		case 'data-toggleable':
			toggleableChange( record.target );
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
 * @param {Node} dropdown The dropdown wrapper
 */
function handleVisibleChange( dropdown ) {
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
 * @param {Node} dropdown The dropdown wrapper
 */
function handleBackdropChange( dropdown ) {
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
 * @param {Node} dropdown The dropdown wrapper
 */
function handleTrapChange( dropdown ) {
	if ( dropdown.dataset.trap === 'active' ) {
		activateTabSkip( dropdown );
		dropdown.addEventListener(
			'keydown',
			dropdown.dropdown.handlers.keydown
		);
	} else {
		deactivateTabSkip( dropdown );
		dropdown.removeEventListener(
			'keydown',
			dropdown.dropdown.handlers.keydown
		);
	}
}

/**
 * Do any actions required by changes to data-toggleable.
 *
 * @param {HTMLElement} dropdown Emitted by a MutationObserver
 */
function handleToggleableChange( dropdown ) {
	if ( dropdown.dataset.toggleable === 'no' ) {
		dropdown.dropdown.toggle.disabled = true;

		if ( dropdown.classList.contains( 'menu-item' ) ) {
			// Only set subnavs to visible if they are the active section.
			const _is_current =
				dropdown.classList.contains( 'current-menu-item' ) ||
				dropdown.classList.contains( 'current-menu-ancestor' );
			dropdown.dataset.visible = _is_current ? 'yes' : 'no';
		} else {
			// If the dropdown can't be toggled, we should always show it
			dropdown.dataset.visible = 'yes';
		}
	} else {
		dropdown.dropdown.toggle.removeAttribute( 'disabled' );
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
		const { toggle } = dropdown.dropdown;
		const { first, last } = dropdown.dropdown.getFocusable();
		let isTabPressed = e.key === 'Tab' || e.keyCode === 9;
		let isEscPressed = e.key === 'Escape' || e.keyCode === 27;

		if ( ! isTabPressed && ! isEscPressed ) {
			return;
		}

		if ( isEscPressed ) {
			dropdown.dataset.visible = 'no';
			if ( toggle ) {
				/**
				 * If we don't return focus to the toggle, then our focused element
				 * is inside of a hidden element, which seems...bad. If the toggle
				 * doesn't exist, then the developer will need to handle this action
				 * another way.
				 */
				toggle.focus();
			}
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
				// if focus has reached to last focusable element then focus first focusable element after pressing tab
				first.focus(); // add focus for the first focusable element
				e.preventDefault();
			}
		}
	};
}

/**
 * Return all focusable elements a DOM element.
 *
 * @param {Element} element A DOM element
 * @returns {Array} All potentially focusable elements in the element
 */
function getFocusableInside( element ) {
	return Array.from(
		element.querySelectorAll(
			'a:not([disabled]), button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), details:not([disabled]), [tabindex]:not([tabindex="-1"]:not([disabled]))'
		)
	);
}

/**
 * Build an object containing the DOM elements needed for tab trapping.
 *
 * This calculates the "allowed" items for tab navigation, as well as
 * determining the first and last items in the resulting list. Use this
 * function when setting or changing dropdown.focusable.
 *
 * @param {Element[]} all Every focusable element available
 * @param {Element[]} [skip=[]] The elements (if any) to skip
 * @returns {{last, allowed, skip, first}} Compiled object with DOM element lists
 */
function calculateFocusableElements( all, skip = [] ) {
	const allowed = all.filter( el => ! skip.includes( el ) );
	return {
		first: allowed[ 0 ],
		last: allowed[ allowed.length - 1 ],
		all,
		allowed,
		skip,
	};
}

/**
 * Remove an element from the tab order.
 *
 * @param {HTMLElement} element The element
 */
function setSkipElement( element ) {
	if ( element.hasAttribute( 'tabindex' ) ) {
		element.dataset.tabindex = element.tabindex;
	}
	element.tabindex = -1;
}

/**
 * Return an element to the tab order.
 *
 * @param {HTMLElement} element The element
 */
function resetSkipElement( element ) {
	if ( element.dataset.tabindex ) {
		element.tabindex = element.dataset.tabindex;
	} else {
		element.removeAttribute( 'tabindex' );
	}
}

/**
 * Remove a dropdown's to-skip elements from tab order.
 *
 * @param {HTMLElement} dropdown A dropdown wrapper
 */
function activateTabSkip( dropdown ) {
	const { skip } = dropdown.dropdown.getFocusable();
	if ( skip.length > 0 ) {
		skip.forEach( setSkipElement );
	}
}

/**
 * Restore a dropdown's to-skip elements to tab order.
 *
 * @param {HTMLElement} dropdown A dropdown wrapper
 */
function deactivateTabSkip( dropdown ) {
	const { skip } = dropdown.dropdown.getFocusable();
	if ( skip.length > 0 ) {
		skip.forEach( resetSkipElement );
	}
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
	// Sometimes we want toggles hidden until instantiation; this keeps that from triggering IntersectionObserver logic
	// when the dropdown initializes.
	if (
		element.dataset.toggleable === 'yes' &&
		toggle &&
		toggle.hasAttribute( 'hidden' )
	) {
		toggle.removeAttribute( 'hidden' );
	}
	const observer = observeMutations( element );

	/**
	 * By default the dropdown skips nothing. If you want to skip some elements
	 * that show up inside the wrapper, you'll need to manually add them to
	 * dropdown.focusable at runtime.
	 *
	 * @type {{last, allowed, skip, first}}
	 */
	const getFocusable = () =>
		calculateFocusableElements( getFocusableInside( element ) );

	element.dropdown = {
		content,
		toggle,
		observer,
		getFocusable,
		handlers: {
			backdropChange: handleBackdropChange,
			toggleableChange: handleToggleableChange,
			trapChange: handleTrapChange,
			visibleChange: handleVisibleChange,
			keydown: buildHandleKeydown( element ),
			toggleClick: buildHandleToggleClick( element ),
		},
	};

	if ( toggle ) {
		toggle.addEventListener(
			'click',
			element.dropdown.handlers.toggleClick
		);
	}
	if ( _backdrop ) {
		_backdrop.addEventListener( 'click', handleBackdropClick );
	}

	// Allow for styling based on the existence of this functionality
	element.dataset.dropdownStatus = 'initialized';
}

/**
 * Activate all dropdown instances on the page.
 */
function setup() {
	_instances.map( initializeDropdown );
}

/**
 * Deactivate and clean up all dropdown instances.
 */
function teardown() {
	_instances.forEach( instance => {
		const { dropdown } = instance;
		dropdown.observer.disconnect();
		dropdown.toggle.removeEventListener(
			'click',
			dropdown.handlers.toggleClick
		);
		delete instance.dropdown;
	} );

	if ( _backdrop ) {
		_backdrop.removeEventListener( 'click', handleBackdropClick );
		_backdrop.dataset.dropdownBackdrop = 'inactive';
		_backdrop.dataset.activeDropdowns = '';
	}
}

export default initialize( setup, teardown );
export {
	_backdrop as backdrop,
	_instances as instances,
	handleTrapChange,
	handleVisibleChange,
	handleToggleableChange,
	handleBackdropChange,
	getFocusableInside,
	calculateFocusableElements,
};
