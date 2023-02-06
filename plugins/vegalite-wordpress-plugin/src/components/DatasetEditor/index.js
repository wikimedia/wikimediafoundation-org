import React, { useCallback, useEffect, useState, useMemo } from 'react';

import { Icon, TextControl, Button, PanelRow, SelectControl, TextareaControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { getSelectedDatasetFromSpec } from '../../util/spec';
import './dataset-editor.scss';
import FileDropZone from '../FileDropZone';

const INLINE = 'inline';

const inlineDataOption = {
	label: __( 'Inline data', 'datavis' ),
	value: INLINE,
};

/** No-op function for use as argument default. */
const noop = () => {};

/**
 * Render an editor for a specific CSV file.
 *
 * @param {object}   props          React component props.
 * @param {string}   props.filename Filename of CSV being edited.
 * @param {Function} props.onSave   Callback to run when CSV changes.
 * @returns {React.ReactNode} Rendered react UI.
 */
const CSVEditor = ( { filename, onSave = noop } ) => {
	// The balance between redux store data and local state data is tricky.
	// Test with caution when editing this component.
	const dataset = useSelect( ( select ) => select( 'csv-datasets' ).getDataset( filename ) );
	const { updateDataset } = useDispatch( 'csv-datasets' );
	const [ content, setCsvContent ] = useState( dataset?.content !== undefined ? dataset.content : 'loading' );

	useEffect( () => {
		// If content has loaded, update the store value.
		if ( content === 'loading' && filename !== INLINE && dataset.content !== undefined ) {
			setCsvContent( dataset.content );
		}
	}, [ filename, dataset, content, setCsvContent ] );

	const onDrop = useCallback( ( { content } ) => {
		setCsvContent( content );
	}, [] );

	const onSaveButton = useCallback( () => {
		if ( filename ) {
			updateDataset( {
				filename,
				content: content,
			} ).then( onSave );
		}
	}, [ filename, content, updateDataset, onSave ] );

	if ( filename === INLINE ) {
		return (
			<p>{ __( 'Edit data values as JSON in the Chart Specification tab.', 'datavis' ) }</p>
		);
	}

	return (
		<FileDropZone
			message={ __( 'Drop CSV to load data', 'datavis' ) }
			onDrop={ onDrop }
		>
			<TextareaControl
				label={ __( 'Edit CSV dataset', 'datavis' ) }
				value={ content || '' }
				onChange={ setCsvContent }
				rows="10"
			/>
			<Button className="is-primary" onClick={ onSaveButton }>
				{ __( 'Save dataset', 'datavis' ) }
			</Button>
		</FileDropZone>
	);
};

/**
 * Render a New Dataset form.
 *
 * @param {object} props              React component props.
 * @param {object} props.onAddDataset Callback after new dataset gets saved.
 * @returns {React.ReactNode} Rendered form.
 */
const NewDatasetForm = ( { onAddDataset } ) => {
	const [ filename, setFilename ] = useState( '' );
	const [ hasFormError, setHasFormError ] = useState( false );
	const { createDataset } = useDispatch( 'csv-datasets' );

	const onChangeContent = useCallback( ( content ) => {
		setFilename( content );
		setHasFormError( false );
	}, [ setFilename, setHasFormError ] );

	const onSubmit = useCallback( () => {
		if ( ! filename.trim() ) {
			setHasFormError( true );
			return;
		}
		createDataset( { filename } ).then( onAddDataset );
	}, [ filename, createDataset, onAddDataset ] );

	const submitOnEnter = useCallback( ( evt ) => {
		if ( evt.code === 'Enter' ) {
			onSubmit();
		}
	}, [ onSubmit ] );

	return (
		<>
			<PanelRow className="datasets-control-row">
				<TextControl
					label={ __( 'CSV dataset name', 'datavis' ) }
					value={ filename }
					onChange={ onChangeContent }
					onKeyDown={ submitOnEnter }
				/>
				<Button
					className="dataset-control-button is-primary"
					onClick={ onSubmit }
				>{ __( 'Save dataset', 'dataset' ) }</Button>
			</PanelRow>
			{ hasFormError ? (
				<p class="dataset-form-error"><em>Name is required when creating a dataset.</em></p>
			) : null }
		</>
	);
};

/**
 * Transform a Vega Lite spec to set a new data source.
 *
 * @param {object} json Vega Lite spec object.
 * @param {string} datasetUrl URL of a remote dataset.
 * @returns {object} Transformed vega spec (new object reference).
 */
const setSpecDataset = ( json, datasetUrl ) => {
	if ( datasetUrl && datasetUrl !== INLINE ) {
		json.data = { url: datasetUrl };
	} else {
		// No URL. Switch to inline data.
		if ( json.data?.url ) {
			// Wipe out any URL property to set back to inline mode.
			json.data = [];
		}
	}
	return { ...json };
};

/**
 * Render the Data Editor selector.
 *
 * This component doesn't use local state: all changes are persisted directly to
 * the Vega Lite JSON spec being edited.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered form.
 */
const SelectDataset = ( { json, setAttributes } ) => {
	const datasets = useSelect( ( select ) => select( 'csv-datasets' ).getDatasets() );
	const selectedDataset = getSelectedDatasetFromSpec( datasets, json, inlineDataOption );
	const options = useMemo( () => [ inlineDataOption ].concat( datasets ), [ datasets ] );

	const onChangeSelected = useCallback( ( filename ) => {
		const selectedDataset = options.find( ( { value } ) => value === filename );
		const updatedSpec = setSpecDataset( json, selectedDataset?.url || INLINE );
		setAttributes( { json: updatedSpec } );
	}, [ options, json, setAttributes ] );

	return (
		<SelectControl
			label={ __( 'Datasets', 'datavis' ) }
			value={ selectedDataset?.value }
			options={ options }
			onChange={ onChangeSelected }
		/>
	);
};

/**
 * Render the Delete Dataset button.
 *
 * @param {object}   props               React component props.
 * @param {string}   props.filename          Vega spec being edited.
 * @param {Function} props.onDelete Callback which gets passed the  Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered form.
 */
const DeleteDataset = ( { filename, onDelete = noop } ) => {
	const { deleteDataset } = useDispatch( 'csv-datasets' );
	const onDeleteDataset = useCallback( () => {
		deleteDataset( { filename } ).then( () => {
			onDelete( filename );
		} );
	}, [ deleteDataset, filename, onDelete ] );

	if ( filename === INLINE ) {
		// Cannot delete inline dataset.
		return null;
	}

	return (
		<Button
			className="dataset-control-button is-tertiary is-destructive"
			onClick={ onDeleteDataset }
		>
			<Icon icon="trash" />
			<span className="screen-reader-text">
				{ __( 'Delete dataset', 'datavis' ) }
			</span>
		</Button>
	);
};

/**
 * Render the Data Editor form.
 *
 * @param {object} props               React component props.
 * @param {object} props.json          Vega spec being edited.
 * @param {object} props.setAttributes Block editor setAttributes method.
 * @returns {React.ReactNode} Rendered form.
 */
const DatasetEditor = ( { json, setAttributes } ) => {
	/** @type {Dataset[]} */
	const datasets = useSelect( ( select ) => select( 'csv-datasets' ).getDatasets() );
	const selectedDataset = getSelectedDatasetFromSpec( datasets, json, inlineDataOption );
	const [ isAddingNewDataset, setIsAddingNewDataset ] = useState( false );

	const onAddDataset = useCallback( ( newDataset ) => {
		const updatedSpec = setSpecDataset( json, newDataset?.url || INLINE );
		setAttributes( { json: updatedSpec } );
		setIsAddingNewDataset( false );
	}, [ json, setAttributes ] );

	const onDeleteDataset = useCallback( ( deletedFile ) => {
		// Having selection return to INLINE on delete feels confusing in the
		// limited testing we have done, so select the item prior to the
		// removed dataset, or the last dataset if there is only one left.
		// selectedDataset and datasets will not yet be updated to reflect
		// the deletion at the time this callback fires.
		if ( selectedDataset?.filename === deletedFile && datasets.length > 1 ) {
			// Datasets array does not include inlineDataOption.
			const indexOfDeleted = datasets.indexOf( selectedDataset );
			let indexToSelect = indexOfDeleted - 1;
			if ( indexToSelect < 0 && datasets.length > 1 ) {
				indexToSelect = indexOfDeleted + 1;
			}
			const updatedSpec = setSpecDataset( json, datasets[indexToSelect]?.url || INLINE );
			setAttributes( { json: updatedSpec } );
		}
	}, [ datasets, selectedDataset, json, setAttributes ] );

	const forceChartUpdate = useCallback( () => {
		// Set `json` to a new object reference to trigger a re-render.
		setAttributes( {
			json: { ...json },
		} );
	}, [ json, setAttributes ] );

	return (
		<div>
			{ isAddingNewDataset ? (
				<NewDatasetForm onAddDataset={ onAddDataset } />
			) : (
				<>
					<PanelRow className="datasets-control-row">
						<SelectDataset json={ json } setAttributes={ setAttributes } />
						<DeleteDataset
							filename={ getSelectedDatasetFromSpec( datasets, json )?.filename || INLINE }
							onDelete={ onDeleteDataset }
						/>
						<Button
							className="dataset-control-button is-primary"
							onClick={ () => setIsAddingNewDataset( true ) }
						>
							{ __( 'New dataset', 'datavis' ) }
						</Button>
					</PanelRow>
					<CSVEditor
						filename={ selectedDataset?.filename || INLINE }
						onSave={ forceChartUpdate }
					/>
				</>
			) }
		</div>
	);
};

export default DatasetEditor;
