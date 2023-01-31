import React, { useCallback, useState } from 'react';

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { Button, Icon, PanelBody, PanelRow, TextControl } from '@wordpress/components';
import { dispatch, useSelect } from '@wordpress/data';
import { __, _n, sprintf } from '@wordpress/i18n';

import sufficientlyUniqueId from '../../util/sufficiently-unique-id';

/**
 * Export registration information for Responsive Container block.
 */
import blockData from './block.json';

import './edit-responsive-container.scss';

export const name = blockData.name;

const BLOCK_TEMPLATE = [
	[ 'vegalite-plugin/visualization', {} ],
];

/**
 * Determine the display text for a breakpoint based on the other available
 * visualizations and their configured sizes.
 *
 * TODO: Neither this nor the overall block logic handles two variants with
 * the same specified minimum size.
 *
 * @param {number[]} breakpoints Array of breakpoint min-width values (in px).
 * @param {number}   chartId     ID of block for which to generate description.
 * @returns {string} Rendered label for the specified breakpoint.
 */
const getBreakpointDescription = ( breakpoints, chartId ) => {
	if ( breakpoints.length === 1 ) {
		return __( 'Default visualization', 'vegalite-plugin' );
	}

	const lowerBound = breakpoints[ chartId ] || 0;
	const sortedBreakpoints = Object.values( breakpoints ).sort( ( a, b ) => {
		return +a < +b ? -1 : 1;
	} );
	const upperBound = sortedBreakpoints.find( ( minWidth ) => lowerBound < minWidth ) || 0;

	if ( lowerBound && upperBound ) {
		return sprintf(
			__( 'Displays between %1$dpx and %2$dpx', 'vegalite-plugin' ),
			lowerBound,
			upperBound
		);
	}
	if ( lowerBound ) {
		return sprintf( __( 'Displays above %dpx', 'vegalite-plugin' ), lowerBound );
	}
	if ( upperBound ) {
		return sprintf( __( 'Displays below %dpx', 'vegalite-plugin' ), upperBound );
	}
	return __( 'Default visualization', 'vegalite-plugin' );
};

/**
 * Editorial UI for the responsive blocks.
 *
 * @param {object}   props               React component props.
 * @param {object}   props.attributes    The attributes for the selected block.
 * @param {Function} props.setAttributes The attributes setter for the selected block.
 * @param {boolean}  props.isSelected    Whether the block is selected in the editor.
 * @param {string}   props.clientId      Editor client ID for the container block.
 * @returns {React.ReactNode} Rendered editorial UI.
 */
const EditResponsiveVisualizationContainer = ( { attributes, setAttributes, isSelected, clientId, ...rest } ) => {
	const blockProps = useBlockProps( {
		className: 'responsive-visualization-container',
	} );
	const { innerBlocks, isChildSelected } = useSelect( ( select ) => ( {
		innerBlocks: select( 'core/block-editor' ).getBlocks( clientId ),
		isChildSelected: select( 'core/block-editor' ).hasSelectedInnerBlock( clientId ),
	} ) );
	const { breakpoints } = attributes;
	const [ activePanel, setActivePanel ] = useState( 0 );

	const updateBreakpoint = useCallback( ( chartId, newMinWidth ) => {
		setAttributes( {
			breakpoints: {
				...breakpoints,
				[ chartId ]: newMinWidth,
			},
		} );
	}, [ breakpoints, setAttributes ] );

	const addSizeVariant = useCallback( () => {
		const newVariant = createBlock(
			'vegalite-plugin/visualization',
			{
				...( innerBlocks[ innerBlocks.length - 1 ]?.attributes || {} ),
				// Do not copy existing chart IDs.
				chartId: sufficientlyUniqueId(),
			}
		);
		dispatch( 'core/block-editor' ).insertBlock(
			newVariant,
			innerBlocks.length,
			clientId
		);

		// Give the new chart a progressively bigger breakpoint.
		const maxMinWidth = Math.max( ...Object.values( breakpoints ).sort( ( a, b ) => {
			return +a < +b ? -1 : 1;
		} ) ) || 0;
		setAttributes( {
			breakpoints: {
				...breakpoints,
				[ newVariant.attributes.chartId ]: ( isNaN( maxMinWidth ) ? 0 : maxMinWidth ) + 320,
			},
		} );
		setActivePanel( innerBlocks.length );
	}, [ clientId, innerBlocks, breakpoints, setAttributes ] );

	const removeSizeVariant = useCallback( ( blockToRemove ) => {
		const block = innerBlocks.find( ( { clientId } ) => clientId === blockToRemove );
		const index = innerBlocks.indexOf( block );

		const chartIds = innerBlocks.map( ( { attributes } ) => attributes.chartId );
		const updatedBreakpoints = Object.keys( breakpoints ).reduce(
			( memo, chartId ) => {
				if ( chartId !== block.attributes.chartId && chartIds.includes( chartId ) ) {
					memo[ chartId ] = breakpoints[ chartId ];
				}
				return memo;
			},
			{}
		);

		dispatch( 'core/block-editor' ).removeBlock( blockToRemove );
		setAttributes( {
			breakpoints: updatedBreakpoints,
		} );
		setActivePanel( index > 0 ? index - 1 : 0 );
	}, [ innerBlocks, breakpoints, setActivePanel, setAttributes ] );

	return (
		<div { ...blockProps }>
			<PanelRow>
				<span>
					<Icon icon="smartphone" />
					<Icon icon="tablet" />
					<Icon icon="desktop" />
					{ ' ' }
					{
						sprintf(
							// Translators: %s - Number of responsive chart variations.
							_n( '%s variant', '%d responsive variants', innerBlocks.length, 'vegalite-plugin' ),
							innerBlocks.length
						)
					}
				</span>
			</PanelRow>
			{ ( isSelected || isChildSelected ) ? (
				<>
					{ innerBlocks.map( ( block, idx ) => {
						return (
							<PanelBody
								opened={ activePanel === idx }
								title={ getBreakpointDescription( breakpoints, block.attributes.chartId ) }
								onToggle={ () => setActivePanel( idx ) }
							>
								{ activePanel === idx ? (
									<>
										<PanelRow>
											<TextControl
												label={ __( 'Minimum width (px)', 'vegalite-plugin' ) }
												value={ breakpoints[block.attributes.chartId] || '' }
												type="number"
												onChange={ ( minWidth ) => {
													updateBreakpoint( block.attributes.chartId, +minWidth );
												} }
											/>
											<Button
												className="is-tertiary is-destructive"
												icon="trash"
												onClick={ () => removeSizeVariant( block.clientId ) }
											>
												{ __( 'Delete variant', 'vegalite-plugin' ) }
											</Button>
										</PanelRow>
										<style type="text/css">
											{ `[data-block="${ block.clientId }"] {
												display: block !important;
											}` }
										</style>
										<InnerBlocks
											allowedBlocks={ [ 'vegalite-plugin/visualization' ] }
											template={ BLOCK_TEMPLATE }
											renderAppender={ false }
										/>
									</>
								) : null }
							</PanelBody>
						);
					} ) }
					<PanelRow>
						<Button
							className="is-tertiary responsive-visualization-container-add-new"
							icon="plus"
							onClick={ addSizeVariant }
						>
							{ __( 'Add visualization size variant', 'vegalite-plugin' ) }
						</Button>
					</PanelRow>
				</>
			) : (
				<>
					<InnerBlocks
						allowedBlocks={ [ 'vegalite-plugin/visualization' ] }
						template={ BLOCK_TEMPLATE }
						renderAppender={ false }
					/>
					<style type="text/css">
						{ `[data-block="${ innerBlocks[0]?.clientId }"] {
							display: block !important;
						}` }
					</style>
				</>
			) }
		</div>
	);
};

/**
 * Render the responsive visualization container for saving in post content.
 *
 * @param {object}   props               React component props.
 * @param {object}   props.attributes    The attributes for the selected block.
 * @returns {React.ReactNode} Rendered editorial UI.
 */
const SaveResponsiveVisualizationContainer = ( props ) => {
	return (
		<InnerBlocks.Content />
	);
};

export const settings = {
	// Apply the block settings from the JSON configuration file.
	...blockData,

	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'vegalite-plugin/visualization' ],
				transform: ( attributes ) => {
					return createBlock(
						blockData.name,
						{},
						// Recreate the existing visualization as an inner block.
						[ createBlock( 'vegalite-plugin/visualization', attributes ) ]
					);
				},
			},
		],
	},

	edit: EditResponsiveVisualizationContainer,

	save: SaveResponsiveVisualizationContainer,
};
