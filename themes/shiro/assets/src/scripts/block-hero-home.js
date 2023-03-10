/**
 * Functionality for rotating the rotating headings in the hero home
 */

const HEADER_TEXT_CYCLE_TIME = 3000;
const OPACITY_TRANSITION_TIME = 750;
const BROWSER_PAINT_WAIT = 20;

const heroes = [ ...document.querySelectorAll( '.hero-home' ) ];
const heroesNotRotating = heroes.filter( block => ! block.closest( '.shiro-carousel' ) );

/**
 * Setup variables for fading in and out.
 *
 * @param {HTMLElement} heroBlock Div representing the hero block to rotate within.
 * @returns {void}
 */
const startRotatingHeadings = heroBlock => {
	const targetLink = heroBlock.querySelector( '.hero-home__link' );

	// Don't animate if this element is focused already.
	if ( targetLink && targetLink === document.activeElement ) {
		heroBlock.rotateHeadingsTimeout = setTimeout( () => startRotatingHeadings( heroBlock ), HEADER_TEXT_CYCLE_TIME );
		return;
	}

	const headings = [ ...heroBlock.querySelectorAll( '.hero-home__heading' ) ];

	// Don't start rotating heading unless there's at least 2 to swap between.
	if ( headings.length < 2 ) {
		return;
	}

	let previousHeadingIndex;
	let currentHeadingIndex = headings.findIndex(
		heading => ! heading.classList.contains( 'hero-home__heading--transparent' )
	);

	previousHeadingIndex = currentHeadingIndex;
	currentHeadingIndex = ++currentHeadingIndex % headings.length;

	Object.assign(
		heroBlock,
		{
			headings,
			previousHeadingIndex,
			currentHeadingIndex,
			rotateHeadingsTimeout: null,
		}
	);

	if ( targetLink ) {
		const targetLinkScreenReaderText = targetLink.querySelector( '.screen-reader-text' );

		if ( targetLinkScreenReaderText ) {
			targetLinkScreenReaderText.textContent = headings[ currentHeadingIndex ].textContent;
		}
	}

	fadeOutPreviousHeading( heroBlock );
};

/**
 * Stop rotating the headings in a block.
 *
 * @param {HTMLElement} heroBlock Hero block div.
 */
const stopRotatingHeadings = heroBlock => {
	if ( heroBlock.rotateHeadingsTimeout ) {
		clearTimeout( heroBlock.rotateHeadingsTimeout );
	}
};

/**
 * Fade out previous heading.
 *
 * @param {HTMLElement} block Div representing the home-hero block.
 * @returns {void}
 */
function fadeOutPreviousHeading( block ) {
	const { headings, previousHeadingIndex } = block;

	headings[ previousHeadingIndex ].classList.add( 'hero-home__heading--transparent' );
	block.rotateHeadingsTimeout = setTimeout( () => fadeInCurrentHeading( block ), OPACITY_TRANSITION_TIME );
}

/**
 * Fade in the current heading and set display values.
 *
 * @param {HTMLElement} block Div representing the home-hero block.
 * @returns {void}
 */
function fadeInCurrentHeading( block ) {
	const {
		headings,
		previousHeadingIndex,
		currentHeadingIndex,
	} = block;

	headings[ previousHeadingIndex ].classList.add( 'hero-home__heading--hidden' );
	headings[ currentHeadingIndex ].classList.remove( 'hero-home__heading--hidden' );

	// Allow the browser to display the element with opacity 0 before setting it to 1.
	setTimeout( function () {
		headings[ currentHeadingIndex ].classList.remove( 'hero-home__heading--transparent' );
	}, BROWSER_PAINT_WAIT );

	// Setup the next cycle
	block.rotateHeadingsTimeout = setTimeout( () => startRotatingHeadings( block ), HEADER_TEXT_CYCLE_TIME );
}

/**
 * Begin rotating headings in a slide when it becomes visible.
 *
 * @param {object} Slide sub-component that is visible.
 * @param {HTMLElement} Slide.slide Element holding current slide.
 */
export function slideVisible( { slide } ) {
	startRotatingHeadings( slide );
}

/**
 * Stop rotating headings in a slide when it becomes hidden.
 *
 * @param {object} Slide sub-component that is visible.
 * @param {HTMLElement} Slide.slide Element holding current slide.
 */
export function slideHidden( { slide } ) {
	stopRotatingHeadings( slide );
}

/**
 * Bootstrap module functionality.
 */
const init = () => {
	if ( heroesNotRotating.length ) {
		heroesNotRotating.forEach( startRotatingHeadings );
	}
};

/**
 * Clear timeouts in preparation for hot module update.
 */
const dispose = () => {
	if ( heroes.length ) {
		heroes.forEach( stopRotatingHeadings );
	}
};

init();

export {
	startRotatingHeadings,
	stopRotatingHeadings,
};

if ( module.hot ) {
	module.hot.dispose( dispose );
	module.hot.accept();
	init();
}
