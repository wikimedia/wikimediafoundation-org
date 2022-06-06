/**
 * Clock block that provides statistics and a count down/up timer.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls, RichText, InnerBlocks } from '@wordpress/block-editor';
import { DateTimePicker, PanelBody, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';

export const name = 'shiro/clock';

export const settings = {
	apiVersion: 2,

	icon: 'block',

	title: __( 'Clock', 'shiro-admin' ),

	category: 'wikimedia',

	description: __(
		'Clock creates a block that provides statistics and a count down/up timer.',
		'shiro-admin'
	),

	attributes: {
		countTitle: {
			type: 'string',
			source: 'html',
			selector: '.content-clock__contents__count_label',
		},
		title: {
			type: 'string',
			source: 'html',
			selector: '.content-clock__title',
		},
	},

	/**
	 * Edit component used to manage the clock block.
	 */
	edit: function ClockBlock( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'content-clock' } );
		const {
			countTitle,
			date,
			stopAtTime,
			title,
		} = attributes;
		const TEMPLATE = [
			[
				'core/columns',
				{
					className: 'content-clock__contents__stats',
					columns: 2,
				},
				[
					[
						'core/column',
						{
							className: 'content-clock__contents__stats__label',
							width: '30%',
						},
						[
							[ 'core/paragraph', { placeholder: 'Label for Stat' } ],
						],
					],
					[
						'core/column',
						{
							className: 'content-clock__contents__stats__stat',
							width: '30%',
						},
						[
							[ 'core/paragraph', { placeholder: 'Enter Stat' } ],
						],
					],
				],
			],
		];

		const ALLOWED_BLOCKS = [
			'core/column',
			'core/columns',
			'core/paragraph',
		];

		return (
			<div { ...blockProps }>
				<RichText
					className="content-clock__title is-style-h3"
					keepPlaceholderOnFocus
					placeholder={ __( 'Title of the clock', 'shiro-admin' ) }
					tagName="h2"
					value={ title }
					onChange={ title => setAttributes( { title } ) }
				/>
				<div
					className="content-clock__contents"
					data-date={ date }
					data-stop={ stopAtTime ?? false }
				>
					<div className="content-clock__contents wp-block-columns">
						<div className="content-clock__contents-left-column wp-block-column">
							<div className="content-clock__contents__count_label">
								<RichText
									className="content-clock__title is-style-h3"
									keepPlaceholderOnFocus
									placeholder={ __( 'Label for Count:', 'shiro-admin' ) }
									tagName="p"
									value={ countTitle }
									onChange={ countTitle => setAttributes( { countTitle } ) }
								/>
							</div>
						</div>
						<div className="content-clock__contents-right-column wp-block-column">
							<div className="content-clock__contents__count_count">
								<p>{ date }</p>
							</div>
						</div>
					</div>
					<InnerBlocks
						allowedBlocks={ ALLOWED_BLOCKS }
						template={ TEMPLATE }
					/>
				</div>
				<InspectorControls>
					<PanelBody initialOpen title={ __( 'Clock settings', 'shiro-admin' ) }>
						<p>{ __( 'Chose the date and time to count up/down to:', 'shiro-admin' ) }</p>
						<DateTimePicker
							__nextRemoveHelpButton
							__nextRemoveResetButton
							currentDate={ date }
							is12Hour={ false }
							onChange={ date => setAttributes( { date } ) }
						/>
						<ToggleControl
							checked={ stopAtTime }
							label={ __( 'Stop clock at the time above', 'shiro-admin' ) }
							onChange={ stopAtTime => setAttributes( { stopAtTime } ) }
						/>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},

	/**
	 * Render the frontend representation of the clock block.
	 */
	save: function Save( { attributes } ) {
		const blockProps = useBlockProps.save( { className: 'content-clock' } );
		const {
			date,
			stopAtTime,
			title,
		} = attributes;

		return (
			<div { ...blockProps }>
				<RichText.Content
					className="content-clock__title is-style-h3"
					tagName="h2"
					value={ title }
				/>
				<div
					className="content-clock__contents"
					data-date={ date }
					data-stop={ stopAtTime }>
					<InnerBlocks.Content />
				</div>
			</div>
		);
	},
};

