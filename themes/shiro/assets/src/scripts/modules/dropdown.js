/**
 *
 * @type {Element}
 * @private
 */
const _backdrop = document.querySelector( '[data-dropdown-backdrop]' );
/**
 *
 * @type {Element[]}
 * @private
 */
const _instances = [ ...document.querySelectorAll( '[data-dropdown]' ) ];

/**
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
 *
 * @param {MutationRecord} record
 */
function processMutationRecord( record ) {
	const { target } = record;

	if ( ! target.dropdown ) {
		// This isn't an actual dropdown
		return;
	}

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
 *
 * @param {MutationRecord[]} list
 * @param {MutationObserver} observer
 */
function handleMutation( list, observer ) {
	list.forEach( processMutationRecord );
}

/**
 *
 * @param {Element} element
 */
function observeMutations( element ) {
	const observer = new MutationObserver( handleMutation );
	observer.observe( element, mutationObserverOptions );
	return observer;
}

/**
 *
 * @param {MutationRecord} record
 */
function handleVisibleChange( record ) {
	const el = record.target;
	const { content: contentElement, toggle: toggleElement } = el.dropdown;
	const { toggleable, visible } = el.dataset;
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
		el.dataset.trap = 'active';
	} else {
		el.dataset.trap = 'inactive';
	}

	// Handle backdrop change
	el.dataset.backdrop = contentIsVisible && toggleable === 'yes' ? 'active' : 'inactive';
}

/**
 *
 * @param {MutationRecord} record
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
 *
 * @param {MutationRecord} record
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
 *
 * @param {MutationRecord} record
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
 *
 * @param {Element} wrapper
 * @return {Function}
 */
function buildHandleToggleClick( wrapper ) {
	return e => {
		wrapper.dataset.visible =
			wrapper.dataset.visible === 'yes' ? 'no' : 'yes';
	};
}

/**
 *
 * @param {Element} wrapper
 * @return {(function(*): void)|*}
 */
function buildHandleKeydown( wrapper ) {
	return e => {
		const { first, last } = wrapper.dropdown.focusable;
		let isTabPressed = e.key === 'Tab' || e.keyCode === 9;
		let isEscPressed = e.key === 'Escape' || e.keyCode === 27;

		if ( ! isTabPressed && ! isEscPressed ) {
			return;
		}

		if ( isEscPressed ) {
			wrapper.dataset.visible = 'no';
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
 *
 * @param {Element} wrapper
 * @return {array}
 */
function getFocusable( wrapper ) {
	return Array.from(
		wrapper.querySelectorAll(
			'a, button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
		)
	);
}

/**
 *
 * @param {HTMLElement} element
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
 *
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
