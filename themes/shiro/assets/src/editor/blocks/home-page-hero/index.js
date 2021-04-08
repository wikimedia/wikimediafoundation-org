/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps, getColorClassName } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import ImagePicker from '../../components/image-picker';

import './style.scss';
import HomePageHeroBlock from './edit';
import { prepareHeadings } from './helpers';

export const name = 'shiro/home-page-hero';

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
		headingColor: {
			type: 'string',
			default: 'yellow50',
		},
		linkUrl: {
			type: 'string',
			source: 'attribute',
			selector: '.hero-home__link',
			attribute: 'href',
		},
	},

	edit: HomePageHeroBlock,

	/**
	 * Save markup for the hero block.
	 */
	save: function Save( { attributes } ) {
		const {
			imageId,
			imageUrl,
			imageAlt,
			enableAnimation,
			headingColor,
			linkUrl,
		} = attributes;
		let {
			headings = [],
		} = attributes;

		headings = prepareHeadings( headings );

		const blockProps = useBlockProps.save( { className: 'hero-home' } );
		const headingColorClassName = getColorClassName( 'background-color', headingColor );

		/**
		 * Render wrapper link if it is set, otherwise simply render the children as-is.
		 */
		const ConditionalLink = ( { children } ) => {
			if ( linkUrl ) {
				return (
					<a className="hero-home__link" href={ linkUrl }>
						{ children }
					</a>
				);
			}

			return ( <>
				{ children }
			</> );
		};

		return (
			<div { ...blockProps } >
				<ConditionalLink>
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
							<div className={ classNames(
								'hero-home__heading-color',
								{
									[ headingColorClassName ]: headingColorClassName,
								}
							) }>
								{ headings.map( ( heading, index ) => {
									return (
										<RichText.Content
											key={ index }
											className={ classNames( {
												'hero-home__heading': true,
												'hero-home__heading--hidden': index !== 0,
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
				</ConditionalLink>
			</div>
		);
	},
};
