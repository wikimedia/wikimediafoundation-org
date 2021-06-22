import classNames from 'classnames';
import PropTypes from 'prop-types';

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const options = [
	{
		label: __( 'No filter', 'shiro-admin' ),
		value: '',
	},
	{
		label: __( 'Inherit from block style', 'shiro-admin' ),
		value: 'inherit',
	},
	{
		label: __( 'Blue', 'shiro-admin' ),
		value: 'blue',
	},
	{
		label: __( 'Red', 'shiro-admin' ),
		value: 'red',
	},
	{
		label: __( 'Yellow', 'shiro-admin' ),
		value: 'yellow',
	},
];

export const DEFAULT_IMAGE_FILTER = 'image-filter-inherit';

/**
 * Render image filter in the editor including the block settings for it.
 */
function ImageFilter( props ) {
	const {
		value = '',
		onChange,
		className,
		...otherProps
	} = props;

	const activeFilter = value.replace( 'image-filter-', '' );

	return (
		<>
			<figure className={ classNames( className, value ) } { ...otherProps }>
				{ props.children }
			</figure>
			<InspectorControls>
				<PanelBody title={ __( 'Image settings', 'shiro-admin' ) }>
					<SelectControl
						label={ __( 'Image filter color', 'shiro-admin' ) }
						options={ options }
						value={ activeFilter }
						onChange={ newFilter => onChange( 'image-filter-' + newFilter ) }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);
}

ImageFilter.propTypes = {
	value: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
};

/**
 * Render frontend content for the image filter.
 */
ImageFilter.Content = props => {
	const { className = '', value = '', ...otherProps } = props;

	return (
		<figure className={ classNames( className, value ) } { ...otherProps }>
			{ props.children }
		</figure>
	);
};

ImageFilter.Content.propTypes = {
	value: PropTypes.string.isRequired,
	className: PropTypes.string,
};

export default ImageFilter;
