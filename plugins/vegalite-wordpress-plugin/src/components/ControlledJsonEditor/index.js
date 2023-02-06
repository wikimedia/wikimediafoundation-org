import { JsonEditor } from 'jsoneditor-react';
import React, { useEffect, useRef, useState } from 'react';

import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import 'jsoneditor-react/es/editor.min.css';
import './jsoneditor.css';

/**
 * Create a JSONEditor where the reference is tracked in order to update the editor component value outside of the editor.
 *
 * @param {object}   props          React component props.
 * @param {object}   props.value    Value to use in the JSON Editor.
 * @param {Function} props.onChange Callback.
 * @returns {Element} JSONEditor component where the reference is tracked.
 */
export const ControlledJsonEditor = ( { value, onChange } ) => {
	const jsonEditorRef = useRef();

	const [ isJsonMode, setIsJsonMode ] = useState( false );

	useEffect(
		() => {
			const editor = jsonEditorRef?.current?.jsonEditor;
			if ( editor && value ) {
				editor.update( value );
			}
		},
		[ jsonEditorRef, value ]
	);

	useEffect(
		() => {
			const editor = jsonEditorRef?.current?.jsonEditor;
			if ( editor ) {
				editor.setMode( isJsonMode ? 'tree' : 'text' );
			}
		},
		[ jsonEditorRef, isJsonMode ]
	);

	return (
		<>
			<ToggleControl
				label={ __( 'Use JSON tree editor', 'datavis' ) }
				checked={ isJsonMode }
				onChange={ () => setIsJsonMode( ! isJsonMode ) }
			/>
			<JsonEditor
				ref={ jsonEditorRef }
				value={ value }
				onChange={ onChange }
			/>
			<p>
				<a href="https://vega.github.io/vega-lite/" target="_blank" rel="noreferrer">
					{ __( 'To create more complicated or interactive graphics, refer to the Vega Lite documentation site.', 'vegalite-plugin' ) }
				</a>
			</p>
		</>
	);
};

export default ControlledJsonEditor;
