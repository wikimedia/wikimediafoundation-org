const { helpers, externals, presets } = require( '@humanmade/webpack-helpers' );
const { filePath } = helpers;

module.exports = presets.production( {
	externals,
	entry: {
		editor: filePath( 'assets/src/editor/index.js' ),
	},
	output: {
		path: filePath( 'assets/dist' )
	}
} );
