/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

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
		rotatingHeadings: {
			type: 'array',
			source: 'query',
			selector: '.hero-home__rotating-heading',
			query: {
				text: {
					type: 'string',
					source: 'html',
				},
				lang: {
					type: 'string',
					source: 'attribute',
					attribute: 'lang',
				},
			},
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
		let { rotatingHeadings }  = attributes;

		// This allows the user to 'delete' headings, by leaving them empty
		rotatingHeadings = rotatingHeadings.filter( heading => ! RichText.isEmpty( heading.text ) );

		const lastHeading = rotatingHeadings[ rotatingHeadings.length - 1 ];
		if ( ! lastHeading || ! RichText.isEmpty( lastHeading.text ) ) {
			rotatingHeadings.push( {
				text: '',
			} );
		}

		const blockProps = useBlockProps( { className: 'hero-home' } );
		const [ showRotatingHeadings, setShowRotatingHeadings ] = useState( false );

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
							placeholder={ __( 'Add a heading', 'shiro' ) }
							tagName="div"
							value={ heading }
							onChange={ heading => setAttributes( { heading } ) }
						/>
						<Button
							className="hero-home__toggle-rotating-headings"
							isPrimary
							onClick={ () => setShowRotatingHeadings( ! showRotatingHeadings ) }
						>
							{ showRotatingHeadings ?
								__( 'Hide rotating headings', 'shiro' ) :
								__( 'Show rotating headings', 'shiro' ) }
						</Button>
						{ showRotatingHeadings && rotatingHeadings.map( ( heading, index ) => {
							return (
								<RichText
									key={ index }
									allowedFormats={ [ 'core/italic', 'core/link', 'core/subscript', 'core/superscript' ] }
									className="hero-home__rotating-heading"
									keepPlaceholderOnFocus
									placeholder={ __( 'Add a rotating heading', 'shiro' ) }
									tagName="div"
									value={ heading.text }
									onChange={ text => {
										setAttributes( {
											rotatingHeadings: rotatingHeadings.map( ( headingAttributes, attributesIndex ) => {
												if ( attributesIndex === index ) {
													return {
														...headingAttributes,
														text,
													};
												}

												return headingAttributes;
											} ),
										} );
									} }
								/>
							);
						} ) }
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
		let {
			rotatingHeadings,
		} = attributes;

		rotatingHeadings = rotatingHeadings.filter( heading => ! RichText.isEmpty( heading.text ) );

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
						{ rotatingHeadings.map( ( heading, index ) => {
							return (
								<RichText.Content
									key={ index }
									className="hero-home__rotating-heading"
									tagName="h1"
									value={ heading.text }
								/>
							);
						} ) }
					</div>
				</header>
			</div>
		);
	},
};
