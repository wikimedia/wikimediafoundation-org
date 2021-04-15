/* global shiroEditorVariables */
import classNames from 'classnames';
import PropTypes from 'prop-types';

/**
 * Get an SVG sprite from the theme's sprite-sheet.
 *
 * The sprites can be found in src/svg/individual/ and are referenced by their
 * filenames (without extensions).
 *
 * The component will check that the `svg` argument doesn't contain anything
 * obviously unpleasant, but it will *not* check to see if the sprite you're
 * asking for exists. If the argument is bad, it returns null; otherwise it
 * returns an SVG that will just point to a nonexistent sprite and therefore
 * be invisible.
 *
 * @param {object}   props React props.
 * @param {number}   props.svg The (file) name of the sprite.
 * @param {string}   props.className Optional class, added to the <svg> element.
 */
function SvgSprite( props ) {
	const {
		svg,
		className,
	} = props;

	const { themeUrl } = shiroEditorVariables;

	// This will ultimately be embedded in an SVG and cause a server request,
	// so let's do some due diligence to make sure it doesn't contain
	// something untoward.
	if ( ! /^[\w|\-|.|\s]*$/gm.test( svg ) ) {
		return null;
	}

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
