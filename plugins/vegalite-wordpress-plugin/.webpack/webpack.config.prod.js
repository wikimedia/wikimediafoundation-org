const { externals, helpers, plugins, presets } = require( '@humanmade/webpack-helpers' );
const vegaExternals = require( './vega-externals' );

const { filePath } = helpers;

module.exports = presets.production( {
    name: 'vegalite-plugin',
    externals: {
		...externals,
		...vegaExternals,
	},
    entry: {
        'vegalite-plugin-editor': filePath( 'src/editor.js' ),
        'vegalite-plugin-frontend': filePath( 'src/frontend.js' ),
    },
    plugins: [
        plugins.clean(),
    ],
	cache: {
		type: 'filesystem',
	},
} );
