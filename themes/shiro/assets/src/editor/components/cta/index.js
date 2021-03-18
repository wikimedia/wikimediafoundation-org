import PropTypes from 'prop-types';
import React from 'react';

import { RichText } from '@wordpress/block-editor';
import { withFocusOutside } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import URLPicker from '../url-picker';

/**
 * Renders a component that can be used to set the URL and text for a CTA.
 *
 * The arguments `onChangeText` and `onChangeLink` are used to set attributes
 * when the respective items change. `onChangeText` will receive `{ text }` and
 * `onChangeLink` will receive `{ url }`. Keep in mind that sometimes
 * `onChangeLink` will receive `{ url: undefined }` which is an expected
 * value: This is how the "remove link" functionality works.
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
						onChange={ onChangeLink }
					/>
					<RichText
						// For some reason withoutInteractiveFormatting doesn't
						// work here, but this does.
						allowedFormats={ [] }
						className={ className }
						placeholder={ __( 'Call to action', 'shiro' ) }
						tagName="div"
						value={ text }
						onChange={ onChangeText }
						onFocus={ () => this.setState( { showButtons: true } ) }
					/>
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
 *
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
