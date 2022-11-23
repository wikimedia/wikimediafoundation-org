/**
 * Accordion wrapper block.
 */

/**
 * WordPress dependencies
 */

import { ReactNode } from 'react';

import { InnerBlocks, useBlockProps, InspectorControls, useSetting } from '@wordpress/block-editor';
import { Panel, PanelBody, ColorPalette } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */

import '../../helpers/accordion/toggler';

export const name = 'shiro/accordion';

export const settings = {
	apiVersion: 2,
	title: __( 'Accordion', 'shiro-admin' ),
	icon: 'menu',
	category: 'wikimedia',
	supports: {
		align: [ 'center', 'full' ],
	},
	attributes: {
		fontColor: {
			type: 'string',
		},
	},
	providesContext: {
		'accordion/fontColor': 'fontColor',
	},
	/**
	 * Render the editor UI for the block.
	 *
	 * @param {object}   props               React component props.
	 * @param {object}   props.attributes    Block attrs.
	 * @param {Function} props.setAttributes Block attribute setter.
	 * @returns {ReactNode} Rendered edit note.
	 */
	edit: function Edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps(); // eslint-disable-line react-hooks/rules-of-hooks
		const { fontColor } = attributes;
		return (
			<>
				<InspectorControls>
					<Panel header= { __( 'Set title font color:', 'shiro-admin' ) } >
						<PanelBody>
							<ColorPalette
								value={ fontColor }
								colors={ [ ...useSetting( 'color.palette' ) ] }
								onChange={ ( fontColor ) => setAttributes( { fontColor } ) }
							/>
						</PanelBody>
					</Panel>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="accordion-wrapper">
						<InnerBlocks
							allowedBlocks={ [ 'shiro/accordion-item' ] }
						/>
					</div>
				</div>
			</>
		);
	},
	save: ( { attributes } ) => {
		const blockProps = useBlockProps.save();
		blockProps.className = `accordion-wrapper ${blockProps.className} ${attributes.fontColor}`;

		return (
			<div { ...blockProps } >
				<InnerBlocks.Content />
			</div>

		);
	},
};
