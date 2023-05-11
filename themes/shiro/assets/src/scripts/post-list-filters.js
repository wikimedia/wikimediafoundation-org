/**
 * Toggles the post list filter container.
 *
 * @param {HTMLElement} filterButton - The button used to toggle the filter container.
 * @param {HTMLElement} filterContainer - The filter container.
 */
const toggleFilterContainer = ( filterButton, filterContainer ) => {
	/**
	 * Click event handler for the filter button.
	 */
	const clickHandler = () => {
	  const isHidden = filterContainer.style.display === 'none' || filterContainer.style.display === '';
	  filterContainer.style.display = isHidden ? 'flex' : 'none';

	  const buttonLabel = filterButton.getAttribute( 'show-filters-applied-button-label' );
	  filterButton.innerHTML = isHidden ? 'Hide filters' : buttonLabel;
	};

	filterButton.addEventListener( 'click', clickHandler );
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

	// Reset all inputs on form.
	const inputs = form.querySelectorAll( 'input[type="text"], input[type="date"]' );
	inputs.forEach( input => {
		input.value = '';
	} );

	// Reset all checkboxes on form.
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
	filterButton.setAttribute( 'show-filters-applied-button-label', filterButton.innerHTML );
	const filterContainer = document.querySelector( '.post-list-filter__container' );
	toggleFilterContainer( filterButton, filterContainer );

	// Controls date filters reset.
	const resetDateFiltersButton = document.querySelector( '.button-reset-date-filters' );
	const fromDateInput = document.querySelector( 'input[name="date_from"]' );
	const toDateInput = document.querySelector( 'input[name="date_to"]' );
	resetDateFiltersButton.addEventListener( 'click', () => {
		resetDateFilters( fromDateInput, toDateInput );
	} );

	// Controls form reset.
	const resetFormButton = document.querySelector( '.button-clear-filters' );
	const form = document.querySelector( '.post-list-filter__form' );
	resetFormButton.addEventListener( 'click', () => {
		resetFormFields( form );
		form.submit();
	} );

};

document.addEventListener( 'DOMContentLoaded', initializePostListFilters );
