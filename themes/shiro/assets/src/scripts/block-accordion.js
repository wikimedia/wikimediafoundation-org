/**
 * Accordion Item open/close Triggers
 */

/**
 * Toggle an accordion item open or closed.
 *
 * @param {Event} e Click event.
 */
const toggleAccordionItem = e => {
	e.preventDefault();

	const parent = e.target.closest( '.accordion-item' );
	const wrapper = e.target.closest( '.accordion-wrapper' );
	const isExpanded = parent.getAttribute( 'aria-expanded' );

	closeAllAccordionItems( wrapper ); // closes any opened item.

	// Open items should have the empty string as the attribute value.
	parent.toggleAttribute( 'aria-expanded', isExpanded !== '' );
	parent.scrollIntoView( { block: 'center' } );
};

/**
 * Closes all opened items.
 *
 * @param {HTMLElement} wrapper Accordion wrapper div.
 */
const closeAllAccordionItems = wrapper => {
	[ ...wrapper.querySelectorAll( '.accordion-item' ) ].forEach(
		accordionItem => accordionItem.removeAttribute( 'aria-expanded' )
	);
};

/**
 * Add click handlers to accordion item titles.
 *
 * @param {Element} item The concerned element.
 */
const addAccordionToggleHandlers = item => {
	const button = item.querySelector( '.accordion-item__title' );
	button.addEventListener( 'click', toggleAccordionItem );
};

/**
 * Initialize Accordion functionality.
 *
 * @returns {void}
 */
const initializeAccordionItems = () => {
	// Hook in click events to each item.
	[ ...document.querySelectorAll( '.accordion-item' ) ].forEach( item => addAccordionToggleHandlers( item ) );
};

document.addEventListener( 'DOMContentLoaded', initializeAccordionItems );
