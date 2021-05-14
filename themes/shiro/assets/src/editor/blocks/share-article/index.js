/**
 * Block for sharing an article on Twitter or Facebook
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { ToggleControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import SvgSprite from "../../components/svg-sprite";

export const name = 'shiro/share-article';

export const settings = {
	title: __( 'Share article', 'shiro' ),

	category: 'wikimedia',

	apiVersion: 2,

	icon: 'share',

	description: __(
		'A Twitter and a Facebook button to share an article.',
		'shiro'
	),

	attributes: {
		enableTwitter: {
			type: 'boolean',
			default: true,
		},
		enableFacebook: {
			type: 'boolean',
			default: true,
		},
	},

	example: {},

	/**
	 * Edit component used to manage featured image and page intro.
	 */
	edit: function ShareArticleBlock( { attributes, setAttributes } ) {
		const {
			enableTwitter,
			enableFacebook,
		} = attributes;

		const blockProps = useBlockProps( {
			className: 'share-article',
		} );

		return (
			<div { ...blockProps } >
				{ ( ! enableTwitter && ! enableFacebook ) && (
					<small>{ __( '(No social share will be shown)', 'shiro' ) }</small>
				) }
				<div className="share-article">
					{ enableTwitter && ( <div className="share-article__link">
						<SvgSprite svg="social-twitter" />
					</div> ) }

					{ enableFacebook && ( <div className="share-article__link">
						<SvgSprite svg="social-facebook" />
					</div> ) }
				</div>
				<InspectorControls>
					<PanelBody initialOpen title={ __( 'Social settings', 'shiro' ) }>
						<ToggleControl
							checked={ enableTwitter }
							label={ __( 'Enable Twitter share', 'shiro' ) }
							onChange={ enableTwitter => setAttributes( { enableTwitter } ) }
						/>
						<ToggleControl
							checked={ enableFacebook }
							label={ __( 'Enable Facebook share', 'shiro' ) }
							onChange={ enableFacebook => setAttributes( { enableFacebook } ) }
						/>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},

	/**
	 * Save the share article block, it's a dynamic block.
	 */
	save: function Save( { attributes } ) {
		return null;
	},
};
