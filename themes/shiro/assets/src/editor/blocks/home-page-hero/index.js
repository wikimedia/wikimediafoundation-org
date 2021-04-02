/**
 * External dependencies
 */
import classNames from 'classnames';
import { isBoolean } from 'lodash';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
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
		mainLang: {
			type: 'string',
			source: 'attribute',
			selector: '.hero-home__heading',
			attribute: 'lang',
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
				classNames: {
					type: 'string',
					source: 'attribute',
					attribute: 'class',
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
			mainLang,
		} = attributes;
		let { rotatingHeadings }  = attributes;

		rotatingHeadings = rotatingHeadings || [];
		// This allows the user to 'delete' headings, by leaving them empty
		rotatingHeadings = rotatingHeadings.filter( heading => ! RichText.isEmpty( heading.text ) );
		rotatingHeadings = rotatingHeadings.map( heading => {
			return {
				...heading,
				switchRtl: isBoolean( heading.switchRtl ) ?
					heading.switchRtl :
					( heading.classNames || '' ).includes( 'rtl-switch' ),
			};
		} );

		const lastHeading = rotatingHeadings[ rotatingHeadings.length - 1 ];
		if ( ! lastHeading || ! RichText.isEmpty( lastHeading.text ) ) {
			rotatingHeadings.push( {
				text: '',
			} );
		}

		const blockProps = useBlockProps( { className: 'hero-home' } );
		const [ showRotatingHeadings, setShowRotatingHeadings ] = useState( false );
		const [ activeRotatingHeading, setActiveRotatingHeading ] = useState( null );

		const hasImage = !! imageId;

		return (
			<div { ...blockProps } >
				<header className="hero-home__header">
					<div className={ classNames(
						'hero-home__image-wrapper',
						{
							'hero-home__image-wrapper--disable-animation': ! isSelected || ! hasImage,
							'hero-home__image-wrapper--no-image': ! hasImage,
						}
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
					{ hasImage && ( <div className="hero-home__heading-wrapper">
						<div className="hero-home__heading-color">
							<RichText
								allowedFormats={ [ 'core/italic', 'core/link', 'core/subscript', 'core/superscript' ] }
								className="hero-home__heading"
								keepPlaceholderOnFocus
								placeholder={ __( 'Add a heading', 'shiro' ) }
								tagName="div"
								value={ heading }
								onChange={ heading => setAttributes( { heading } ) }
								onFocus={ () => setActiveRotatingHeading( null ) }
							/>
						</div>
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
								<div key={ index } className="hero-home__heading-color">
									<RichText
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
										onFocus={ () => setActiveRotatingHeading( index ) }
									/>
								</div>
							);
						} ) }
					</div> ) }
					{ activeRotatingHeading === null && ( <InspectorControls>
						<PanelBody initialOpen title={ __( 'Heading settings', 'shiro' ) }>
							<TextControl
								label={ __( 'Language code', 'shiro' ) }
								value={ mainLang || '' }
								onChange={ mainLang => setAttributes( { mainLang } ) }
							/>
						</PanelBody>
					</InspectorControls> ) }
					{ activeRotatingHeading !== null && ( <InspectorControls>
						<PanelBody initialOpen title={ __( 'Heading settings', 'shiro' ) }>
							<TextControl
								label={ __( 'Language code', 'shiro' ) }
								value={ rotatingHeadings[ activeRotatingHeading ].lang || '' }
								onChange={ lang => {
									setAttributes( {
										rotatingHeadings: rotatingHeadings.map( ( headingAttributes, attributesIndex ) => {
											if ( attributesIndex === activeRotatingHeading ) {
												return {
													...headingAttributes,
													lang,
												};
											}

											return headingAttributes;
										} ),
									} );
								} }
							/>
							<ToggleControl
								checked={ rotatingHeadings[ activeRotatingHeading ].switchRtl || false }
								label={ __( 'Switch text direction for this heading', 'rtl' ) }
								onChange={ switchRtl => {
									setAttributes( {
										rotatingHeadings: rotatingHeadings.map( ( headingAttributes, attributesIndex ) => {
											if ( attributesIndex === activeRotatingHeading ) {
												return {
													...headingAttributes,
													switchRtl,
												};
											}

											return headingAttributes;
										} ),
									} );
								} }
							/>
						</PanelBody>
					</InspectorControls> ) }
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
			mainLang,
		} = attributes;
		let {
			rotatingHeadings,
		} = attributes;

		rotatingHeadings = rotatingHeadings.filter( heading => ! RichText.isEmpty( heading.text ) );
		rotatingHeadings = rotatingHeadings.map( heading => {
			return {
				...heading,
				switchRtl: isBoolean( heading.switchRtl ) ?
					heading.switchRtl :
					( heading.classNames || '' ).includes( 'rtl-switch' ),
			};
		} );

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
						<div className="hero-home__heading-color">
							<RichText.Content
								className="hero-home__heading"
								lang={ mainLang }
								tagName="h1"
								value={ heading }
							/>
							{ rotatingHeadings.map( ( heading, index ) => {
								return (
									<RichText.Content
										key={ index }
										className={ classNames( {
											'hero-home__rotating-heading hero-home__rotating-heading--hidden': true,
											'rtl-switch': heading.switchRtl || false,
										} ) }
										lang={ heading.lang }
										tagName="h1"
										value={ heading.text }
									/>
								);
							} ) }
						</div>
					</div>
				</header>
			</div>
		);
	},
};
