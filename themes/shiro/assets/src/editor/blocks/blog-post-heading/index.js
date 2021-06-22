/**
 * Editor control for setting featured image, flair, and deck text.
 */

import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { Icon } from '@wordpress/components';
import { useSelect, select, dispatch, subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * Handle clicks on the featured image div in the post header block.
 *
 * Opens the document sidebar, expands the featured image panel, and focuses the
 * select featured image button inside it.
 */
const openFeaturedImageSelector = () => {
	const { openGeneralSidebar, toggleEditorPanelOpened } = dispatch( 'core/edit-post' );
	const { isEditorPanelOpened } = select( 'core/edit-post' );

	openGeneralSidebar( 'edit-post/document' );

	if ( ! isEditorPanelOpened( 'featured-image' ) ) {
		toggleEditorPanelOpened( 'featured-image' );
	}

	setTimeout( () => {
		const button = document.querySelector( '.editor-post-featured-image__container .components-button' );
		const panel = button.closest( '.components-panel__body' );
		panel.scrollIntoView();
		button.focus();
		button.click();
	} );
};

export const name = 'shiro/blog-post-heading';

export const settings = {
	title: __( 'Post Heading controls', 'shiro-admin' ),

	category: 'wikimedia',

	icon: 'cover-image',

	description: __(
		'Editor controls for selecting featured image and post intro.',
		'shiro-admin'
	),

	supports: {
		inserter: false,
		multiple: false,
		reusable: false,
	},

	attributes: {
		postIntro: {
			type: 'string',
			multiline: 'p',
			source: 'meta',
			meta: 'page_intro',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function Edit( { attributes, setAttributes } ) {

		// Get the source URL of the post featured image, in the large 16x9
		// size if available, with a fallback to the full-size image if not
		// (for example, if the original image is too small to be resized to
		// the large 16x9 crop size).
		const featuredImageUrl = useSelect(
			select => {
				const thumbnailId = select( 'core/editor' ).getEditedPostAttribute( 'featured_media' );
				if ( ! thumbnailId ) {
					return false;
				}
				const media = select( 'core' ).getMedia( thumbnailId );
				return media?.media_details.sizes.image_16x9_large?.source_url || media?.source_url || false;
			}
		);

		return (
			<div className="post-heading">
				<div
					className={
						classNames(
							'post-heading__image-wrapper',
							{ 'post-heading__image-wrapper--empty': ! featuredImageUrl }
						)
					}
				>
					{ featuredImageUrl ?
						<img alt="" className="post-heading__image" src={ featuredImageUrl } /> :
						<div className="post-heading__image--empty">{ __( 'No featured image selected', 'shiro-admin' ) }</div> }
					<button className="post-heading__click-overlay" onClick={ openFeaturedImageSelector }>
						<span className="dashicon-wrapper">
							<Icon icon="edit-large" />
						</span>
					</button>
				</div>
				<div className="article-title">
					<RichText
						className="post-heading__intro"
						keepPlaceholderOnFocus
						multiline="p"
						placeholder={ __( 'Add a post intro', 'shiro-admin' ) }
						tagName="div"
						value={ attributes.postIntro }
						onChange={ postIntro => setAttributes( { postIntro } ) }
					/>
				</div>
			</div>
		);
	},
};

subscribe( () => {
	const { replaceBlocks, removeBlock, selectBlock } = dispatch( 'core/block-editor' );
	const { getBlocks } = select( 'core/block-editor' );
	const { getCurrentPostType } = select( 'core/editor' );

	if ( getCurrentPostType() !== 'post' ) {
		return;
	}

	const [ firstBlock, ...otherBlocks ] = getBlocks();

	// Return early if nothing exists (content not inititalized yet).
	if ( ! firstBlock ) {
		return;
	}

	// Ensure that the first block is a blog post heading.
	if ( firstBlock.name !== name ) {
		const blogPostHeaderBlock = createBlock( name );
		replaceBlocks(
			firstBlock.clientId,
			[
				blogPostHeaderBlock,
				firstBlock,
			]
		);
		selectBlock( blogPostHeaderBlock.clientId );
	}

	// Ensure that the blog post heading hasn't been moved anywhere but the first slot.
	otherBlocks.filter( block => block.name === name )
		.forEach( block => removeBlock( block.clientId ) );
} );
