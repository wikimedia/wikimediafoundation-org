import { unregisterBlockStyle, registerBlockStyle } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';

domReady( () => {
	registerBlockStyle( 'core/button', {
		name: 'primary',
		label: __( 'Primary', 'shiro' ),
		isDefault: true,
	} );

	registerBlockStyle( 'core/button', {
		name: 'secondary',
		label: __( 'Secondary', 'shiro' ),
	} );

	registerBlockStyle( 'core/button', {
		name: 'tertiary',
		label: __( 'Tertiary', 'shiro' ),
	} );

	unregisterBlockStyle( 'core/button', 'outline' );
	unregisterBlockStyle( 'core/button', 'fill' );
} );
