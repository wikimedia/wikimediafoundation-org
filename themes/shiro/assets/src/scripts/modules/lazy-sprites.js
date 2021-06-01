import initialize from '../util/initialize';

const _manifest = require( '../../../dist/rev-manifest.json' ) || {};
const _filename = _manifest[ 'icons.svg' ];

/**
 * Set up a particular icon.
 *
 * @param {Node} icon The svg to load
 */
function activateIcon( icon ) {
	const useEl = icon.querySelector( 'use' );
	const svgPath = icon.dataset.spritePath.replace(
		'[sprite-filename]',
		_filename
	);
	if ( useEl ) {
		useEl.setAttribute( 'href', svgPath );
	}
}

/**
 * Set up all lazy-loading icons that rely on sprites.
 */
function setup() {
	const sprites = document.querySelectorAll( '[data-sprite-path]' );
	if ( _filename && sprites && sprites.length > 0 ) {
		sprites.forEach( activateIcon );
	}
}

/**
 * Remove all lazy-loaded icons.
 */
function teardown() {
	const sprites = document.querySelectorAll( '[data-sprite-path]' );
	if ( _filename && sprites && sprites.length > 0 ) {
		sprites.forEach( icon => {
			const useEl = icon.querySelector( 'use' );
			if ( useEl ) {
				useEl.removeAttribute( 'href' );
			}
		} );
	}
}

export default initialize( setup, teardown );
