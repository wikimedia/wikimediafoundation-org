/**
 * Block for displaying Wiki Unseen artist.
 */

/**
 * WordPress dependencies.
 */
import { InspectorControls, InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TextControl, ToggleControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import './style.scss';

// Get the theme URL.
const themeUrl = shiroEditorVariables.themeUrl; // eslint-disable-line no-undef

// Define blocks template.
const template = [
	[ 'core/heading', {
		content: __( 'Who do you picture when you think of this profession?', 'shiro-admin' ),
		className: 'person-bio-top-heading is-style-sans-p',
		level: 2,
	} ],
	[ 'core/image', {
		url: `${themeUrl}/assets/src/images/mercedes-richards.jpg`,
		alt: '',
	} ],
	[ 'core/heading', {
		content: __( 'See the person who made a difference.', 'shiro-admin' ),
		className: 'person-bio-heading',
		level: 3,
	} ],
	[ 'core/paragraph', {
		placeholder: __( 'Add person bio...', 'shiro-admin' ),
		className: 'person-bio-entry is-style-sans-p',
	} ],
];

export const name = 'shiro/unseen-artist';

export const settings = {
	title: __( 'Unseen Artist', 'shiro-admin' ),

	apiVersion: 2,

	description: __( 'Add a featured Wiki Unseen artist showcase.', 'shiro-admin' ),

	icon: 'admin-customizer',

	category: 'wikimedia',

	keywords: [ 'Wikimedia', 'Wiki Unseen', 'Artist' ],

	attributes: {
		hideMeta: {
			type: 'boolean',
			default: false,
		},
		artistName: {
			type: 'string',
			default: __( 'Artist Name', 'shiro-admin' ),
		},
		topHeading: {
			type: 'string',
			default: __( 'Behind the picture', 'shiro-admin' ),
		},
		linkURL: {
			type: 'string',
			default: '#',
		},
		linkText: {
			type: 'string',
			default: __( 'Listen to the Interview', 'shiro-admin' ),
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
			hideMeta: false,
			artistName: __( 'Esther Griffith', 'shiro-admin' ),
			topHeading: __( 'Behind the picture', 'shiro-admin' ),
			linkURL: '#',
			linkText: __( 'Listen to the Interview', 'shiro-admin' ),
			facebookURL: '#',
			instagramURL: '#',
			twitterURL: '#',
			linkedInURL: '#',
			behanceURL: '#',
		},
		innerBlocks: [
			{
				name: 'core/heading',
				attributes: {
					content: __( 'Who do you picture when<br>you think of an astrophysicist?', 'shiro-admin' ),
					className: 'person-bio-top-heading is-style-sans-p',
					level: 2,
				},
			},
			{
				name: 'core/image',
				attributes: {
					url: `${themeUrl}/assets/src/images/mercedes-richards.jpg`,
					alt: '',
				},
			},
			{
				name: 'core/heading',
				attributes: {
					content: __( 'See the woman who made her own space.', 'shiro-admin' ),
					className: 'person-bio-heading',
					level: 3,
				},
			},
			{
				name: 'core/paragraph',
				attributes: {
					content: __( 'Mercedes Tharam Richards (Kingston, 14 May 1955 - Hershey, 3 February 2016), née Davis, was a Jamaican astronomy and astrophysics professor. Her investigation focused on computational astrophysics, stellar astrophysics and exoplanets and brown dwarfs, and the physical dynamics of interacting binary stars systems.', 'shiro-admin' ),
				},
			},
		],
	},

	/**
	 * Render edit of the Artist Display block.
	 */
	edit: function ArtistDisplayEdit( { attributes, setAttributes } ) {
		const {
			hideMeta,
			artistName,
			topHeading,
			linkURL,
			linkText,
			facebookURL,
			instagramURL,
			twitterURL,
			linkedInURL,
			behanceURL,
		} = attributes;

		const hiddenMeta = hideMeta ? 'hidden-info' : '';

		const blockProps = useBlockProps();

		const facebook = facebookURL ? (
			<li>
				<a href={ facebookURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/facebook-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Facebook.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const instagram = instagramURL ? (
			<li>
				<a href={ instagramURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/instagram-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Instagram.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const twitter = twitterURL ? (
			<li>
				<a href={ twitterURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/twitter-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Twitter.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const linkedIn = linkedInURL ? (
			<li>
				<a href={ linkedInURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/linkedin-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on LinkedIn.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const behance = behanceURL ? (
			<li>
				<a href={ behanceURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/adobe-behance-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Behance.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		return (
			<>
				<Fragment>
					<InspectorControls>
						<PanelBody
							initialOpen
							title={ __( 'Artist Info', 'shiro-admin' ) }
						>
							<PanelRow>
								<ToggleControl
									checked={ hideMeta }
									label={ __( 'Hide artist info on desktop', 'shiro-admin' ) }
									onChange={ value => {
										setAttributes( { hideMeta: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Artist Name', 'shiro-admin' ) }
									value={ artistName }
									onChange={ value => {
										setAttributes( { artistName: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Top Heading', 'shiro-admin' ) }
									value={ topHeading }
									onChange={ value => {
										setAttributes( { topHeading: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Interview Link URL', 'shiro-admin' ) }
									placeholder={ 'https://...' }
									value={ linkURL }
									onChange={ value => {
										setAttributes( { linkURL: value } );
									} }
								/>
							</PanelRow>

							<PanelRow>
								<TextControl
									label={ __( 'Interview Link Text', 'shiro-admin' ) }
									value={ linkText }
									onChange={ value => {
										setAttributes( { linkText: value } );
									} }
								/>
							</PanelRow>
						</PanelBody>

						<PanelBody
							initialOpen
							title={ __( 'Artist Social', 'shiro-admin' ) }
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
					<div className={ 'person-bio' }>
						<InnerBlocks
							template={ template }
							templateLock={ 'all' }
						/>
					</div>

					<div className={ 'artist-info' }>
						<div className={ hiddenMeta }>
							<h4 className={ 'artist-info-heading is-style-sans-p' }>{ topHeading }</h4>
							<p className={ 'artist-info-name is-style-sans-p' }>{ artistName }</p>
							<a className={ 'artist-info-link is-style-sans-p' } href={ linkURL }>{ linkText }</a>
							<ul className={ 'artist-social' }>
								{ facebook }
								{ instagram }
								{ twitter }
								{ linkedIn }
								{ behance }
							</ul>
						</div>
					</div>
				</section>
			</>
		);
	},

	/**
	 * Render save of the Artist Display block.
	 */
	save: function ArtistDisplaySave( { attributes } ) {
		const {
			hideMeta,
			artistName,
			topHeading,
			linkURL,
			linkText,
			facebookURL,
			instagramURL,
			twitterURL,
			linkedInURL,
			behanceURL,
		} = attributes;

		const hiddenMeta = hideMeta ? 'hidden-info' : '';

		const blockProps = useBlockProps.save();

		const facebook = facebookURL ? (
			<li>
				<a href={ facebookURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/facebook-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Facebook.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const instagram = instagramURL ? (
			<li>
				<a href={ instagramURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/instagram-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Instagram.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const twitter = twitterURL ? (
			<li>
				<a href={ twitterURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/twitter-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Twitter.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const linkedIn = linkedInURL ? (
			<li>
				<a href={ linkedInURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/linkedin-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on LinkedIn.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		const behance = behanceURL ? (
			<li>
				<a href={ behanceURL }>
					<img alt='' src={ `${themeUrl}/assets/src/images/adobe-behance-b.svg` } />
					<span className={ 'screen-reader-text' }>{ `${__( 'Follow', 'shiro-admin' )} ${artistName} ${__( 'on Behance.', 'shiro-admin' )}` }</span>
				</a>
			</li> ) : '';

		return (
			<section { ...blockProps }>
				<div className={ 'person-bio' }>
					<InnerBlocks.Content />
				</div>

				<div className={ 'artist-info' }>
					<div className={ hiddenMeta }>
						<h4 className={ 'artist-info-heading is-style-sans-p' }>{ topHeading }</h4>
						<p className={ 'artist-info-name is-style-sans-p' }>{ artistName }</p>
						<a className={ 'artist-info-link is-style-sans-p' } href={ linkURL }>{ linkText }</a>
						<ul className={ 'artist-social' }>
							{ facebook }
							{ instagram }
							{ twitter }
							{ linkedIn }
							{ behance }
						</ul>
					</div>
				</div>
			</section>
		);
	},

};
