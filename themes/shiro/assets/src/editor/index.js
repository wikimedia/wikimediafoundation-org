import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Renders the edit of the hello world block.
 *
 * @returns {*} The rendered editing UI.
 */
function EditHelloWorld() {
	// eslint-disable-next-line react/react-in-jsx-scope
	return (
		<div>
			Hello World!
		</div>
	);
}

registerBlockType( 'shiro/hello-world', {
	title: __( 'Hello world!', 'shiro' ),
	edit: EditHelloWorld,
} );
