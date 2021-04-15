/* global shiroEditorVariables */

import classNames from 'classnames';
import PropTypes from 'prop-types';

/**
 * Get an SVG sprite from the theme's sprite-sheet.
 *
 * The sprites can be found in src/svg/individual/ and are referenced by their
 * filenames (without extensions).
 *
 * This component does no validation on the svg
 *
 * @param {object}   props React props.
 * @param {number}   props.svg The (file) name of the sprite.
 * @param {string}   props.className Optional class, added to the <svg> element.
 * @returns {JSX.Element}	A React element containing and SVG definition.
 */
function SvgSprite( props ) {
	const {
		svg,
		className,
	} = props;

	const { themeUrl } = shiroEditorVariables;

	const svgPath = `${themeUrl}/assets/dist/icons.svg#${svg}`;

	return (
		<svg
			className={
				classNames(
					{ [`icon-${svg}`]: svg },
					className
				)
			}>
			<use href={ svgPath }></use>
		</svg>
	);
}

SvgSprite.propTypes = {
	svg: PropTypes.string.isRequired,
	className: PropTypes.string,
};

export default SvgSprite;
