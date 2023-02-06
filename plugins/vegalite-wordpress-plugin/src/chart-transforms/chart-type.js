import React, { useCallback } from 'react';

import { SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import deepCopy from '../util/deep-copy';
import { getSelectedDatasetFromSpec } from '../util/spec';
import toDictionary from '../util/to-dictionary';

const options = [
	{
		label: __( 'X/Y plot', 'datavis' ),
		value: 'xy',
		/**
		 * Transform a spec to become an X-Y plot, if not already.
		 *
		 * @param {object}   json     Vega Lite spec.
		 * @param {object[]} [fields] Array of known fields in this data.
		 * @returns {object} Transformed chart spec.
		 */
		transform: ( json, fields = [] ) => {
			if ( ( json?.mark?.type || json?.mark ) !== 'arc' && json?.encoding?.x && json?.encoding?.y ) {
				return json;
			}

			if ( json?.mark?.type || json.mark === 'arc' ) {
				json.mark = {
					type: 'bar',
					tooltip: true,
				};
			}

			if ( json.encoding?.theta ) {
				Reflect.deleteProperty( json.encoding, 'theta' );
				if ( ! json.encoding?.x ) {
					json.encoding.x = {
						field: 'unknown',
						...fields[0],
					};
				}
				if ( ! json.encoding?.y ) {
					json.encoding.y = {
						field: 'unknown',
						...fields[1],
					};
				}
			}

			return json;
		},
		/**
		 * Determine whether this option is currently active in a given spec.
		 *
		 * @param {object} json Vega Lite spec
		 * @returns {boolean} Whether this option is selected in this spec.
		 */
		isActive: ( json ) => {
			if ( ( json?.mark?.type || json?.mark ) === 'arc' ) {
				return false;
			}
			if ( ! json?.encoding?.x || ! json?.encoding?.y ) {
				return false;
			}
			return true;
		},
	},
	{
		label: __( 'Radial plot', 'datavis' ),
		value: 'radial',
		/**
		 * Transform a spec to become a radial plot, if not already.
		 *
		 * @param {object}   json     Vega Lite spec.
		 * @param {object[]} [fields] Array of known fields in this data.
		 * @returns {object} Transformed chart spec.
		 */
		transform: ( json, fields = [] ) => {
			if ( ( json?.mark?.type || json?.mark ) === 'arc' && json?.encoding?.theta ) {
				return json;
			}

			if ( ( json.mark?.type && json.mark?.type !== 'arc' ) || json.mark !== 'arc' ) {
				json.mark = {
					type: 'arc',
					tooltip: true,
				};
			}

			if ( json.encoding?.x ) {
				Reflect.deleteProperty( json.encoding, 'x' );
			}

			if ( json.encoding?.y ) {
				Reflect.deleteProperty( json.encoding, 'y' );
			}

			if ( ! json.encoding?.theta ) {
				const fieldToSet = fields.find( ( { type } ) => type === 'quantitative' ) || fields[0] || { field: 'unknown' };

				json.encoding.theta = {
					...fieldToSet,
				};
			}

			return json;
		},
		isActive: ( json ) => ( json?.mark?.type || json?.mark ) === 'arc',
	},
];

export const chartTypes = toDictionary( options, 'value' );

/**
 * Helper to determine the type of chart given a JSON spec.
 *
 * @param {object} json Vega Lite specification.
 * @returns {object} The matching option object, or undefined if no match.
 */
export const getChartType = ( json ) => {
	const matchingOption = options.find( ( option ) => option.isActive( json ) );
	return matchingOption?.value || 'error';
};

/**
 * Render the control to set top-level chart type.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
const SelectChartType = ( { setAttributes, json } ) => {
	const datasets = useSelect( ( select ) => select( 'csv-datasets' ).getDatasets() );
	const selectedDataset = getSelectedDatasetFromSpec( datasets, json );

	const onChange = useCallback( ( chartType ) => {
		const selectedOption = options.find( ( { value } ) => value === chartType );
		if ( ! selectedOption.isActive( json ) ) {
			// Transform the JSON (using a cloned object to ensure the return
			// value is a new object reference, triggering re-render).
			const updatedSpec = selectedOption.transform( deepCopy( json ), selectedDataset?.fields || [] );
			setAttributes( { json: updatedSpec } );
		}
	}, [ setAttributes, json, selectedDataset?.fields ] );

	return (
		<SelectControl
			label={ __( 'Chart type', 'datavis' ) }
			value={ getChartType( json ) }
			options={ options }
			onChange={ onChange }
		/>
	);
};

export default SelectChartType;
