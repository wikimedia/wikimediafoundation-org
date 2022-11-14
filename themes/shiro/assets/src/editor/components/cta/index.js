import classNames from 'classnames';
import PropTypes from 'prop-types';
import React from 'react';

import { RichText } from '@wordpress/block-editor';
import { withFocusOutside } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import './style.scss';
import URLPicker from '../url-picker';

/**
 * Render a component that can be used to set the URL and text for a CTA.
 *
 * The arguments `onChangeText` and `onChangeLink` are used to set attributes
 * when the respective items change. `onChangeText` will receive `text` and
 * `onChangeLink` will receive `url` (and a second parameter which contains
 * additional data if the selected link is an internal resource, like 'ID',
 * 'title', and 'postType'). Keep in mind that sometimes `onChangeLink` will
 * receive `undefined` which is an expected value: This is how the "remove
 * link" functionality works.
 *
 * `withFocusOutside()` is necessary here (paired with the `unstableOnFocus` attribute)
 * in order to show & hide the button on the toolbar when the CTA is focused
 * in the editor (or not).
 */
const CtaWithFocusOutside = withFocusOutside(
	class extends React.Component {
		constructor( props ) {
			super( props );
			this.state = {
				showButtons: false,
			};
		}

		handleFocusOutside() {
			this.setState( { showButtons: false } );
		}

		render() {
			const { showButtons } = this.state;
			const {
				text,
				onChangeText,
				onChangeLink,
				className,
				url,
			} = this.props;

			return (
				<>
					<URLPicker
						isSelected={ showButtons }
						url={ url }
						onChangeLink={ onChangeLink }
					/>
					<div className={
						classNames(
							'call-to-action-wrapper',
							{ 'call-to-action--no-url': ! url }
						)
					}>
						<RichText
						// For some reason withoutInteractiveFormatting doesn't
						// work here, but this does.
							allowedFormats={ [] }
							className={
								classNames(
									'call-to-action',
									className
								)
							}
							placeholder={ __( 'Call to action', 'shiro-admin' ) }
							tagName="div"
							unstableOnFocus={ () => this.setState( { showButtons: true } ) }
							value={ text }
							onChange={ onChangeText }
						/>
						{ ! url && <div className={ 'call-to-action__warning' }>
							<span aria-label={ __( 'Warning', 'shiro-admin' ) } role={ 'img' }>⚠️</span>
						&nbsp;
							<span>{ __( 'Add a URL to this CTA', 'shiro-admin' ) }</span>
						</div> }
					</div>
				</>
			);
		}
	}
);

CtaWithFocusOutside.propTypes = {
	text: PropTypes.string,
	onChangeText: PropTypes.func.isRequired,
	onChangeLink: PropTypes.func.isRequired,
	className: PropTypes.string,
	url: PropTypes.string,
};

/**
 * Provide a ready-made element for `save()`.
 */
CtaWithFocusOutside.Content = ( { url, text, className, ...props } ) => {
	if ( ! url ) {
		return null;
	}

	return (
		<a
			className={ className }
			href={ url }
			{ ...props }>{ text }</a>
	);
};

CtaWithFocusOutside.Content.propTypes = {
	url: PropTypes.string,
	text: PropTypes.string,
	className: PropTypes.string,
};

export default CtaWithFocusOutside;
