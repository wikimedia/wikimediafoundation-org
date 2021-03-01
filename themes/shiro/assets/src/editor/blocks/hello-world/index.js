/**
 * A sample block, to be deleted when we have actual blocks in the theme.
 */
import { __ } from '@wordpress/i18n';

export const
	name = 'shiro/hello-world',
	settings = {

		title: __( 'Hello world!', 'shiro' ),

		edit: () => {
			return (
				<div>
				Hello World
				</div>
			);
		},
	};
