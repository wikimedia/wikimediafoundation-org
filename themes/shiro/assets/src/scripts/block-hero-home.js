/**
 * Functionality for rotating the rotating headings in the hero home
 */

/**
 * Bootstrap frontend functionality.
 *
 * - This file is loaded at the bottom of the body, so we don't need an onready.
 */
const NO_CYCLING_HEADING_COUNT = 1;
const HERO_IMAGE_CYCLE_TIME = 6000;
const HEADER_TEXT_CYCLE_TIME = 3000;
const OPACITY_TRANSITION_TIME = 750;
const BROWSER_PAINT_WAIT = 20;

const heroes = [ ...document.querySelectorAll( '.hero-home' ) ];
const rotatingHeroes = [ ...document.querySelectorAll( '.hero-home__rotator' ) ];
const heroesNotRotating = heroes.filter( block => ! block.closest( '.hero-home__rotator' ) );

/**
 * Start rotating the hero blocks inside a hero rotator block.
 *
 * If a hero rotator block has more than one hero in it, this will set the
 * first one as visible and set an interval to begin cycling through the other
 * images.
 *
 * @param {HTMLElement} heroRotatorBlock Hero rotator block.
 */
const startRotatingImages = heroRotatorBlock => {

	// Ensure there are child blocks to rotate through.
	const childBlocks = [ ...heroRotatorBlock.querySelectorAll( '.hero-home' ) ];
	const controls = heroRotatorBlock.querySelector( '.hero-home__controls' );

	if ( ! childBlocks.length ) {
		return;
	}

	// Start with only the first block visible.
	childBlocks.forEach( block => block.classList.remove( 'hero-home--current' ) );
	childBlocks[0].classList.add( 'hero-home--current' );

	if ( childBlocks.length > NO_CYCLING_HEADING_COUNT ) {

		if ( controls ) {
			controls.classList.add( 'hero-home__controls--active' );
		}

		controls.addEventListener( 'click', () => showNextImage( heroRotatorBlock ) );

		// Disabled: the original request was for autorotating images, but
		// client wanted to try navigation buttons instead. I suspect we'll
		// switch back, so I'm leaving the timeout code in place for now.
		//heroRotatorBlock.rotateImagesTimeout = setTimeout(
		//	() => showNextImage( heroRotatorBlock ),
		//	HERO_IMAGE_CYCLE_TIME
		//);
	}
};

/**
 * Show the next hero image in a rotating hero series.
 *
 * @param {HTMLElement} heroRotatorBlock Div representing the hero-rotator block.
 */
const showNextImage = heroRotatorBlock => {
	const currentImage = heroRotatorBlock.querySelector( '.hero-home--current' );
	const images = heroRotatorBlock.querySelectorAll( '.hero-home:not(.hero-home--current)' );
	const nextImage = images[ Math.floor( Math.random() * images.length ) ];

	// clear headings rotate interval on the slide to hide, start it on the new one.
	if ( currentImage.rotateHeadingsTimeout ) {
		clearTimeout( currentImage.rotateHeadingsTimeout );
	}
	currentImage.classList.remove( 'hero-home--current' );
	nextImage.classList.add( 'hero-home--current' );
	nextImage.rotateHeadingsTimeout = setTimeout(
		() => startRotatingHeadings( nextImage ),
		HEADER_TEXT_CYCLE_TIME
	);

	// Set timer for the next iteration of this cycle.
	//heroRotatorBlock.rotateImagesTimeout = setTimeout(
	//	() => showNextImage( heroRotatorBlock ),
	//	HERO_IMAGE_CYCLE_TIME
	//);
};

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
 * Bootstrap module functionality.
 */
const init = () => {
	if ( rotatingHeroes.length ) {
		rotatingHeroes.forEach( startRotatingImages );
	}

	if ( heroesNotRotating.length ) {
		heroesNotRotating.forEach( startRotatingHeadings );
	}
};

/**
 * Clear timeouts in preparation for hot module update.
 */
const dispose = () => {
	if ( rotatingHeroes.length ) {
		//rotatingHeroes.forEach( block => clearTimeout( block.rotateImagesTimeout ) );
		rotatingHeroes.forEach( block => {
			const controls = block.querySelector( '.hero-home__controls' );
			if ( controls ) {
				controls.replaceWith( controls.clone( true ) );
			}
		} );
	}

	if ( heroes.length ) {
		heroes.forEach( block => clearTimeout( block.rotateHeadingsTimeout ) );
	}
};

init();

if ( module.hot ) {
	module.hot.dispose( dispose );
	module.hot.accept();
	init();
}
