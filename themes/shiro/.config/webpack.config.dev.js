const {helpers, externals, presets} = require('@humanmade/webpack-helpers');
const {withDynamicPort, choosePort, cleanOnExit, filePath} = helpers;

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
				publicPath: `http://localhost:8080/shiro-blocks/`
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
				publicPath: `http://localhost:8080/shiro-theme/`
			}
		}),
	]
);
