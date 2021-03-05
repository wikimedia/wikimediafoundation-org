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
import { useSelect, select, useDispatch, dispatch, subscribe } from '@wordpress/data';
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
		const button = document.querySelector( '.editor-post-featured-image__preview' );
		const panel = button.closest( '.components-panel__body' );
		panel.scrollIntoView();
		button.focus();
		button.click();
	} );
};

export const name = 'shiro/blog-post-heading';

export const settings = {
	title: __( 'Post Heading controls', 'shiro' ),

	description: __(
		'Editor controls for selecting featured image and post intro.',
		'shiro'
	),

	supports: {
		inserter: false,
		multiple: false,
		reusable: false,
	},

	attributes: {
		postIntro: {
			type: 'string',
			source: 'meta',
			meta: 'page_intro',
		},
	},

	icon: 'cover-image',

	edit: ( { attributes, setAttributes } ) => {

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
						<div className="post-heading__image--empty">{ __( 'No featured image selected', 'shiro' ) }</div> }
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
						placeholder={ __( 'Add a post intro', 'shiro' ) }
						tagName="h3"
						value={ attributes.postIntro }
						onChange={ postIntro => setAttributes( { postIntro } ) }
					/>
				</div>
			</div>
		);
	},
};

const SUPPORTED_POST_TYPES = [ 'post', 'archive-post' ];

subscribe( () => {
	const { replaceBlocks, removeBlock } = dispatch( 'core/block-editor' );
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
		replaceBlocks(
			firstBlock.clientId,
			[
				createBlock(  name  ),
				firstBlock,
			]
		);
	}

	// Ensure that the blog post heading hasn't been moved anywhere but the first slot.
	otherBlocks.filter( block => block.name === name )
		.forEach( block => removeBlock( block.clientId ) );
} );
