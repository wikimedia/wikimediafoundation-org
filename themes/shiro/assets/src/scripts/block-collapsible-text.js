/**
 * Expand/collapse functionality for the shiro/collapsible-text block.
 */

/**
 * Handle a click event on the collapsible area's toggle button.
 *
 * @param {Event} Click event.
 */
const toggleCollapsibleArea = ( { currentTarget } ) => {
	const expanded = currentTarget.closest( '.collapsible-text' ).classList.toggle( 'expanded' );
	if ( ! expanded ) {
		currentTarget.scrollIntoView( { block: 'center' } );
	}
};

/**
 * Attach listeners to any collapsible area triggers.
 *
 * @returns {HTMLElement[]} All toggle buttons on the current page.
 */
const initializeCollapsibleTextBlocks = () => {
	[ ...document.querySelectorAll( '.collapsible-text__toggle' ) ].forEach(
		button => button.addEventListener( 'click', toggleCollapsibleArea )
	);
};

document.addEventListener( 'DOMContentLoaded', initializeCollapsibleTextBlocks );
