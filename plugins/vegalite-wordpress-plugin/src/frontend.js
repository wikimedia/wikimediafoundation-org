/**
 * Front-end functionality for vega-lite blocks.
 */
import vegaEmbed from 'vega-embed';

import './styles.scss';

/**
 * A collection of all vega-lite blocks on the page.
 *
 * @type {Element[]}
 * @private
 */
let _instances = [];

/**
 * Entry function to initialize all vega-lite blocks to render the blocks datavis model.
 */
function setupDatavisBlocks() {
	// Get all vega-lite block ids on the page.
	_instances = [ ...document.querySelectorAll( '[data-datavis]' ) ];
	renderAllBlocks();
}

/**
 * Render charts, called on load and periodically as page dimensions change.
 */
function renderAllBlocks() {
	_instances.map( initializeDatavisBlock );
}

/**
 * Callback to initialize a vega-lite block to render its model.
 *
 * @param {Element} element Vega-Lite block element.
 */
function initializeDatavisBlock( element ) {
	const config = element.dataset.config;
	const datavis = element.dataset.datavis;

	if ( ! config || ! datavis ) {
		return;
	}

	const jsonElement = document.getElementById( config );
	if ( ! jsonElement ) {
		return;
	}

	// Handle responsive breakpoint values if present.
	const minWidth = element.dataset.minWidth;
	const maxWidth = element.dataset.maxWidth;
	const { width } = element.getBoundingClientRect();
	const doNotRender = ! ! ( ( minWidth && width < minWidth ) || ( maxWidth && maxWidth <= width ) );
	element.classList.toggle( 'chart-hidden', doNotRender );
	if ( doNotRender ) {
		return;
	}

	// Render if possible and necessary.
	if ( typeof vegaEmbed === 'function' && ! jsonElement.classList.contains( 'vega-embed' ) ) {
		vegaEmbed(
			document.getElementById( element.dataset.datavis ),
			JSON.parse( jsonElement.textContent ),
			{ actions: false }
		);
	}
}

// Kick things off after load.
window.addEventListener( 'load', setupDatavisBlocks );

// Listen to window.resize events and re-render graphs with a (debounced)
// callback to update which responsive variants are shown.
let timeout = false;
window.addEventListener( 'resize', function() {
	// clear the timeout
	clearTimeout( timeout );
	// start timing for event "completion"
	timeout = setTimeout( renderAllBlocks, 200 );
} );
