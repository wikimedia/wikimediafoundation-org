/**
 * Editor control for setting featured image, flair, and deck text.
 */

import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';

const BLOCKS_TEMPLATE = [
	[ 'core/columns', {}, [
		[ 'core/column', { width: 66.66 }, [
			[ 'core/heading' ],
			[ 'core/paragraph' ],
			[ 'core/button' ],
		] ],
		[ 'core/column', { width: 33.33 }, [
			[ 'core/image' ],
		] ],
	] ],
];

export const name = 'shiro/banner';

export const settings = {
	title: __( 'Banner controls', 'shiro' ),

	icon: 'cover-image',

	description: __(
		'Editor controls for selecting featured image and post intro.',
		'shiro'
	),

	supports: {
		align: [ 'wide', 'full' ],
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function BannerEdit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		return (
			<div { ...blockProps } >
				<InnerBlocks
					template={ BLOCKS_TEMPLATE }
					templateLock={ false } />
			</div>
		);
	},

	/**
	 * Save the banner
	 */
	save: function BannerSave( { attributes } ) {
		const blockProps = useBlockProps.save();

		return (
			<div { ...blockProps } >
				<InnerBlocks.Content />
			</div>
		);
	},
};
