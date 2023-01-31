import throttle from 'lodash.throttle';

/**
 * Creates a throttled version of an asynchronous function.
 *
 * @param {Function} func Callback to throttle.
 * @param {number}   wait How long to wait between invocations.
 * @returns {Function} Throttled function.
 */
const asyncThrottle = ( func, wait ) => {
	const throttled = throttle( ( resolve, reject, args ) => {
		func( ...args ).then( resolve ).catch( reject );
	}, wait );
	return ( ...args ) => new Promise( ( resolve, reject ) => {
		throttled( resolve, reject, args );
	} );
};

export default asyncThrottle;
