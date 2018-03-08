/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	$( '#translate_post_global' ).change( function(){
		var checked = $( this ).is( ':checked' );
		$( 'input[name="mlp_to_translate[]"]' ).prop( 'checked', checked );
	} );

} )( jQuery );
