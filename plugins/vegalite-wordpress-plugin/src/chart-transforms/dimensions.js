import React from 'react';

import { ResizableBox } from '@wordpress/components';

import VegaChart from '../components/VegaChart';

const enableWidthAndHeightOnly = {
	top: false,
	right: true,
	bottom: true,
	left: false,
	topRight: false,
	bottomRight: true,
	bottomLeft: false,
	topLeft: false,
};

/**
 * Transform a Vega spec's top-level height and width properties.
 *
 * @param {object} json   Vega spec being transformed.
 * @param {number} width  New chart width.
 * @param {number} height New chart height.
 * @returns {object} Transformed spec.
 */
const transformDimensions = ( json, width, height ) => {
	// Do not need a deep copy since these are top-level properties.
	const transformedSpec = { ...json };
	if ( height ) {
		transformedSpec.height = height;
	}
	if ( width ) {
		transformedSpec.width = width;
	}
	return transformedSpec;
};

/**
 * Render a chart preview and provide drag handles to resize the chart.
 *
 * @param {object}  props               React component props.
 * @param {number}  props.id            Unique chart ID.
 * @param {object}  props.json          Vega spec being edited.
 * @param {object}  props.setAttributes Block editor setAttributes method.
 * @param {boolean} props.showHandles   Whether to display the drag handles (resize UI).
 * @returns {React.ReactNode} Rendered chart type selector.
 */
export const ResizableChartPreview = ( { id, json, setAttributes, showHandles } ) => {
	if ( ! showHandles ) {
		return (
			<VegaChart spec={ json } />
		);
	}

	const width = json.width || 400;
	const height = json.height || 200;
	const dimensionProps = {};
	if ( json.width && json.height ) {
		dimensionProps.size = {
			width,
			height,
		};
	} else if ( json.width ) {
		dimensionProps.size = {
			width,
		};
	} else if ( json.height ) {
		dimensionProps.size = {
			height,
		};
	}

	return (
		<ResizableBox
			minHeight="50"
			minWidth="50"
			enable={ enableWidthAndHeightOnly }
			defaultSize="auto"
			{ ...dimensionProps }
			onResizeStop={ ( event, direction, elt, delta ) => {
				// Only update a dimension if the user interaction provided a
				// value for that dimension. (Allows height to remain "auto").
				const newWidth = [ 'right', 'bottomRight' ].includes( direction )
					? width + delta.width
					: null;
				const newHeight = [ 'bottom', 'bottomRight' ].includes( direction )
					? height + delta.height
					: null;

				setAttributes( {
					json: transformDimensions( json, newWidth, newHeight ),
				} );
			} }
		>
			<VegaChart id={ id } spec={ json } />
		</ResizableBox>
	);
};
