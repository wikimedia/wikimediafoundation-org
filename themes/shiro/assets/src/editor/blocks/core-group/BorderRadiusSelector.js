import { map } from 'lodash';

import { SelectControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import TokenList from '@wordpress/token-list';

const possibleRadii = {
	'': __( 'No border radius', 'shiro' ),
	small: __( 'Small border radius', 'shiro' ),
	big: __( 'Big border radius', 'shiro' ),
};

/**
 * Determine the active border radius for the className.
 *
 * @param {string[]} radii All possible border radii.
 * @param {string} className The current className value.
 * @returns {string} The active border radius.
 */
function getActiveRadius( radii, className ) {
	for ( const possibleClass of new TokenList( className ).values() ) {
		if ( ! possibleClass.startsWith( 'has-radius-' ) ) {
			continue;
		}

		// "has-radius-".length === 11;
		const potentialRadiusName = possibleClass.substring( 11 );
		if ( radii.includes( potentialRadiusName ) ) {
			return potentialRadiusName;
		}
	}
}

/**
 * Replaces the active radius in the className.
 *
 * @param {string} className The className to replace in.
 * @param {string} activeRadius The currently active radius.
 * @param {string} newRadius The new radius to make active.
 * @returns {string} The new className.
 */
function replaceActiveRadius( className, activeRadius, newRadius ) {
	const list = new TokenList( className );

	if ( activeRadius ) {
		list.remove( 'has-border-radius' );
		list.remove( 'has-radius-' + activeRadius );
	}

	if ( newRadius !== '' ) {
		list.add( 'has-border-radius' );
		list.add( 'has-radius-' + newRadius );
	}

	return list.value;
}

/**
 * Render the group border radius selector
 *
 * @param {object} props Props
 * @param {object} props.attributes The attributes for the selected block.
 * @param {Function} props.setAttributes The attributes setter for the selected block.
 */
export default function BorderRadiusSelector( { attributes, setAttributes } ) {
	const { className } = attributes;
	const options = map( possibleRadii, ( label, value ) => ( {
		label,
		value,
	} ) );
	const activeRadius = getActiveRadius( Object.keys( possibleRadii ), className );

	/**
	 * @param {string} selectedRadius Selected radius in the input.
	 */
	const onChangeRadius = selectedRadius => {
		setAttributes( {
			className: replaceActiveRadius( className, activeRadius, selectedRadius ),
		} );
	};

	return (
		<PanelBody title={ __( 'Wikimedia group settings', 'shiro' ) }>
			<SelectControl
				help={ __( 'This will only have an effect when a background color has been chosen for this group.', 'shiro' ) }
				label={ __( 'Border radius', 'shiro' ) }
				options={ options }
				value={ activeRadius }
				onChange={ onChangeRadius }
			/>
		</PanelBody>
	);
}
