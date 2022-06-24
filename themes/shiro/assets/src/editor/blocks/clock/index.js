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
import clockBlock, { wrapCharacters } from '../../../scripts/clock-block';

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
		title: {
			type: 'string',
			source: 'html',
			selector: '.clock__title',
		},
		stopAtTime: {
			type: 'boolean',
			default: false,
		},
		date: {
			type: 'string',
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
			stopAtTime,
			title,
		} = attributes;

		const ALLOWED_BLOCKS = [
			'shiro/clock-stat',
		];

		// Setup the counter.
		useEffect( () => {
			clockBlock();
		}, [ date, stopAtTime ] );

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
					data-stop={ stopAtTime ?? false }
				>
					<div className="clock__contents__count wp-block-columns">
						<div className="clock__contents-left-column wp-block-column">
							<div className="clock__contents__count-count">{ date }</div>
						</div>
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
		const blockProps = useBlockProps.save( { className: 'clock' } );
		const {
			countTitle,
			date,
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
					data-stop={ stopAtTime ?? false }
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
			</div>
		);
	},
};

