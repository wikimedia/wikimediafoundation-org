
/**
 * Block for displaying the Wiki Unseen text intro content.
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
		content: __( 'Wiki Unseen is more than a<br>project — it is a promise.', 'shiro-admin' ),
		className: 'unseen-intro-cta is-style-sans-p',
		level: 2,
	} ],
	[ 'core/paragraph', {
		placeholder: __( 'Add content...', 'shiro-admin' ),
		className: 'unseen-intro-content is-style-sans-p',
	} ],
];

export const name = 'shiro/unseen-intro';

export const settings = {
	title: __( 'Unseen Text Intro', 'shiro-admin' ),

	apiVersion: 2,

	description: __( 'Add the Wiki Unseen text intro section.', 'shiro-admin' ),

	icon: 'edit-large',

	category: 'wikimedia',

	keywords: [ 'Wikimedia', 'Wiki Unseen', 'Intro' ],

	example: {
		innerBlocks: [
			{
				name: 'core/heading',
				attributes: {
					content: __( 'Wiki Unseen is more than a<br>project — it is a promise.', 'shiro-admin' ),
					className: 'unseen-intro-cta is-style-sans-p',
					level: 2,
				},
			},
			{
				name: 'core/paragraph',
				attributes: {
					content: __( 'A promise to show the world the people who have shaped the world, but were systematically erased from knowledge spaces. People whose images were taken out of the picture.', 'shiro-admin' ),
					className: 'unseen-intro-content is-style-sans-p',
				},
			},
		],
	},

	/**
	 * Render edit of the Artist Display block.
	 */
	edit: function IntroEdit( { attributes, setAttributes } ) {
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
	save: function IntroSave( { attributes } ) {
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
