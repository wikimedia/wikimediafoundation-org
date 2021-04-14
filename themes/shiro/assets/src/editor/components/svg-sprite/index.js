import classNames from 'classnames';
import PropTypes from 'prop-types';

/**
 *
 */
function SvgSprite( props ) {
	const {
		svg,
		className,
	} = props;

	const svgPath = `${shiro_theme_dir_uri}assets/dist/icons.svg#${svg}`;

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
	class: PropTypes.string,
};

export default SvgSprite;
