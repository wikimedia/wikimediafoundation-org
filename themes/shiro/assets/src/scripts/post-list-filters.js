/**
 * Toggles the post list filter container.
 *
 * @param {HTMLElement} filterButton - The button used to toggle the filter container.
 * @param {HTMLElement} filterContainer - The filter container.
 */
const addFilterToggleHandler = ( filterButton, filterContainer ) => {
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
 * Resets the date filters.
 *
 * @param {HTMLElement} fromDateInput - The input element for the "from" date.
 * @param {HTMLElement} toDateInput - The input element for the "to" date.
 */
const resetDateFilters = ( fromDateInput, toDateInput ) => {
	fromDateInput.value = '';
	toDateInput.value = '';
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
	inputs.forEach( input => {
		input.value = '';
	} );

	// Reset all category checkboxes.
	const checkboxes = form.querySelectorAll( 'input[type="checkbox"]' );
	checkboxes.forEach( checkbox => {
		checkbox.checked = false;
	} );
};

/**
 * Initializes the post list filters functionality.
 */
const initializePostListFilters = () => {

	// Controls filters container visibility toggle.
	const filterButton = document.querySelector( '.post-list-filter__toggle' );
	const filterContainer = document.querySelector( '.post-list-filter__container' );
	addFilterToggleHandler( filterButton, filterContainer );

	// Controls date filters reset.
	const resetDateFiltersButton = document.getElementById( 'button-reset-date-filters' );
	const fromDateInput = document.querySelector( 'input[name="date_from"]' );
	const toDateInput = document.querySelector( 'input[name="date_to"]' );
	resetDateFiltersButton.addEventListener( 'click', () => {
		resetDateFilters( fromDateInput, toDateInput );
	} );

	// Controls form reset.
	const form = document.querySelector( '.post-list-filter__form' );
	const resetFormButton = filterContainer.querySelector( '#button-clear-filters' );
	resetFormButton.addEventListener( 'click', () => {
		resetFormFields( form );
		form.submit();
	} );

};

document.addEventListener( 'DOMContentLoaded', initializePostListFilters );
