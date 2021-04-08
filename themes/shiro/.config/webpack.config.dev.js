const {helpers, plugins, externals, presets} = require('@humanmade/webpack-helpers');
const {withDynamicPort, choosePort, cleanOnExit, filePath} = helpers;

// Clean up manifests on exit.
cleanOnExit([
	filePath('assets/dist/asset-manifest.json'),
]);

// This allows our configs to share the same manifest when using dev-server
let seed = {};

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
			plugins: [
				plugins.manifest( {
					seed,
				} )
			],
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
			plugins: [
				plugins.manifest( {
					seed
				} ),
			],
		}),
	]
);
