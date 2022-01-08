/**
 * Editor control for setting the hero block on landing pages.
 */

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Cta from '../../components/cta';
import ImageFilter, { DEFAULT_IMAGE_FILTER } from '../../components/image-filter';
import ImagePicker from '../../components/image-picker';
import blockStyles, { applyDefaultStyle } from '../../helpers/block-styles';
import './style.scss';

export const name = 'shiro/landing-page-hero';

export const styles = blockStyles;

export const settings = {
	apiVersion: 2,

	title: __( 'Landing page hero', 'shiro-admin' ),

	category: 'wikimedia',

	icon: 'superhero',

	description: __(
		'A hero image and text to be used on "subsite" landing pages',
		'shiro-admin'
	),

	example: {
		attributes: {
			kicker: __( 'Our Work', 'shiro-admin' ),
			title: __( 'We help everyone share in the sum of all knowledge', 'shiro-admin' ),
			pageIntro: __( 'We are the people who keep knowledge free. There is an amazing community of people around the world that makes great projects like Wikipedia. We help them do that work. We take care of the technical infrastructure, the legal challenges, and the growing pains.', 'shiro-admin' ),
			imageUrl: 'https://s.w.org/images/core/5.3/MtBlanc1.jpg',
			buttonText: 'Learn More',
			buttonLink: 'https://wikimediafoundation.org/',
		},
	},

	supports: {
		inserter: true,
		multiple: false,
		reusable: false,
	},

	attributes: {
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
		imageFilter: {
			type: 'string',
			default: DEFAULT_IMAGE_FILTER,
		},
		buttonText: {
			type: 'string',
			source: 'html',
			selector: '.hero__call-to-action',
		},
		buttonLink: {
			type: 'string',
			source: 'attribute',
			selector: '.hero__call-to-action',
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
			kicker,
			title,
			imageId,
			imageUrl,
			buttonText,
			buttonLink,
			pageIntro,
			imageFilter,
		} = attributes;

		const blockProps = useBlockProps( { className: 'hero' } );

		return (
			<div { ...applyDefaultStyle( blockProps ) } >
				<header className="hero__header">
					<div className="hero__text-column">
						<RichText
							className="hero__kicker"
							keepPlaceholderOnFocus
							placeholder={ __( 'Kicker', 'shiro-admin' ) }
							tagName="small"
							value={ kicker }
							onChange={ kicker => setAttributes( { kicker } ) }
						/>
						<RichText
							className="hero__title"
							keepPlaceholderOnFocus
							placeholder={ __( 'Title for the page', 'shiro-admin' ) }
							tagName="h1"
							value={ title }
							onChange={ title => setAttributes( { title } ) }
						/>
						<Cta
							className="hero__call-to-action cta-button"
							text={ buttonText }
							url={ buttonLink }
							onChangeLink={ buttonLink => setAttributes( { buttonLink } ) }
							onChangeText={ buttonText => setAttributes( { buttonText } ) }
						/>
					</div>
					<ImageFilter
						className="hero__image-container"
						value={ imageFilter }
						onChange={ imageFilter => setAttributes( { imageFilter } ) }
					>
						<ImagePicker
							className="hero__image"
							id={ imageId }
							imageSize="image_16x9_large"
							src={ imageUrl }
							onChange={
								( { id: imageId, src: imageUrl } ) => {
									setAttributes( {
										imageId,
										imageUrl,
									} );
								}
							}
						/>
					</ImageFilter>
				</header>
				<RichText
					className="hero__intro"
					keepPlaceholderOnFocus
					multiline="p"
					placeholder={ __( 'Introductory paragraph - some information about this page to guide the reader.', 'shiro-admin' ) }
					tagName="div"
					value={ pageIntro }
					onChange={ pageIntro => setAttributes( { pageIntro } ) }
				/>
			</div>
		);

	},

	/**
	 * Save markup for the hero block.
	 */
	save: function Save( { attributes, className } ) {
		const {
			kicker,
			title,
			imageUrl,
			buttonText,
			buttonLink,
			pageIntro,
			imageFilter,
		} = attributes;

		const blockProps = useBlockProps.save( { className: 'hero' } );

		return (
			<div { ...applyDefaultStyle( blockProps ) }>
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
								className="hero__call-to-action cta-button"
								href={ buttonLink }
							>
								{ buttonText }
							</a>
						) }
					</div>
					<ImageFilter.Content
						className="hero__image-container"
						value={ imageFilter }>
						<img
							alt=""
							className="hero__image"
							src={ imageUrl }
						/>
					</ImageFilter.Content>
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
