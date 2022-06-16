import classNames from 'classnames';
import { partial, tail } from 'lodash';

import { RichText, useBlockProps, InspectorControls, withColors, PanelColorSettings, URLInput } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import ImagePicker from '../../components/image-picker';
import { ensureEmptyHeading, prepareHeadings } from '../../helpers/repeating-headings';

/**
 * Edit component used to manage featured image and page intro.
 */
const HomePageHeroBlock = ( { attributes, setAttributes, isSelected, setHeadingColor, headingColor } ) => {
	const {
		imageId,
		imageUrl,
		enableAnimation,
		linkUrl,
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
						labels={ {
							instructions: __( '⚠️ Scrolling images are expected to be 4237px high and 454px wide (or multiples of those values). Using a different resolution will result in strange behavior.', 'shiro-admin' ),
						} }
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
					<div className={ classNames(
						'hero-home__heading-color',
						headingColor?.class
					) }>
						<RichText
							allowedFormats={ [ 'core/italic', 'core/link', 'core/subscript', 'core/superscript' ] }
							className="hero-home__heading"
							keepPlaceholderOnFocus
							placeholder={ __( 'Add a heading', 'shiro-admin' ) }
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
							__( 'Hide rotating headings', 'shiro-admin' ) :
							__( 'Show rotating headings', 'shiro-admin' ) }
					</Button> ) }
					{ showRotatingHeadings && rotatingHeadings.map( ( heading, headingIndex ) => {
						// Account for the non-rotating heading.
						headingIndex += 1;

						return (
							<div key={ headingIndex } className={ classNames(
								'hero-home__heading-color',
								headingColor?.class
							) }>
								<RichText
									allowedFormats={ [ 'core/italic', 'core/link', 'core/subscript', 'core/superscript' ] }
									className="hero-home__heading"
									keepPlaceholderOnFocus
									placeholder={ __( 'Add a rotating heading', 'shiro-admin' ) }
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
					<PanelBody initialOpen title={ __( 'Image settings', 'shiro-admin' ) }>
						<ToggleControl
							checked={ enableAnimation }
							label={ __( 'Enable animation', 'shiro-admin' ) }
							onChange={ enableAnimation => setAttributes( { enableAnimation } ) }
						/>
					</PanelBody>
					<PanelBody initialOpen title={ __( 'Link settings', 'shiro-admin' ) }>
						<URLInput
							isFullWidth
							label={ __( 'Link', 'shiro-admin' ) }
							value={ linkUrl }
							onChange={ linkUrl => setAttributes( { linkUrl } ) }
						/>
					</PanelBody>
				</InspectorControls>
				{ activeHeading !== null && ( <InspectorControls>
					<PanelBody initialOpen title={ __( 'Heading settings', 'shiro-admin' ) }>
						<TextControl
							label={ __( 'Language code', 'shiro-admin' ) }
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
				<InspectorControls>
					<PanelColorSettings
						colorSettings={ [
							{
								value: headingColor.color,
								onChange: setHeadingColor,
								label: __( 'Heading background color' ),
								clearable: false,
							},
						] }
						title={ __( 'Color settings' ) }
					>
					</PanelColorSettings>
				</InspectorControls>
			</header>
		</div>
	);
};

export default compose(
	withColors( { headingColor: 'background-color' } )
)( HomePageHeroBlock );
