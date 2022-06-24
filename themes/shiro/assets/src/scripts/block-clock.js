/**
 * A collection of all clock blocks on the page.
 *
 * @type {Element[]}
 * @private
 */
let _instances = [];

/**
 * A collection of all the timer intervals.
 *
 * @type {number[]}
 * @private
 */
let _timers = [];

/**
 * Activate all clock instances on the page.
 */
function setup() {
	// Clear all the existing intervals.
	_timers.forEach( timer => {
		clearInterval( timer );
	} );

	// Reset the timer collection.
	_timers = [];

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
		stopAtTime = element.dataset.stop ? ( element.dataset.stop === 'true' ) : false,
		countPlaceholder = element.querySelector(
			'.clock__contents__count-count'
		);

	if ( dateTime === false ) {
		return;
	}

	const to = new Date( dateTime ).getTime(),
		secondsInMinute = 60 * 1000,
		secondsInHour = secondsInMinute * 60,
		secondsInDay = secondsInHour * 24;

	if ( stopAtTime ) {
		const current = Date.now();

		if ( current > to ) {
			countPlaceholder.textContent = '0';
			return;
		}
	}

	/**
	 * Timer callback function.
	 */
	function timer() {
		const current = Date.now(),
			diff = Math.abs( current - to ),
			days = Math.floor( diff / ( secondsInDay ) ),
			hours = Math.floor( ( diff % ( secondsInDay ) ) / ( secondsInHour ) ),
			mins = Math.floor( ( ( diff % ( secondsInDay ) ) % ( secondsInHour ) ) / ( secondsInMinute ) ),
			secs = Math.floor( ( ( ( diff % ( secondsInDay ) ) % ( secondsInHour ) ) % ( secondsInMinute ) ) / 1000 );

		countPlaceholder.textContent = 'Days:' + days + ', Hours:' + hours + ', Minutes:' + mins + ', Seconds:' + secs;
	}

	_timers.push( setInterval( timer, 1000 ) );
}
export default setup;
