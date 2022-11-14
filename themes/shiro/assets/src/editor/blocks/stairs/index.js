import {
	InnerBlocks,
	useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/stairs.svg';

import './style.scss';

const template = [
	[ 'shiro/stair' ],
	[ 'shiro/stair' ],
	[ 'shiro/stair' ],
	[ 'shiro/stair' ],
];

export const
	name = 'shiro/stairs',
	settings = {
		apiVersion: 2,
		icon: BlockIcon,
		title: __( 'Stairs', 'shiro-admin' ),
		category: 'wikimedia',
		attributes: {},

		/**
		 * Render edit of the stair block.
		 */
		edit: function EditStairsBlock() {
			const blockProps = useBlockProps();

			return (
				<div { ...blockProps }>
					<InnerBlocks
						allowedBlocks={ [ 'shiro/stair' ] }
						template={ template }
					/>
				</div>
			);
		},

		/**
		 * Render save of the stair block.
		 */
		save: function SaveStairsBlock( { attributes } ) {
			const blockProps = useBlockProps.save();

			return (
				<div { ...blockProps }>
					<InnerBlocks.Content />
				</div>
			);
		},
	};
