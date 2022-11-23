/**
 * Augment the loader list with an svgr loader.
 *
 * This assumes a particular rule order -- loaders are configured with a oneOf
 * definition, so that only the first matching rule will execute. Because the
 * loader object with the oneOf property may not be at a consistent index,
 * iterate until it is found, apply our custom rule, and then return the
 * mutated configuration.
 *
 * @param {object} config Webpack configuration object.
 * @returns {object} Mutated Webpack configuration object.
 */
function addSvgr( config ) {
	for ( let i = 0; i < config.module.rules.length; i++ ) {
		if ( config.module.rules[ i ].oneOf ) {
			config.module.rules[ i ].oneOf.unshift( {
				test: /\.svg$/,
				loader: '@svgr/webpack',
				options: {
					icon: true,
				},
			} );
			return config;
		}
	}

	return config;
}

module.exports = {
	addSvgr,
};
