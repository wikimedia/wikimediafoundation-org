/**
 * Initialize the dropdown functionality for the search results.
 */
function initializeDropdownFunctionality() {
	const dropdown = document.querySelector( '.sort-dropdown' );
	const toggleButton = document.querySelector( '.search-results__tabs__sort button' );

	/**
	 * Checks if dropdown is visible.
	 *
	 * @returns {boolean} true if visible, false otherwise.
	 */
	function isDropdownVisible() {
		return dropdown.classList.contains( 'is-visible' );
	}

	/**
	 * Displays the dropdown and update aria.
	 */
	function showDropdown() {
		dropdown.classList.add( 'is-visible' );
		toggleButton.setAttribute( 'aria-expanded', 'true' );
	}

	/**
	 * Hides the dropdown and update aria.
	 */
	function hideDropdown() {
		dropdown.classList.remove( 'is-visible' );
		toggleButton.setAttribute( 'aria-expanded', 'false' );
	}

	toggleButton.addEventListener( 'click', function () {
		if ( isDropdownVisible() ) {
			hideDropdown();
		} else {
			showDropdown();
		}
	} );

	// Hide the dropdown if click happened outside.
	document.addEventListener( 'click', function ( event ) {
		// Check if the clicked element is the toggleButton or on any of the button descendants.
		const isToggleButtonOrChildren = toggleButton.contains( event.target ) || event.target === toggleButton;

		if ( ! dropdown.contains( event.target ) && ! isToggleButtonOrChildren ) {
			hideDropdown();
		}
	} );
}

document.addEventListener( 'DOMContentLoaded', initializeDropdownFunctionality );
