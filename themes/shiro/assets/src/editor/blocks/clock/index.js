/**
 * Clock block that provides statistics and a count down/up timer.
 */

/**
 * WordPress dependencies
 */
import { useBlockProps, InspectorControls, RichText, InnerBlocks } from '@wordpress/block-editor';
import { DateTimePicker, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import clockBlock, { wrapCharacters } from '../../../scripts/clock-block';

const displayOptions = [
	{
		value: 'd-nolabel',
		label: 'Days (No Label)',
	},
	{
		value: 'd',
		label: 'Days',
	},
	{
		value: 'dh',
		label: 'Days Hours',
	},
	{
		value: 'dhm',
		label: 'Days Hours Minutes',
	},
	{
		value: 'dhms',
		label: 'Days Hours Minutes Seconds',
	},
];

export const name = 'shiro/clock';

export const settings = {
	apiVersion: 2,

	icon: 'star-filled',

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
			selector: '.clock__contents__count-label',
		},
		date: {
			type: 'string',
		},
		disclaimer: {
			type: 'string',
			source: 'html',
			selector: '.clock__contents__disclaimer',
		},
		display: {
			type: 'string',
			default: displayOptions[0]['value'],
		},
		displayPadding: {
			type: 'string',
			default: '0',
		},
		stopAtTime: {
			type: 'boolean',
			default: false,
		},
		title: {
			type: 'string',
			source: 'html',
			selector: '.clock__title',
		},
	},

	/**
	 * Edit component used to manage the clock block.
	 */
	edit: function ClockBlock( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'clock' } );
		const {
			countTitle,
			date,
			disclaimer,
			display,
			displayPadding,
			stopAtTime,
			title,
		} = attributes;

		const ALLOWED_BLOCKS = [
			'shiro/clock-stat',
		];

		// Setup the counter.
		useEffect( () => {
			clockBlock();
		}, [ date, stopAtTime, display, displayPadding ] );

		return (
			<div { ...blockProps }>
				<RichText
					className="clock__title is-style-h3"
					keepPlaceholderOnFocus
					placeholder={ __( 'Title of the clock', 'shiro-admin' ) }
					tagName="h2"
					value={ title }
					onChange={ title => setAttributes( { title } ) }
				/>
				<div
					className="clock__contents"
					data-clock={ date }
					data-display={ display }
					data-displaypadding={ displayPadding }
					data-stop={ stopAtTime ?? false }
				>
					<div className="clock__contents__count wp-block-columns">
						<div className="clock__contents-left-column wp-block-column">
							<div className="clock__contents__count-count">{ date }</div>
						</div>
						<span className="clock-stat__divider">:</span>
						<div className="clock__contents-right-column wp-block-column">
							<RichText
								className="clock__contents__count-label"
								keepPlaceholderOnFocus
								placeholder={ __( 'Label for Counter:', 'shiro-admin' ) }
								tagName="div"
								value={ countTitle }
								onChange={ value => setAttributes( { countTitle: wrapCharacters( value ) } ) }
							/>
						</div>
					</div>
					<InnerBlocks
						allowedBlocks={ ALLOWED_BLOCKS }
					/>
					<RichText
						className="clock__contents__disclaimer"
						keepPlaceholderOnFocus
						placeholder={ __( 'Disclaimers', 'shiro-admin' ) }
						tagName="div"
						value={ disclaimer }
						onChange={ disclaimer => setAttributes( { disclaimer } ) }
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
						<SelectControl
							help={ __( 'Units to display in clock', 'shiro-admin' ) }
							label={ __( 'Clock Display', 'shiro' ) }
							options={ displayOptions }
							value={ display }
							onChange={ display => setAttributes( { display } ) }
						/>
						<TextControl
							help={ __( 'Number of empty values before a unit. Minimum 0 and Maximum 5', 'shiro-admin' ) }
							label={ __( 'Minimum length of display', 'shiro-admin' ) }
							value={ displayPadding }
							onChange={ displayPadding => {
								let padding = ( displayPadding ) ? parseInt( displayPadding ) : 0;
								if ( padding > 5 || padding < 0 ) {
									padding = 0;
								}
								setAttributes( { displayPadding: padding.toString() } );
							} }
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
		const blockProps = useBlockProps.save( { className: 'clock' } );
		const {
			countTitle,
			date,
			disclaimer,
			display,
			displayPadding,
			stopAtTime,
			title,
		} = attributes;

		return (
			<div { ...blockProps }>
				<RichText.Content
					className="clock__title is-style-h3"
					tagName="h2"
					value={ title }
				/>
				<div
					className="clock__contents"
					data-clock={ date }
					data-display={ display }
					data-displaypadding={ displayPadding }
					data-stop={ stopAtTime }
				>
					<div className="clock__contents wp-block-columns">
						<div className="clock__contents-left-column wp-block-column">
							<div className="clock__contents__count-count">
							</div>
						</div>
						<div className="clock__contents-right-column wp-block-column">
							<RichText.Content
								className="clock__contents__count-label"
								tagName="div"
								value={ countTitle }
							/>
						</div>
					</div>
				</div>
				<InnerBlocks.Content />
				<RichText.Content
					className="clock__contents__disclaimer"
					tagName="div"
					value={ disclaimer }
				/>
			</div>
		);
	},
};

