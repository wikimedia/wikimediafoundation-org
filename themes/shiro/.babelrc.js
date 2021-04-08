module.exports = ( api ) => {
	api.cache.forever();

	return {
		presets: [ '@wordpress/default' ],
		plugins: [
			'@babel/plugin-proposal-class-properties',
			[ 'transform-react-jsx', {
				pragma: 'wp.element.createElement',
			} ],
			[ "@wordpress/babel-plugin-makepot", {
				"output": "languages/shiro-js.pot"
			} ]
		],
	};
};
