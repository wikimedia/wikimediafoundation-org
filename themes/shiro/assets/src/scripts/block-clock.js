/**
 * A collection of all clock blocks on the page.
 *
 * @type {Element[]}
 * @private
 */
let _instances = [];

/**
 * Activate all clock instances on the page.
 */
function setup() {
	_instances = [ ...document.querySelectorAll( '[data-clock]' ) ];
	_instances.map( initializeClockBlock );
}

/**
 * Set up the clock that this element contains.
 *
 * @param {HTMLElement} element The wrapper for a clock
 */
function initializeClockBlock( element ) {
	const dateTime = element.dataset.clock ?? false,
		stopAtTime = element.dataset.stop ?? false,
		countPlaceholder = element.querySelector(
			'.content-clock__contents__count-count'
		);

	if ( dateTime === false ) {
		return;
	}

	const to = new Date( dateTime ).getTime(),
		secondsInMinute = 60 * 1000,
		secondsInHour = secondsInMinute * 60,
		secondsInDay = secondsInHour * 24;

	/**
	 * Timer callback function.
	 */
	function timer() {
		const current = Date.now(),
			diff = current - to,
			days = Math.floor( diff / ( secondsInDay ) ),
			hours = Math.floor( ( diff % ( secondsInDay ) ) / ( secondsInHour ) ),
			mins = Math.floor( ( ( diff % ( secondsInDay ) ) % ( secondsInHour ) ) / ( secondsInMinute ) ),
			secs = Math.floor( ( ( ( diff % ( secondsInDay ) ) % ( secondsInHour ) ) % ( secondsInMinute ) ) / 1000 );

		element.querySelector(
			'.content-clock__contents__count-count'
		).textContent = new Date( diff ).toDateString();
	}

	setInterval( timer, 1000 );
}

export default setup;
