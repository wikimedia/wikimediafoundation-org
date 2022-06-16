
/**
 * Block for displaying the Wiki Unseen facts section.
 */

/**
 * WordPress dependencies.
 */
import { InspectorControls, InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TextControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

// Get the theme URL.
const themeUrl = shiroEditorVariables.themeUrl; // eslint-disable-line no-undef

// Define blocks template.
const template = [
	[ 'core/paragraph', {
		content: __( 'Unseen facts:', 'shiro-admin' ),
		className: 'list-heading is-style-sans-p',
	} ],
	[ 'core/list' ],
];

export const name = 'shiro/unseen-facts';

export const settings = {
	title: __( 'Unseen Facts Section', 'shiro-admin' ),

	apiVersion: 2,

	description: __( 'Add the Wiki Unseen facts section.', 'shiro-admin' ),

	icon: 'star-filled',

	category: 'wikimedia',

	keywords: [ 'Wikimedia', 'Wiki Unseen', 'Facts' ],

	attributes: {
		heading: {
			type: 'string',
			source: 'html',
			selector: 'h2',
			default: __( 'Redrawing history,<br>one image at a time.', 'wd-blocks' ),
		},
		content: {
			type: 'string',
			source: 'html',
			selector: 'p',
			default: __( 'With the support of our volunteers, we aim to share these new images on Wikipedia, making them available for everyone, everywhere. Because that is what free knowledge is about. Learn how you can become part of this initiative.', 'wd-blocks' ),
		},
		facebookURL: {
			type: 'string',
			default: '#',
		},
		instagramURL: {
			type: 'string',
			default: '#',
		},
		twitterURL: {
			type: 'string',
			default: '#',
		},
		linkedInURL: {
			type: 'string',
			default: '#',
		},
		behanceURL: {
			type: 'string',
			default: '#',
		},
	},

	example: {
		attributes: {
			heading: __( 'Redrawing history,<br>one image at a time.', 'wd-blocks' ),
			content: __( 'With the support of our volunteers, we aim to share these new images on Wikipedia, making them available for everyone, everywhere. Because that is what free knowledge is about. Learn how you can become part of this initiative.', 'wd-blocks' ),
			facebookURL: '#',
			instagramURL: '#',
			twitterURL: '#',
			linkedInURL: '#',
			behanceURL: '#',
		},
		innerBlocks: [
			{
				name: 'core/paragraph',
				attributes: {
					content: __( 'Unseen facts:', 'shiro-admin' ),
					className: 'unseen-facts is-style-sans-p',
				},
			},
			{
				name: 'core/list',
			},
		],
	},

	/**
	 * Render edit of the Artist Display block.
	 */
	edit: function FactsEdit( { attributes, setAttributes } ) {
		const {
			heading,
			content,
			facebookURL,
			instagramURL,
			twitterURL,
			linkedInURL,
			behanceURL,
		} = attributes;

		const blockProps = useBlockProps();

		const facebook = facebookURL ? (
			<li>
				<a href={ facebookURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/facebook.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Facebook', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const instagram = instagramURL ? (
			<li>
				<a href={ instagramURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/instagram.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Instagram', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const twitter = twitterURL ? (
			<li>
				<a href={ twitterURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/twitter.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Twitter', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const linkedIn = linkedInURL ? (
			<li>
				<a href={ linkedInURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/linkedin.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on LinkedIn', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const behance = behanceURL ? (
			<li>
				<a href={ behanceURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/adobe-behance.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Behance', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		return (
			<>
				<Fragment>
					<InspectorControls>
						<PanelBody
							initialOpen
							title={ __( 'Social Icons', 'shiro-admin' ) }
						>
							<PanelRow>
								<TextControl
									label={ __( 'Facebook URL', 'shiro-admin' ) }
									placeholder={ 'https://...' }
									value={ facebookURL }
									onChange={ value => {
										setAttributes( { facebookURL: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Instagram URL', 'shiro-admin' ) }
									placeholder={ 'https://...' }
									value={ instagramURL }
									onChange={ value => {
										setAttributes( { instagramURL: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Twitter URL', 'shiro-admin' ) }
									placeholder={ 'https://...' }
									value={ twitterURL }
									onChange={ value => {
										setAttributes( { twitterURL: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'LinkedIn URL', 'shiro-admin' ) }
									placeholder={ 'https://...' }
									value={ linkedInURL }
									onChange={ value => {
										setAttributes( { linkedInURL: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Behance URL', 'shiro-admin' ) }
									placeholder={ 'https://...' }
									value={ behanceURL }
									onChange={ value => {
										setAttributes( { behanceURL: value } );
									} }
								/>
							</PanelRow>
						</PanelBody>
					</InspectorControls>
				</Fragment>

				<section { ...blockProps }>
					<div className={ 'facts-content' }>
						<ul className={ 'social-icons' }>
							{ facebook }
							{ instagram }
							{ twitter }
							{ linkedIn }
							{ behance }
						</ul>

						<RichText
							className={ 'section-heading' }
							placeholder={ __( 'Add heading...', 'shiro-admin' ) }
							tagName='h2'
							value={ heading }
							onChange={ value => {
								setAttributes( { heading: value } );
							} }
						/>

						<RichText
							className={ 'section-content' }
							placeholder={ __( 'Add content...', 'shiro-admin' ) }
							tagName='p'
							value={ content }
							onChange={ value => {
								setAttributes( { content: value } );
							} }
						/>
					</div>

					<div className={ 'facts-box' }>
						<div>
							<InnerBlocks
								template={ template }
								templateLock={ 'all' }
							/>
						</div>
					</div>
				</section>
			</>
		);
	},

	/**
	 * Render save of the Artist Display block.
	 */
	save: function FactsSave( { attributes } ) {
		const {
			heading,
			content,
			facebookURL,
			instagramURL,
			twitterURL,
			linkedInURL,
			behanceURL,
		} = attributes;

		const blockProps = useBlockProps.save();

		const facebook = facebookURL ? (
			<li>
				<a href={ facebookURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/facebook.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Facebook', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const instagram = instagramURL ? (
			<li>
				<a href={ instagramURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/instagram.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Instagram', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const twitter = twitterURL ? (
			<li>
				<a href={ twitterURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/twitter.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on Twitter', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const linkedIn = linkedInURL ? (
			<li>
				<a href={ linkedInURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/linkedin.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on LinkedIn', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		const behance = behanceURL ? (
			<li>
				<a href={ behanceURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/adobe-behance.svg` } />
					<span className={ 'screen-reader-text' }>{ __( 'Follow us on LinkedIn', 'shiro-admin' ) }</span>
				</a>
			</li> ) : '';

		return (
			<section { ...blockProps }>
				<div className={ 'facts-content' }>
					<ul className={ 'social-icons' }>
						{ facebook }
						{ instagram }
						{ twitter }
						{ linkedIn }
						{ behance }
					</ul>

					<RichText.Content
						className={ 'section-heading' }
						tagName='h2'
						value={ heading }
					/>

					<RichText.Content
						className={ 'section-content' }
						tagName='p'
						value={ content }
					/>
				</div>

				<div className={ 'facts-box' }>
					<div>
						<InnerBlocks.Content />
					</div>
				</div>
			</section>
		);
	},

};
