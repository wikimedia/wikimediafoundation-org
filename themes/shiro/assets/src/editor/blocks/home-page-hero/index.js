/**
 * External dependencies
 */
import classNames from 'classnames';
import { isBoolean, tail, partial } from 'lodash';

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

/**
 * Ensure an empty heading is present at the end of the array.
 *
 * This allows the user to add new headings and to keep focus in the RichText.
 *
 * @param {Array} headings The original list of headings
 * @returns {Array} The modified list of headings
 */
const ensureEmptyHeading = headings => {
	const lastHeading = headings[ headings.length - 1 ];
	if ( ! lastHeading || ! RichText.isEmpty( lastHeading.text ) ) {
		headings = [
			...headings,
			{
				text: '',
			},
		];
	}

	return headings;
};

/**
 * Prepare headings for use in the save & edit functions.
 *
 * @param {Array} headings Headings as they are saved in the attributes.
 * @returns {Array} Headings for use in the render functions.
 */
const prepareHeadings = headings => {
	// This allows the user to 'delete' headings, by leaving them empty
	headings = headings.filter( heading => ! RichText.isEmpty( heading.text ) );
	headings = headings.map( heading => {
		return {
			...heading,
			switchRtl: isBoolean( heading.switchRtl ) ?
				heading.switchRtl :
				(
					heading.classNames || ''
				).includes( 'rtl-switch' ),
		};
	} );

	return headings;
};

export const settings = {
	apiVersion: 2,

	title: __( 'Home hero', 'shiro' ),

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
		headings: {
			type: 'array',
			source: 'query',
			selector: '.hero-home__heading',
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
		enableAnimation: {
			type: 'boolean',
			default: true,
		},
	},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function HomePageHeroBlock( { attributes, setAttributes, isSelected } ) {
		const {
			imageId,
			imageUrl,
			enableAnimation,
		} = attributes;
		let {
			headings = [],
		}  = attributes;

		headings = prepareHeadings( headings );
		headings = ensureEmptyHeading( headings );

		const rotatingHeadings = tail( headings );

		const blockProps = useBlockProps( { className: 'hero-home' } );
		const [ showRotatingHeadings, setShowRotatingHeadings ] = useState( false );
		const [ activeHeading, setActiveHeading ] = useState( null );

		const hasImage = !! imageId;

		/**
		 * Set the heading attribute for the heading with the given index
		 *
		 * @param {string} attribute The attribute to set
		 * @param {number} headingToUpdate Index of the heading to set
		 * @param {*} newValue The new value for the attribute
		 * @returns {void}
		 */
		const setHeadingAttribute = ( attribute, headingToUpdate, newValue ) => {
			setAttributes( {
				headings: headings.map( ( heading, i ) => {
					if ( headingToUpdate === i ) {
						return {
							...heading,
							[ attribute ]: newValue,
						};
					}

					return heading;
				} ),
			} );
		};

		return (
			<div { ...blockProps } >
				<header className="hero-home__header">
					<div className={ classNames(
						'hero-home__image-wrapper',
						{
							'hero-home__image-wrapper--disable-animation': ! isSelected || ! hasImage || ! enableAnimation,
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
								value={ headings[0]?.text || '' }
								onChange={ partial( setHeadingAttribute, 'text', 0 ) }
								onFocus={ () => setActiveHeading( 0 ) }
							/>
						</div>
						{ headings.length > 1 && ( <Button
							className="hero-home__toggle-rotating-headings"
							isPrimary
							onClick={ () => setShowRotatingHeadings( ! showRotatingHeadings ) }
						>
							{ showRotatingHeadings ?
								__( 'Hide rotating headings', 'shiro' ) :
								__( 'Show rotating headings', 'shiro' ) }
						</Button> ) }
						{ showRotatingHeadings && rotatingHeadings.map( ( heading, headingIndex ) => {
							// Account for the non-rotating heading.
							headingIndex += 1;

							return (
								<div key={ headingIndex } className="hero-home__heading-color">
									<RichText
										allowedFormats={ [ 'core/italic', 'core/link', 'core/subscript', 'core/superscript' ] }
										className="hero-home__heading"
										keepPlaceholderOnFocus
										placeholder={ __( 'Add a rotating heading', 'shiro' ) }
										tagName="div"
										value={ heading.text }
										onChange={ partial( setHeadingAttribute, 'text', headingIndex ) }
										onFocus={ () => setActiveHeading( headingIndex ) }
									/>
								</div>
							);
						} ) }
					</div> ) }
					<InspectorControls>
						<PanelBody initialOpen title={ __( 'Image settings', 'shiro' ) }>
							<ToggleControl
								checked={ enableAnimation }
								label={ __( 'Enable animation', 'shiro' ) }
								onChange={ enableAnimation => setAttributes( { enableAnimation } ) }
							/>
						</PanelBody>
					</InspectorControls>
					{ activeHeading !== null && ( <InspectorControls>
						<PanelBody initialOpen title={ __( 'Heading settings', 'shiro' ) }>
							<TextControl
								label={ __( 'Language code', 'shiro' ) }
								value={ headings[ activeHeading ].lang || '' }
								onChange={ partial( setHeadingAttribute, 'lang', activeHeading ) }
							/>
							<ToggleControl
								checked={ headings[ activeHeading ].switchRtl || false }
								label={ __( 'Switch text direction for this heading', 'rtl' ) }
								onChange={ partial( setHeadingAttribute, 'switchRtl', activeHeading ) }
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
			enableAnimation,
		} = attributes;
		let {
			headings = [],
		} = attributes;

		headings = prepareHeadings( headings );

		const blockProps = useBlockProps.save( { className: 'hero-home' } );

		return (
			<div { ...blockProps } >
				<header className="hero-home__header">
					<div className={ classNames(
						'hero-home__image-wrapper',
						{
							'hero-home__image-wrapper--disable-animation': ! enableAnimation,
						}
					) }>
						<ImagePicker.Content
							alt={ imageAlt }
							className="hero-home__image"
							id={ imageId }
							src={ imageUrl }
						/>
					</div>
					<div className="hero-home__heading-wrapper">
						<div className="hero-home__heading-color">
							{ headings.map( ( heading, index ) => {
								return (
									<RichText.Content
										key={ index }
										className={ classNames( {
											'hero-home__heading hero-home__heading--hidden': true,
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
