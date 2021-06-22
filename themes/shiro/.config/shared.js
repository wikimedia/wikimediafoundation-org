/**
 * Augment the loader list with an svgr loader.
 *
 * This assumes a particular rule order--it needs to modify the default
 * oneOf definition. If you add or change rules, you may need to change this.
 * Since deep merge seems to concatenate options we should be good, but be
 * aware that this code is *fragile*.
 *
 * @param config
 * @return {*}
 */
function addSvgr( config ) {
	config.module.rules[1].oneOf.unshift({
		test: /\.svg$/,
		loader: '@svgr/webpack',
		options: {
			icon: true,
		}
	});

	return config;
}

module.exports = {
	addSvgr,
}
