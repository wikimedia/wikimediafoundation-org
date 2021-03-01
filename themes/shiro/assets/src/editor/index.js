/**
 * Autoload and require all block editor functionality.
 */
import { autoloadBlocks } from 'block-editor-hmr';

// Load all block index files.
autoloadBlocks(
	{ getContext: () => require.context( './blocks', true, /index\.js$/ ) },
	( context, loadModules ) => {
		if ( module.hot ) {
			module.hot.accept( context.id, loadModules );
		}
	}
);
