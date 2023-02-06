import React, { useCallback, useEffect, useMemo } from 'react';

import { SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import deepCopy from '../util/deep-copy';
import { getSelectedDatasetFromSpec } from '../util/spec';

import { chartTypes } from './chart-type';

const markOptions = [
	{
		label: __( 'Area', 'datavis' ),
		value: 'area',
	},
	{
		label: __( 'Bar', 'datavis' ),
		value: 'bar',
	},
	{
		label: __( 'Circle', 'datavis' ),
		value: 'circle',
	},
	{
		label: __( 'Line', 'datavis' ),
		value: 'line',
	},
	{
		label: __( 'Point', 'datavis' ),
		value: 'point',
	},
	{
		label: __( 'Rect', 'datavis' ),
		value: 'rect',
	},
	{
		label: __( 'Rule', 'datavis' ),
		value: 'rule',
	},
	{
		label: __( 'Square', 'datavis' ),
		value: 'square',
	},
	{
		label: __( 'Text', 'datavis' ),
		value: 'text',
	},
	{
		label: __( 'Tick', 'datavis' ),
		value: 'tick',
	},
].map( ( option ) => {
	return {
		...option,
		isActive: ( json ) => ( json?.mark?.type || json?.mark ) === option.value,
		transform: ( json ) => {
			json.mark = {
				type: option.value,
				tooltip: true,
			};
			return json;
		},
	};
} );

/**
 * Return the json mark type.
 *
 * @param {object} json Vega spec.
 * @returns {string} Chart type used by this spec.
 */
const getMarkType = ( json ) => json?.mark?.type || json?.mark || '';

/**
 * Render the control to set the mark type for an x-y plot.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const SelectMark = ( { setAttributes, json } ) => {
	const onChange = useCallback( ( mark ) => {
		const selectedOption = markOptions.find( ( { value } ) => value === mark );
		if ( selectedOption && ! selectedOption.isActive( json ) ) {
			// Transform the JSON (using a cloned object to ensure the return
			// value is a new object reference, triggering re-render).
			const updatedSpec = selectedOption.transform( deepCopy( json ) );
			setAttributes( { json: updatedSpec } );
		}
	}, [ json, setAttributes ] );

	if ( ! chartTypes.xy.isActive( json ) ) {
		return null;
	}

	return (
		<SelectControl
			label={ __( 'Mark', 'datavis' ) }
			value={ getMarkType( json ) }
			options={ markOptions }
			onChange={ onChange }
		/>
	);
};

/**
 * Get the value of the field type for a given encoding parameter.
 *
 * @param {object} json  Vega spec.
 * @param {string} field String name of an encoding property, e.g. "theta" or "x".
 * @returns {string} value of the requested encoding property's "field" name, or ''.
 */
export const getEncodingField = ( json, field ) => {
	if ( ! json?.encoding || ! json.encoding[ field ] || ! json.encoding[field].field ) {
		return '';
	}
	return json.encoding[ field ].field;
};

/**
 * Get the value of the field property from a given encoding parameter.
 *
 * @param {object} json  Vega spec.
 * @param {string} field String name of an encoding property, e.g. "theta" or "x".
 * @returns {string} Value of the requested encoding property's "field.type" property, or 'nominal'.
 */
export const getEncodingFieldType = ( json, field ) => {
	if ( ! json?.encoding || ! json.encoding[ field ] || ! json.encoding[field].type ) {
		return 'nominal';
	}
	return json.encoding[ field ].type;
};

const fieldTypeOptions = [
	{
		label: __( 'Nominal', 'datavis' ),
		value: 'nominal',
		isActive: ( json, fieldName ) => {
			const field = ( json?.encoding || {} )[ fieldName ];
			if ( field && field?.type !== 'nominal' ) {
				return false;
			}
			// Nominal is the default.
			return true;
		},
	},
	{
		label: __( 'Ordinal', 'datavis' ),
		value: 'ordinal',
	},
	{
		label: __( 'Quantitative', 'datavis' ),
		value: 'quantitative',
	},
	{
		label: __( 'Temporal', 'datavis' ),
		value: 'temporal',
	},
].map( ( option ) => ( {
	/**
	 * Check if this option is active for a given encoding field in the spec.
	 *
	 * @param {object} json  Vega Lite spec.
	 * @param {string} field Encoding field being edited
	 * @returns {boolean} Whether this option is active in the provided spec, for the given field.
	 */
	isActive: ( json, field ) => {
		if ( json?.encoding && json.encoding[ field ] ) {
			return json.encoding[ field ].type === option.value;
		}
		return false;
	},
	transform: ( json, fieldName ) => {
		const currentFieldEncoding = ( json?.encoding || {} )[ fieldName ] || {};
		return {
			...json,
			encoding: {
				...( json?.encoding || {} ),
				[ fieldName ]: {
					...currentFieldEncoding,
					type: option.value,
				},
			},
		};
	},
	...option,
} ) );

/**
 * Render the control to set the field for a property in the encoding.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @param {string} props.label         Label for the field being edited.
 * @param {string} props.field         Encoding property for which to edit the field.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const SelectEncodingField = ( { setAttributes, json, field, label } ) => {
	const datasets = useSelect( ( select ) => select( 'csv-datasets' ).getDatasets() );
	const selectedDataset = getSelectedDatasetFromSpec( datasets, json );

	const fieldOptions = useMemo( () => {
		return ( selectedDataset?.fields || [] ).map( ( fieldOption ) => ( {
			label: fieldOption.field,
			value: fieldOption.field,
			isActive: ( json ) => {
				if ( json?.encoding && json.encoding[ field ] ) {
					return json.encoding[ field ].field === fieldOption.field;
				}
				return false;
			},
			transform: ( json ) => {
				const currentFieldEncoding = ( json?.encoding || {} )[ field ] || {};
				return {
					...json,
					encoding: {
						...( json?.encoding || {} ),
						[ field ]: {
							...currentFieldEncoding,
							field: fieldOption.field,
							type: fieldOption.type || 'nominal',
						},
					},
				};
			},
		} ) );
	}, [ selectedDataset, field ] );

	const onChangeField = useCallback( ( fieldName ) => {
		const selectedOption = fieldOptions.find( ( { value } ) => value === fieldName );
		if ( selectedOption && ! selectedOption.isActive( json ) ) {
			const updatedSpec = selectedOption.transform( deepCopy( json ) );
			setAttributes( { json: updatedSpec } );
		}
	}, [ fieldOptions, json, setAttributes ] );

	const onChangeFieldType = useCallback( ( fieldType ) => {
		const selectedOption = fieldTypeOptions.find( ( { value } ) => value === fieldType );
		if ( selectedOption && ! selectedOption.isActive( json, field ) ) {
			const updatedSpec = selectedOption.transform( deepCopy( json ), field );
			setAttributes( { json: updatedSpec } );
		}
	}, [ field, json, setAttributes ] );

	const encodingField = getEncodingField( json, field );

	// If the selected field is not available in the field options, pick one
	// that is valid. This means that changing dataset will clear previous
	// field settings in your chart.
	useEffect( () => {
		if ( ! fieldOptions.length || ! fieldOptions[0].value ) {
			return;
		}

		const selectedOptionExists = ! ! fieldOptions.find( ( { value } ) => encodingField === value );
		if ( ! selectedOptionExists ) {
			onChangeField( fieldOptions[0].value );
		}
	}, [ encodingField, fieldOptions, onChangeField ] );

	return (
		<>
			<SelectControl
				label={ label }
				value={ getEncodingField( json, field ) }
				options={ fieldOptions }
				onChange={ onChangeField }
			/>
			<SelectControl
				label={ __( 'Field type', 'datavis' ) }
				value={ getEncodingFieldType( json, field ) }
				options={ fieldTypeOptions }
				onChange={ onChangeFieldType }
			/>
		</>
	);
};

/**
 * Render the control to set the X Axis field.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const SelectXField = ( { setAttributes, json } ) => {
	if ( ! chartTypes.xy.isActive( json ) ) {
		return null;
	}
	return (
		<SelectEncodingField
			json={ json }
			setAttributes={ setAttributes }
			field="x"
			label={ __( 'X Axis field', 'datavis' ) }
		/>
	);
};

/**
 * Render the control to set the Y Axis field.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const SelectYField = ( { setAttributes, json } ) => {
	if ( ! chartTypes.xy.isActive( json ) ) {
		return null;
	}
	return (
		<SelectEncodingField
			json={ json }
			setAttributes={ setAttributes }
			field="y"
			label={ __( 'Y Axis field', 'datavis' ) }
		/>
	);
};

/**
 * Render the control to set the Theta field.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const SelectThetaField = ( { setAttributes, json } ) => {
	if ( ! chartTypes.radial.isActive( json ) ) {
		return null;
	}
	return (
		<SelectEncodingField
			json={ json }
			setAttributes={ setAttributes }
			field="theta"
			label={ __( 'Theta field', 'datavis' ) }
			description={ __( 'Radial distribution', 'datavis' ) }
		/>
	);
};

/**
 * Render the control to set the Color field.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const SelectColorField = ( { setAttributes, json } ) => {
	return (
		<SelectEncodingField
			json={ json }
			setAttributes={ setAttributes }
			field="color"
			label={ __( 'Color field', 'datavis' ) }
		/>
	);
};

/**
 * Render all available encoding fields, which selectively show based on the current spec.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const ConditionalEncodingFields = ( { setAttributes, json } ) => (
	<>
		<SelectMark json={ json } setAttributes={ setAttributes } />
		<SelectXField json={ json } setAttributes={ setAttributes } />
		<SelectYField json={ json } setAttributes={ setAttributes } />
		<SelectThetaField json={ json } setAttributes={ setAttributes } />
		<SelectColorField json={ json } setAttributes={ setAttributes } />
	</>
);
