import { isString, trim } from 'lodash';

import { InspectorControls } from '@wordpress/block-editor';
import {
	RichText,
	useBlockProps,
	URLInput,
} from '@wordpress/block-editor';
import { TextareaControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/twitter.svg';

export const
	name = 'shiro/tweet-this',
	settings = {
		apiVersion: 2,
		icon: BlockIcon,
		title: __( 'Tweet this', 'shiro' ),
		category: 'wikimedia',
		attributes: {
			text: {
				type: 'string',
				source: 'html',
				selector: '.tweet-this',
			},
			tweetText: {
				type: 'string',
			},
			tweetUrl: {
				type: 'string',
			},
		},

		/**
		 * Render edit of the tweet this block.
		 */
		edit: function EditTweetThis( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'tweet-this' } );
			const { text, tweetText, tweetUrl } = attributes;

			const permalink = useSelect( select => select( 'core/editor' ).getPermalink() );
			useEffect( () => {
				if ( ! isString( tweetUrl ) ) {
					setAttributes( { tweetUrl: permalink } );
				}
			}, [ tweetUrl, permalink, setAttributes ] );

			return (
				<div { ...blockProps }>
					<RichText
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
						className="wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue"
						keepPlaceholderOnFocus
						placeholder={ __( 'Write tweet this text', 'shiro' ) }
						tagName="div"
						value={ text }
						onChange={ text => setAttributes( { text } ) }
					/>
					<InspectorControls>
						<PanelBody title={ __( 'Tweet settings', 'shiro' ) }>
							<TextareaControl
								help={ __( 'When clicking the link, this text will be inside the composed tweet', 'shiro' ) }
								label={ __( 'Tweet text', 'shiro' ) }
								value={ tweetText }
								onChange={ tweetText => setAttributes( { tweetText } ) }
							/>
							<URLInput
								className="tweet-this__url-input"
								isFullWidth
								label={ __( 'Tweet URL', 'shiro' ) }
								value={ tweetUrl }
								onChange={ tweetUrl => setAttributes( { tweetUrl } ) }
							/>
						</PanelBody>
					</InspectorControls>
				</div>
			);
		},

		/**
		 * Render save of the tweet this block.
		 */
		save: function SaveTweetThis( { attributes } ) {
			const blockProps = useBlockProps.save( { className: 'tweet-this' } );
			const { text } = attributes;
			let { tweetText, tweetUrl } = attributes;

			tweetText = encodeURIComponent( tweetText );
			tweetUrl = encodeURIComponent( tweetUrl );

			const tweetThisUrl = trim( `https://twitter.com/intent/tweet?text=${ tweetText } ${ tweetUrl }` );

			return (
				<RichText.Content
					href={ tweetThisUrl }
					tagName="a"
					value={ text }
					{ ...blockProps }
					className="tweet-this wp-block-shiro-button is-style-as-link has-icon has-icon-social-twitter-blue"
				/>
			);
		},
	};
