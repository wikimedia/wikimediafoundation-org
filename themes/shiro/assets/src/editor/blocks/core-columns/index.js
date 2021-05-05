import { registerBlockStyle } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

// Register a new align-buttons-bottom block style for the column block.
registerBlockStyle( 'core/columns', [
	{
		name: 'align-buttons-bottom',
		label: __( 'Align Buttons Bottom', 'shiro' ),
	},
] );
