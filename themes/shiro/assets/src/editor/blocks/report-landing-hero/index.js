/**
 * Editor control for setting the hero block on report landing pages.
 */

/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import ImageFilter, { DEFAULT_IMAGE_FILTER } from '../../components/image-filter';
import ImagePicker from '../../components/image-picker';
import blockStyles, { applyDefaultStyle } from '../../helpers/block-styles';

export const name = 'shiro/report-landing-hero';

export const styles = blockStyles;

export const settings = {
	apiVersion: 2,

	title: __( 'Report landing hero', 'shiro-admin' ),

	category: 'wikimedia',

	icon: 'superhero',

	description: __(
		'A hero image and text to be used on report landing pages',
		'shiro-admin'
	),

	example: {
		attributes: {
			kicker: __( 'WIKIMEDIA IN EDUCATION', 'shiro-admin' ),
			title: __( 'Supporting teachers, students, and learning worldwide through Wikipedia and other free knowledge projects', 'shiro-admin' ),
			imageUrl: 'https://s.w.org/images/core/5.3/MtBlanc1.jpg',
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
			selector: '.hero-report__kicker',
		},
		title: {
			type: 'string',
			source: 'html',
			selector: '.hero-report__title',
		},
		imageId: {
			type: 'integer',
		},
		imageUrl: {
			type: 'string',
			source: 'attribute',
			selector: '.hero-report__image',
			attribute: 'src',
		},
		imageFilter: {
			type: 'string',
			default: DEFAULT_IMAGE_FILTER,
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
			imageFilter,
		} = attributes;

		const blockProps = useBlockProps( { className: 'hero-report' } );

		return (
			<div { ...applyDefaultStyle( blockProps ) } >
				<header className="hero-report__header">
					<div className="hero-report__text-column">
						<RichText
							className="hero-report__kicker"
							keepPlaceholderOnFocus
							placeholder={ __( 'Kicker', 'shiro-admin' ) }
							tagName="small"
							value={ kicker }
							onChange={ kicker => setAttributes( { kicker } ) }
						/>
						<RichText
							className="hero-report__title"
							keepPlaceholderOnFocus
							placeholder={ __( 'Title for the page', 'shiro-admin' ) }
							tagName="h1"
							value={ title }
							onChange={ title => setAttributes( { title } ) }
						/>
					</div>
					<ImageFilter
						className="hero-report__image-container"
						value={ imageFilter }
						onChange={ imageFilter => setAttributes( { imageFilter } ) }
					>
						<ImagePicker
							className="hero-report__image"
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
			imageFilter,
		} = attributes;

		const blockProps = useBlockProps.save( { className: 'hero' } );

		return (
			<div { ...applyDefaultStyle( blockProps ) }>
				<header className="hero-report__header">
					<div className="hero-report__text-column">
						<RichText.Content
							className="hero-report__kicker"
							tagName="small"
							value={ kicker }
						/>
						<RichText.Content
							className="hero-report__title"
							tagName="h1"
							value={ title }
						/>
					</div>
					<ImageFilter.Content
						className="hero-report__image-container"
						value={ imageFilter }>
						<img
							alt=""
							className="hero-report__image"
							src={ imageUrl }
						/>
					</ImageFilter.Content>
				</header>
			</div>
		);
	},
};
