import React from 'react';

import { RichText } from '@wordpress/block-editor';
import { withFocusOutside } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import URLPicker from '../url-picker';

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
				setAttributes,
				url,
			} = this.props;

			return (
				<>
					<URLPicker
						isSelected={ showButtons }
						setAttributes={ setAttributes }
						url={ url }
					/>
					<RichText
						// For some reason withoutInteractiveFormatting doesn't
						// work here, but this does.
						allowedFormats={ [] }
						className="banner__cta"
						placeholder={ __( 'Call to action', 'shiro' ) }
						tagName="div"
						value={ text }
						onChange={ text => setAttributes( { buttonText: text } ) }
						onFocus={ () => this.setState( { showButtons: true } ) }
					/>
				</>
			);
		}
	}
);

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

export default CtaWithFocusOutside;
