/**
 * Set up the module, taking into account HMR.
 */

/**
 * Generate an initializing function.
 *
 * @param {Function} setup Sets up all module functionality.
 * @param {Function} teardown Removes all module functionality and side-effects.
 * @returns {Function} Function that sets up this module.
 */
function initialize( setup, teardown ) {
	return () => {
		if ( module.hot ) {
			module.hot.accept();
			module.hot.dispose( teardown );
			setup();
		} else {
			setup();
		}
	};
}

export default initialize;
