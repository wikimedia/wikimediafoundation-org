import dropdown, { teardown } from './modules/dropdown';

dropdown();
if ( module.hot ) {
	module.hot.accept( './modules/dropdown.js', function () {
		teardown();
		dropdown();
	} );
}
