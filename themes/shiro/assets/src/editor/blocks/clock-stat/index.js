/**
 * Block used internally in the clock block for stats.
 */
import React from 'react';

import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';

export const name = 'shiro/clock-stat';

export const settings = {
	icon: 'star-filled',
	title: __( 'Clock Stat', 'shiro-admin' ),
	category: 'wikimedia',
	description: __(
		'Adds a stat row to the clock block.',
		'shiro-admin'
	),
	supports: {
		multiple: true,
	},
	attributes: {
		label: {
			type: 'string',
			source: 'html',
			selector: '.clock-stat__label',
		},
		stat: {
			type: 'string',
			source: 'html',
			selector: '.clock-stat__stat',
		},
	},
	parent: [ 'shiro/clock' ],

	/**
	 * Edit component used to manage the clock block.
	 */
	edit: function ClockBlock( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'clock-stat' } );
		const {
			label,
			stat,
		} = attributes;

		return (
			<div { ...blockProps }>
				<div className="clock__contents wp-block-columns">
					<div className="clock-stat__left-column wp-block-column">
						<RichText
							className="clock-stat__stat"
							keepPlaceholderOnFocus
							placeholder={ __( 'Stat', 'shiro-admin' ) }
							tagName="p"
							value={ stat }
							onChange={ value => setAttributes( { stat: wrapCharacters( value ) } ) }
						/>
					</div>
					<span className="clock-stat__divider">:</span>
					<div className="clock-stat__right-column wp-block-column">
						<RichText
							className="clock-stat__label"
							keepPlaceholderOnFocus
							placeholder={ __( 'Label', 'shiro-admin' ) }
							tagName="p"
							value={ label }
							onChange={ value => setAttributes( { label: wrapCharacters( value ) } ) }
						/>
					</div>
				</div>
			</div>
		);
	},

	/**
	 * Render the frontend representation of the clock block.
	 */
	save: function Save( { attributes } ) {
		const blockProps = useBlockProps.save( { className: 'clock-stat' } );
		const {
			label,
			stat,
		} = attributes;

		return (
			<div { ...blockProps }>
				<div className="clock-stat__left-column wp-block-column">
					<RichText.Content
						className="clock-stat__stat"
						tagName="p"
						value={ label }
					/>
				</div>
				<div className="clock__contents-right-column wp-block-column">
					<RichText.Content
						className="clock-stat__label"
						tagName="p"
						value={ stat }
					/>
				</div>
			</div>
		);
	},
};

/**
 * Wrap all of the characters in the string with a span tag.
 *
 * Removes any HTML elements in the string before performing operation.
 *
 * @param {string} string String to wrap.
 * @returns {string} String of wrapped characters.
 */
const wrapCharacters = string => {
	// Strip html.
	string = string.replace( /(<([^>]+)>)/gi, '' );
	// Split up the characters.
	let stringArray = string.split( '' );
	// Add a <span> around the characters
	stringArray = stringArray.map( char => '<span>'+char+'</span>' );
	// Re-construct.
	return stringArray.join( '' );
};
