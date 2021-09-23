const defaultScenario = {
	referenceUrl: '',
	// cookiePath: 'backstop_data/engine_scripts/cookies.json',
	readyEvent: '',
	readySelector: '',
	delay: 0,
	removeSelectors: [
		'#query-monitor-main',
		'#a8c-debug-flag',
	],
	hoverSelector: '',
	clickSelector: '',
	postInteractionWait: 0,
	selectors: [],
	selectorExpansion: true,
	expect: 0,
	misMatchThreshold: 0.1,
	requireSameDimensions: false,
};
const rootUrl = 'http://wikimediafoundation.test/';

module.exports = {
	id: 'wikimedia',
	viewports: [
		{
			label: 'phone',
			width: 320,
			height: 480,
		},
		{
			label: 'tablet',
			width: 1024,
			height: 768,
		},
		{
			label: 'desktop',
			width: 1366,
			height: 1024,
		},
	],
	onBeforeScript: 'puppet/onBefore.js',
	onReadyScript: 'puppet/onReady.js',
	scenarios: [
		{
			label: 'Wikimedia Homepage',
			url: rootUrl,
			hideSelectors: [ '.header-bg-img' ],
			...defaultScenario,
		},
		{
			label: 'Our Work',
			url: `${rootUrl}our-work`,
			...defaultScenario,
		},
		{
			label: 'News',
			url: `${rootUrl}news`,
			...defaultScenario,
		},
		{
			label: 'Support Wikipedia',
			url: `${rootUrl}support`,
			...defaultScenario,
		},
		{
			label: 'Contact',
			url: `${rootUrl}about/contact`,
			...defaultScenario,
		},
		{
			label: 'About',
			url: `${rootUrl}about`,
			...defaultScenario,
		},
		{
			label: 'Work with us',
			url: `${rootUrl}about/jobs`,
			...defaultScenario,
		},
		{
			label: 'Wikipedia20',
			url: `${rootUrl}wikipedia20`,
			...defaultScenario,
		},
	],
	paths: {
		bitmaps_reference: 'backstop_data/bitmaps_reference',
		bitmaps_test: 'backstop_data/bitmaps_test',
		engine_scripts: 'backstop_data/engine_scripts',
		html_report: 'backstop_data/html_report',
		ci_report: 'backstop_data/ci_report',
	},
	report: [],
	engine: 'puppeteer',
	engineOptions: {
		args: [ '--no-sandbox' ],
	},
	asyncCaptureLimit: 5,
	asyncCompareLimit: 50,
	debug: false,
	debugWindow: false,
};
