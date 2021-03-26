/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import ImagePicker from '../../components/image-picker';
import './style.scss';

export const name = 'shiro/home-page-hero';

export const settings = {
	apiVersion: 2,

	title: __( 'Hero home', 'shiro' ),

	category: 'wikimedia',

	icon: 'cover-image',

	description: __(
		'A moving hero for the homepage',
		'shiro'
	),

	supports: {
		inserter: true,
		multiple: false,
		reusable: false,
	},

	attributes: {
		title: {
			type: 'string',
			source: 'html',
			selector: '.hero-home__title',
		},
		imageId: {
			type: 'integer',
		},
		imageUrl: {
			type: 'string',
			source: 'attribute',
			selector: '.hero-home__image',
			attribute: 'src',
		},
		imageAlt: {
			type: 'string',
			source: 'attribute',
			selector: '.hero-home__image',
			attribute: 'alt',
		},
		heading: {
			type: 'string',
			source: 'html',
			selector: '.hero-home__heading',
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function HomePageHeroBlock( { attributes, setAttributes, isSelected } ) {
		const {
			imageId,
			imageUrl,
			heading,
		} = attributes;

		const blockProps = useBlockProps( { className: 'hero-home' } );

		return (
			<div { ...blockProps } >
				<header className="hero-home__header">
					<div className={ classNames(
						'hero-home__image-wrapper',
						{ 'hero-home__image-wrapper--disable-animation': ! isSelected }
					) }>
						<ImagePicker
							className="hero-home__image"
							id={ imageId }
							src={ imageUrl }
							onChange={
								( { id: imageId, src: imageUrl, alt: imageAlt } ) => {
									setAttributes( {
										imageId,
										imageUrl,
										imageAlt,
									} );
								}
							}
						/>
					</div>
					<div className="hero-home__heading-wrapper">
						<RichText
							allowedFormats={ [ 'core/italic', 'core/link', 'core/subscript', 'core/superscript' ] }
							className="hero-home__heading"
							keepPlaceholderOnFocus
							placeholder={ __( 'Add a home heading', 'shiro' ) }
							tagName="div"
							value={ heading }
							onChange={ heading => setAttributes( { heading } ) }
						/>
					</div>
				</header>
			</div>
		);

	},

	/**
	 * Save markup for the hero block.
	 */
	save: function Save( { attributes } ) {
		const {
			imageId,
			imageUrl,
			imageAlt,
			heading,
		} = attributes;

		const blockProps = useBlockProps.save( { className: 'hero-home' } );

		return (
			<div { ...blockProps } >
				<header className="hero-home__header">
					<div className="hero-home__image-wrapper">
						<ImagePicker.Content
							alt={ imageAlt }
							className="hero-home__image"
							id={ imageId }
							src={ imageUrl }
						/>
					</div>
					<div className="hero-home__heading-wrapper">
						<RichText.Content
							className="hero-home__heading"
							tagName="h1"
							value={ heading }
						/>
					</div>
				</header>
			</div>
		);
	},
};
