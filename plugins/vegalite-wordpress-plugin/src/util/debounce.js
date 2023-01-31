/**
 * Debounce function.
 *
 * @param {Function} callback Callback to use.
 * @param {number} wait Time to wait.
 * @returns {Function} A callback.
 */
const debounce = ( callback, wait ) => {
	let timeoutId = null;
	return ( ...args ) => {
		window.clearTimeout( timeoutId );
		timeoutId = window.setTimeout( () => {
			callback.apply( null, args );
		}, wait );
	};
};

export default debounce;
