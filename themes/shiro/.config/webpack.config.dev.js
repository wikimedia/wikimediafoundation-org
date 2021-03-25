const { helpers, externals, presets } = require( '@humanmade/webpack-helpers' );
const { choosePort, cleanOnExit, filePath } = helpers;

// Clean up manifests on exit.
cleanOnExit( [
	filePath( 'assets/dist/asset-manifest.json' ),
] );

module.exports = choosePort( 8080 ).then( port =>
	presets.development( {
		devServer: {
			port,
		},
		externals,
		entry: {
			editor: filePath( 'assets/src/editor/index.js' ),
			shiro: filePath( 'assets/src/scripts/shiro.js' ),
		},
		output: {
			path: filePath( 'assets/dist' ),
			publicPath: `http://localhost:${ port }/shiro/`
		}
	} )
);
