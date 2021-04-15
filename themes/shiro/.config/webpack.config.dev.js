const {helpers, externals, presets} = require('@humanmade/webpack-helpers');
const {choosePort, cleanOnExit, filePath} = helpers;

// Clean up manifests on exit.
cleanOnExit([
	filePath('assets/dist/asset-manifest.json'),
]);

module.exports = choosePort( 8080).then( port => [
		presets.development({
			name: 'editor',
			externals,
			devServer: {
				port,
			},
			entry: {
				editor: filePath('assets/src/editor/index.js'),
			},
			output: {
				path: filePath('assets/dist'),
				publicPath: `http://localhost:${port}/shiro-blocks/`
			},
			resolve: {
				alias: {
					"sass-lib": filePath('assets/src/sass/css/scss/')
				}
			}
		}),
		presets.development({
			name: 'theme',
			devServer: {
				port,
			},
			entry: {
				shiro: filePath('assets/src/scripts/shiro.js'),
			},
			output: {
				path: filePath('assets/dist'),
				publicPath: `http://localhost:${port}/shiro-theme/`
			},
		}),
	]
);
