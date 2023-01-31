import React, { useEffect, useRef } from 'react';
import vegaEmbed from 'vega-embed';

import sufficientlyUniqueId from '../../util/sufficiently-unique-id';

/**
 * Display a Vega Lite chart.
 *
 * @param {object} props      React component props.
 * @param {object} props.spec Vega Lite specification object.
 * @param {object} props.id   ID attribute for chart container.
 * @returns {React.ReactNode} Node for a container into which chart will be rendered.
 */
const VegaChart = ( { spec, id = sufficientlyUniqueId() } ) => {
	const container = useRef( null );

	useEffect( () => {
		if ( ! container.current ) {
			return;
		}
		vegaEmbed(
			container.current,
			{
				$schema: 'https://vega.github.io/schema/vega-lite/v5.json',
				...spec,
			},
			{ actions: false }
		);
	}, [ container, id, spec ] );

	return (
		<div ref={ container } id={ id }></div>
	);
};

export default VegaChart;
