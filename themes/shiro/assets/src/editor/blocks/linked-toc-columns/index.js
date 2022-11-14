import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/table-of-contents.svg';

const BLOCKS_TEMPLATE = [
	[
		'core/columns',
		{
			className: 'toc__section',
			columns: 2,
		},
		[
			[
				'core/column',
				{
					className: 'toc__sidebar',
					width: '30%',
				},
				[
					[ 'shiro/linked-toc', {} ],
				],
			],
			[
				'core/column',
				{
					className: 'toc__content',
					width: '70%',
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Purus sit amet volutpat consequat mauris. Sagittis orci a scelerisque purus semper eget duis at. Eget arcu dictum varius duis at consectetur lorem donec massa. Velit dignissim sodales ut eu sem integer vitae justo. Gravida in fermentum et sollicitudin ac orci phasellus. Quam elementum pulvinar etiam non quam lacus.',
								'shiro-admin'
							),
						},
					],
				],
			],
		],
	],
];

export const name = 'shiro/linked-toc-columns',
	settings = {
		apiVersion: 2,
		icon: BlockIcon,
		title: __( 'Linked Table of Contents Columns', 'shiro-admin' ),
		category: 'wikimedia',
		description: __(
			'A ready-to-go columns block for list template pages with table of contents menu sidebar.',
			'shiro-admin'
		),
		supports: {
			inserter: true,
			multiple: false,
			reusable: false,
		},

		/**
		 * Render edit of the table of contents column block.
		 */
		edit: function EditTocColumnsBlock() {
			const blockProps = useBlockProps();
			return (
				<div { ...blockProps }>
					<InnerBlocks template={ BLOCKS_TEMPLATE } />
				</div>
			);
		},

		/**
		 * Render the save of the table of contents column block.
		 */
		save: function SaveTocColumnsBlock() {
			const blockProps = useBlockProps.save();
			return (
				<div { ...blockProps }>
					<InnerBlocks.Content />
				</div>
			);
		},
	};
