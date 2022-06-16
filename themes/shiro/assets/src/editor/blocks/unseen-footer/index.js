/**
 * Block for displaying the Wiki Unseen footer.
 */

/**
 * WordPress dependencies.
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import './style.scss';

// Define blocks template.
const template = [
	[ 'core/heading', {
		content: __( 'Help us close the visual<br>knowledge gap.', 'shiro-admin' ),
		className: 'unseen-footer-cta is-style-sans-p',
		level: 2,
	} ],
	[ 'core/paragraph', {
		placeholder: __( 'Add content...', 'shiro-admin' ),
		className: 'unseen-footer-content is-style-sans-p',
	} ],
];

export const name = 'shiro/unseen-footer';

export const settings = {
	title: __( 'Unseen Footer CTA', 'shiro-admin' ),

	apiVersion: 2,

	description: __( 'Add the Wiki Unseen footer call-to-action.', 'shiro-admin' ),

	icon: 'megaphone',

	category: 'wikimedia',

	keywords: [ 'Wikimedia', 'Wiki Unseen', 'Footer' ],

	example: {
		innerBlocks: [
			{
				name: 'core/heading',
				attributes: {
					content: __( 'Help us close the visual<br>knowledge gap.', 'shiro-admin' ),
					className: 'unseen-footer-cta is-style-sans-p',
					level: 2,
				},
			},
			{
				name: 'core/paragraph',
				attributes: {
					content: __( 'Our work has just started. With the help of researchers, artists, and volunteers like you, we are recreating the likeness of those whose pictures cannot be found anywhere. Get involved.', 'shiro-admin' ),
					className: 'unseen-footer-content is-style-sans-p',
				},
			},
		],
	},

	/**
	 * Render edit of the Artist Display block.
	 */
	edit: function FooterEdit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();

		return (
			<section { ...blockProps }>
				<div className={ 'inner-wrap' }>
					<InnerBlocks
						template={ template }
						templateLock={ 'all' }
					/>
				</div>
			</section>
		);
	},

	/**
	 * Render save of the Artist Display block.
	 */
	save: function FooterSave( { attributes } ) {
		const blockProps = useBlockProps.save();

		return (
			<section { ...blockProps }>
				<div className={ 'inner-wrap' }>
					<InnerBlocks.Content />
				</div>
			</section>
		);
	},

};
