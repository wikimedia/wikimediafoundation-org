/**
 * Editor control for setting the hero block on landing pages.
 */

/**
 * WordPress dependencies
 */
import { RichText, URLInputButton } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import ImagePicker from '../../components/image-picker';
import './style.scss';

export const name = 'shiro/landing-page-hero';

export const settings = {
	title: __( 'Landing page hero', 'shiro' ),

	icon: 'cover-image',

	description: __(
		'A hero image and text to be used on "subsite" landing pages',
		'shiro'
	),

	supports: {
		inserter: true,
		multiple: false,
		reusable: false,
	},

	attributes: {
		colorTheme: {
			type: 'string',
		},
		kicker: {
			type: 'string',
			source: 'html',
			selector: '.hero__kicker',
		},
		title: {
			type: 'string',
			source: 'html',
			selector: '.hero__title',
		},
		imageId: {
			type: 'integer',
		},
		imageUrl: {
			type: 'string',
			source: 'attribute',
			selector: '.hero__image',
			attribute: 'src',
		},
		buttonText: {
			type: 'string',
			source: 'html',
			selector: '.hero__cta',
		},
		buttonLink: {
			type: 'string',
			source: 'attribute',
			selector: '.hero__cta',
			attribute: 'href',
		},
		pageIntro: {
			type: 'string',
			source: 'html',
			selector: '.hero__intro',
			multiline: 'p',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function Edit( { attributes, setAttributes } ) {
		const {
			colorTheme,
			kicker,
			title,
			imageId,
			imageUrl,
			buttonText,
			buttonLink,
			pageIntro,
		} = attributes;

		return (
			<div className="hero">
				<header className="hero__header">
					<div className="hero__text-column">
						<RichText
							className="hero__kicker"
							keepPlaceholderOnFocus
							placeholder={ __( 'Kicker', 'shiro' ) }
							tagName="small"
							value={ kicker }
							onChange={ kicker => setAttributes( { kicker } ) }
						/>
						<RichText
							className="hero__title"
							keepPlaceholderOnFocus
							placeholder={ __( 'Title for the page', 'shiro' ) }
							tagName="h1"
							value={ title }
							onChange={ title => setAttributes( { title } ) }
						/>
						<RichText
							className="hero__cta"
							placeholder={ __( 'Call to action', 'shiro' ) }
							tagName="button"
							value={ buttonText }
							onChange={ buttonText => setAttributes( { buttonText } ) }
						/>
						<URLInputButton
							url={ buttonLink }
							onChange={ buttonLink => setAttributes( { buttonLink } ) }
						/>
					</div>
					<div className="hero__image">
						<ImagePicker
							id={ imageId }
							src={ imageUrl }
							imageSize="image_16x9_small"
							onChange={
								( { id: imageId, url: imageUrl } ) => {
									setAttributes( {
										imageId,
										imageUrl,
									} );
								}
							}
						/>
					</div>
				</header>
				<RichText
					className="hero__intro"
					keepPlaceholderOnFocus
					multiline="p"
					placeholder={ __( 'Introductory paragraph - some information about this page to guide the reader.', 'shiro' ) }
					tagName="div"
					value={ pageIntro }
					onChange={ pageIntro => setAttributes( { pageIntro } ) }
				/>
			</div>
		);

	},

	save: ( { attributes, className } ) => {
		const {
			kicker,
			title,
			imageUrl,
			buttonText,
			buttonLink,
			pageIntro,
		} = attributes;

		return (
			<div className="hero">
				<header className="hero__header">
					<div className="hero__text-column">
						<RichText.Content
							className="hero__kicker"
							tagName="small"
							value={ kicker }
						/>
						<RichText.Content
							className="hero__title"
							tagName="h1"
							value={ title }
						/>
						{ buttonLink && (
							<a
								className="hero__cta"
								href={ buttonLink }
							>
								{ buttonText }
							</a>
						) }
					</div>
					<img
						alt=""
						className="hero__image"
						src={ imageUrl }
					/>
				</header>
				<RichText.Content
					className="hero__intro"
					multiline="p"
					tagName="div"
					value={ pageIntro }
				/>
			</div>
		);
	},
};
