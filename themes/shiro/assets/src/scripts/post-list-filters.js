/**
 * Set up event handlers to toggle the News section post list filter container, when present.
 *
 * @param {HTMLElement|null} filterContainer The node containing the filter form.
 */
const addFilterToggleHandler = filterContainer => {
	const filterButton = document.querySelector( '.post-list-filter__toggle' );

	if ( ! filterButton || ! filterContainer ) {
		// No filter controls available.
		return;
	}

	/**
	 * Click event handler for the filter button.
	 */
	const toggleFilterClickHandler = () => {
		filterContainer.classList.toggle( 'post-list-filter__container--open' );
		filterButton.classList.toggle( 'post-list-filter__toggle--open' );
	};

	filterButton.addEventListener( 'click', toggleFilterClickHandler );
};

/**
 * Set up event handlers for the News section date filter interface, when present.
 */
const addDateFilterHandlers = () => {
	const resetDateFiltersButton = document.getElementById( 'button-reset-date-filters' );
	const fromDateInput = document.querySelector( 'input[name="date_from"]' );
	const toDateInput = document.querySelector( 'input[name="date_to"]' );

	if ( ! resetDateFiltersButton ) {
		return;
	}

	resetDateFiltersButton.addEventListener( 'click', () => {
		resetDateFilters( fromDateInput, toDateInput );
	} );
};

/**
 * Resets the date filters.
 *
 * @param {HTMLElement} fromDateInput - The input element for the "from" date.
 * @param {HTMLElement} toDateInput - The input element for the "to" date.
 */
const resetDateFilters = ( fromDateInput, toDateInput ) => {
	if ( fromDateInput ) {
		fromDateInput.value = '';
	}
	if ( toDateInput ) {
		toDateInput.value = '';
	}
};

/**
 * Set up event handlers for post list filtering form submission and reset behavior.
 *
 * @param {HTMLElement|null} filterContainer The node containing the filter form.
 */
const addFormHandlers = filterContainer => {
	const form = document.querySelector( '.post-list-filter__form' );
	const resetFormButton = filterContainer.querySelector( '#button-clear-filters' );
	resetFormButton.addEventListener( 'click', () => {
		resetFormFields( form );
		form.submit();
	} );
};

/**
 * Walk through the form elements and reset them.
 *
 * @param {HTMLElement} form - The form element.
 * @returns {void}
 */
const resetFormFields = form => {
	// Reset search and date inputs.
	const inputs = form.querySelectorAll( 'input[type="text"], input[type="date"]' );
	if ( inputs ) {
		inputs.forEach( input => {
			input.value = '';
		} );
	}

	// Reset all category checkboxes.
	const checkboxes = form.querySelectorAll( 'input[type="checkbox"]' );
	if ( checkboxes ) {
		checkboxes.forEach( checkbox => {
			checkbox.checked = false;
		} );
	}
};

/**
 * Initializes the post list filters functionality.
 */
const initializePostListFilters = () => {
	const filterContainer = document.querySelector( '.post-list-filter__container' );

	if ( filterContainer ) {
		// The filtering UI is present: set up event handling.

		// Controls filters container visibility toggle.
		addFilterToggleHandler( filterContainer );

		// Controls date filters reset.
		addDateFilterHandlers();

		// Controls form submission and reset.
		addFormHandlers( filterContainer );
	}
};

document.addEventListener( 'DOMContentLoaded', initializePostListFilters );
