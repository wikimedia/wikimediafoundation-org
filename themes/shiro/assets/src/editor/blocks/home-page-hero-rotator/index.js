/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { select } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import './style.scss';

const HERO_BLOCK = 'shiro/home-page-hero';

export const name = 'shiro/home-page-hero-rotator';

export const settings = {
	apiVersion: 2,

	title: __( 'Home hero rotating', 'shiro-admin' ),

	category: 'wikimedia',

	icon: 'superhero-alt',

	description: __(
		'One or more images with headers and links for a home hero block',
		'shiro-admin'
	),

	supports: {
		inserter: true,
		multiple: false,
	},

	/**
	 * Allow adding any number of Hero blocks as inners.
	 */
	edit: function Edit() {
		const blockProps = useBlockProps();

		return (
			<div { ...blockProps }>
				<InnerBlocks
					template={ [ [ HERO_BLOCK ] ] }
					allowedBlocks={ [ HERO_BLOCK ] }
				/>
			</div>
		);
	},

	/**
	 * Save markup for the hero block.
	 */
	save: function Save() {
		return (
			<div className="hero-home__rotator">
				<InnerBlocks.Content />
			</div>
		);
	},

	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ HERO_BLOCK ],
				isMatch: ( _, { clientId } ) => {
					const { getBlocksByClientId, getBlockParents } = select( 'core/block-editor' );
					const parentTypes = getBlocksByClientId( getBlockParents( clientId ) )
						.map( ( { name } ) => name );
					return ! parentTypes.includes( name );
				},
				transform: ( attributes ) => createBlock(
					name,
					{},
					[
						createBlock(
							HERO_BLOCK,
							attributes
						),
					]
				),
			},
		],
	},
};