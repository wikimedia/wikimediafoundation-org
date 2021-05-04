import { select } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

/**
 * Assign a class name to the WritingFlow component, for use in styling content.
 */

/**
 * The name of this editor plugin. Required.
 */
export const name = 'post-type-override';

export const settings = {
	/**
	 * "Render" component for this plugin.
	 *
	 * Returns nothing, just adds a class name as a side effect.
	 */
	render: function Render() {
		const postType = select( 'core/editor' ).getCurrentPostType();

		useEffect( () => {
			const wrapperElement = document.querySelector( '.block-editor-writing-flow' );
			if ( wrapperElement ) {
				const classes = [
					'single',
					'has-blocks',
					`single-${postType}`,
				];

				// Add on first load
				wrapperElement.classList.add( ...classes );

				// Make sure the classes we add stick around
				const observer = new MutationObserver( ( list, observer ) => {
					list.forEach( mutation => {
						if ( mutation.type === 'attributes' && mutation.attributeName === 'class' ) {
							classes.forEach( className => {
								if ( ! wrapperElement.classList.contains( className ) ) {
									wrapperElement.classList.add( className );
								}
							} );
						}
					} );
				} );
				observer.observe( wrapperElement, {
					attributeFilter: [ 'class' ],
				} );
			}
		} );

		return null;
	},
};
