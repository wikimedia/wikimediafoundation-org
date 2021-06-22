import { SelectControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import TokenList from '@wordpress/token-list';

const icons = [
	'',
	'calendar',
	'circle',
	'close',
	'diamond',
	'down',
	'edit-ltr',
	'edit-rtl',
	'email',
	'globe',
	'heart-pink',
	'language',
	'lock',
	'lock-orig',
	'lock-pink',
	'lock-white',
	'map-pin',
	'square',
	'triangle',
	'wave',
	'arrow-back',
	'download',
	'downTriangle',
	'image',
	'mail',
	'menu',
	'open',
	'search',
	'social-facebook',
	'social-facebook-blue',
	'social-instagram',
	'social-instagram-blue',
	'social-pinterest',
	'social-twitter',
	'social-twitter-blue',
	'social-linkedin',
	'social-linkedin-blue',
	'social-youtube',
	'translate',
	'trending',
	'upTriangle',
	'userAvatar',
	'wikimedia',
	'wikimedia-blue',
	'wikipedia',
	'wikipedia-black',
];

/**
 * Determine the active icon from the className.
 *
 * @param {string[]} icons All possible icons
 * @param {string} className The current className value.
 * @returns {string} The active icon.
 */
function getActiveIcon( icons, className ) {
	for ( const icon of new TokenList( className ).values() ) {
		if ( icon.indexOf( 'has-icon-' ) === -1 ) {
			continue;
		}

		const potentialIconName = icon.substring( 9 );
		if ( icons.includes( potentialIconName ) ) {
			return potentialIconName;
		}
	}

	return '';
}

/**
 * Replaces the active icon in the className.
 *
 * @param {string} className The className to replace in.
 * @param {string} activeIcon The currently active icon.
 * @param {string} newIcon The new icon to make active.
 * @returns {string} The new className.
 */
function replaceActiveIcon( className, activeIcon, newIcon ) {
	const list = new TokenList( className );

	if ( activeIcon ) {
		list.remove( 'has-icon' );
		list.remove( 'has-icon-' + activeIcon );
	}

	if ( newIcon !== '' ) {
		list.add( 'has-icon' );
		list.add( 'has-icon-' + newIcon );
	}

	return list.value;
}

/**
 * Render the button icon selector.
 *
 * @param {object} props Props
 * @param {object} props.attributes The attributes for the selected block.
 * @param {Function} props.setAttributes The attributes setter for the selected block.
 */
export default function IconSelector( { attributes, setAttributes } ) {
	const { className } = attributes;
	const options = icons.map( icon => ( {
		label: icon || __( 'No icon', 'shiro' ),
		value: icon,
	} ) );
	const activeIcon = getActiveIcon( icons, className );

	/**
	 * @param {string} selectedIcon Selected icon in the select input.
	 */
	const handleIconSelect = selectedIcon => {
		setAttributes( {
			className: replaceActiveIcon( className, activeIcon, selectedIcon ),
		} );
	};

	return (
		<PanelBody title={ __( 'Icons', 'shiro' ) }>
			<SelectControl
				help={ __( 'A custom icon can be used by inserting an inline image', 'shiro' ) }
				label={ __( 'Icon', 'shiro' ) }
				options={ options }
				value={ activeIcon }
				onChange={ handleIconSelect }
			/>
		</PanelBody>
	);
}
