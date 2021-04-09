/**
 * Autoload and require all block editor functionality.
 */
import { autoloadBlocks, autoloadPlugins } from 'block-editor-hmr';

import './style.scss';

// Load all block index files.
autoloadBlocks(
	{ getContext: () => require.context( './blocks', true, /index\.js$/ ) },
	( context, loadModules ) => {
		if ( module.hot ) {
			module.hot.accept( context.id, loadModules );
		}
	}
);

// Load all plugin index files.
autoloadPlugins(
	{ getContext: () => require.context( './plugins', true, /index\.js$/ ) },
	( context, loadModules ) => {
		if ( module.hot ) {
			module.hot.accept( context.id, loadModules );
		}
	}
);
