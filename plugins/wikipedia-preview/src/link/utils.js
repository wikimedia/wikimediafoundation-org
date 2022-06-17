export const getSiteLanguage = () => {
	return document.documentElement.getAttribute( 'lang' ).split( '-' )[ 0 ];
};
