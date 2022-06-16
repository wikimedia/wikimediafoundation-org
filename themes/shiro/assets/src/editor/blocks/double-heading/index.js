/* global shiroEditorVariables */

import { partial, findIndex, zipObject, get } from 'lodash';

import {
	RichText,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import BlockIcon from '../../../svg/blocks/double-heading.svg';
import {
	ensureEmptyHeading,
	prepareHeadings,
} from '../../helpers/repeating-headings';

const { languages, siteLanguage } = shiroEditorVariables;

const isRtlMap = zipObject(
	languages.map( language => language.shortname ),
	languages.map( language => language.is_rtl )
);

/**
 * Return whether the language is a RTL language.
 */
const isRtl = language => get( isRtlMap, language );

/**
 * Determine whether the given heading has the site language.
 */
const isSiteLanguageHeading = heading => heading.lang === siteLanguage;

/**
 *
 */
function ensureSiteLanguageHeading( headings ) {
	let siteLanguageIndex = findIndex( headings, isSiteLanguageHeading );

	if ( siteLanguageIndex === -1 ) {
		// Purposefully use .push here so we can return other values.
		headings.push( {
			text: '',
			lang: siteLanguage,
		} );

		siteLanguageIndex = headings.length - 1;
	}

	const siteLanguageHeading = headings[ siteLanguageIndex ];

	return {
		siteLanguageIndex,
		siteLanguageHeading,
	};
}

export const name = 'shiro/double-heading',
	settings = {
		apiVersion: 2,
		icon: BlockIcon,
		title: __( 'Double heading', 'shiro-admin' ),
		category: 'wikimedia',
		attributes: {
			primaryHeading: {
				type: 'string',
			},
			secondaryHeadings: {
				type: 'array',
			},
		},

		/**
		 * Render edit of the double heading block.
		 */
		edit: function DoubleHeadingBlock( { attributes, setAttributes } ) {
			const blockProps = useBlockProps( { className: 'double-heading' } );
			const { primaryHeading } = attributes;
			let { secondaryHeadings: headings = [] } = attributes;

			const [ isExpanded, setIsExpanded ] = useState( false );
			const [ activeHeading, setActiveHeading ] = useState( null );

			headings = prepareHeadings( headings );

			// Make sure the current site language always has a heading. This
			// prevents focus from shifting when the user starts typing.
			const {
				siteLanguageIndex,
				siteLanguageHeading,
			} = ensureSiteLanguageHeading( headings );

			headings = ensureEmptyHeading( headings );

			/**
			 * Set the heading attribute for the heading with the given index
			 *
			 * @param {string} attribute The attribute to set
			 * @param {number} headingToUpdate Index of the heading to set
			 * @param {*} newValue The new value for the attribute
			 * @returns {void}
			 */
			const setHeadingAttribute = (
				attribute,
				headingToUpdate,
				newValue
			) => {
				setAttributes( {
					secondaryHeadings: headings.map( ( heading, i ) => {
						if ( headingToUpdate === i ) {
							return {
								...heading,
								[ attribute ]: newValue,
							};
						}

						return heading;
					} ),
				} );

				if ( attribute === 'lang' ) {
					const switchRtl =
						isRtl( newValue ) !== isRtl( siteLanguage );

					if ( switchRtl !== headings[ headingToUpdate ].switchRtl ) {
						setHeadingAttribute(
							'switchRtl',
							headingToUpdate,
							switchRtl
						);
					}
				}
			};

			return (
				<div { ...blockProps }>
					{ isExpanded && (
						<>
							<small>
								{ __(
									'One of these variants will be shown randomly when visiting the site:',
									'shiro-admin'
								) }
							</small>
							{ headings.map( ( heading, headingIndex ) => {
								if ( heading.lang === siteLanguage ) {
									return null;
								}

								return (
									<div
										key={ headingIndex }
										className="double-heading__secondary is-style-h5"
									>
										<RichText
											allowedFormats={ [] }
											keepPlaceholderOnFocus
											placeholder={ __(
												'Write secondary heading',
												'shiro-admin'
											) }
											tagName="span"
											value={ siteLanguageHeading.text }
											onChange={ partial(
												setHeadingAttribute,
												'text',
												siteLanguageIndex
											) }
											onFocus={ () =>
												setActiveHeading( null ) }
										/>
										&nbsp;—&nbsp;
										<RichText
											allowedFormats={ [] }
											keepPlaceholderOnFocus
											placeholder={ __(
												'Write translated secondary heading',
												'shiro-admin'
											) }
											tagName="span"
											value={ heading.text }
											onChange={ partial(
												setHeadingAttribute,
												'text',
												headingIndex
											) }
											onFocus={ () =>
												setActiveHeading( headingIndex ) }
										/>
									</div>
								);
							} ) }
						</>
					) }
					{ ! isExpanded && (
						<div className="double-heading__secondary is-style-h5">
							<RichText
								allowedFormats={ [] }
								className=""
								keepPlaceholderOnFocus
								placeholder={ __(
									'Write secondary heading',
									'shiro-admin'
								) }
								tagName="span"
								value={ siteLanguageHeading.text }
								onChange={ partial(
									setHeadingAttribute,
									'text',
									siteLanguageIndex
								) }
							/>
							{ headings.length > 2 && (
								<>
									&nbsp;—{ ' ' }
									{ __(
										'[One of the available translated headings]',
										'shiro-admin'
									) }
								</>
							) }
						</div>
					) }
					<RichText
						allowedFormats={ [] }
						className="double-heading__primary is-style-h3"
						keepPlaceholderOnFocus
						placeholder={ __(
							'Write primary heading',
							'shiro-admin'
						) }
						value={ primaryHeading }
						onChange={ primaryHeading =>
							setAttributes( { primaryHeading } ) }
					/>
					{ headings.length > 1 && (
						<Button
							className="hero-home__toggle-translated-headings"
							isPrimary
							onClick={ () => setIsExpanded( ! isExpanded ) }
						>
							{ isExpanded
								? __(
									'Hide translated headings',
									'shiro-admin'
								  )
								: __(
									'Show translated headings',
									'shiro-admin'
								  ) }
						</Button>
					) }
					{ activeHeading !== null && (
						<InspectorControls>
							<PanelBody
								initialOpen
								title={ __(
									'Heading settings',
									'shiro-admin'
								) }
							>
								<SelectControl
									label={ __( 'Language', 'shiro-admin' ) }
									options={ [
										{
											label: '',
											value: '',
										},
										...languages
											.filter(
												language =>
													language.shortname !==
													siteLanguage
											)
											.map( language => {
												return {
													label: language.name,
													value: language.shortname,
												};
											} ),
									] }
									value={
										headings[ activeHeading ].lang || ''
									}
									onChange={ partial(
										setHeadingAttribute,
										'lang',
										activeHeading
									) }
								/>
							</PanelBody>
						</InspectorControls>
					) }
				</div>
			);
		},

		/**
		 * Render nothing for a dynamic block.
		 */
		save: function Save() {
			return null;
		},
	};
