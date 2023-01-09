/* eslint-env es6 */
/* eslint-disable */

/**
 * Generic site JavaScript.
 */

/**
 * Search for Donation Button and add parameter link Op1.
 */
document.querySelectorAll('[href^="https://donate.wikimedia.org"]').forEach( function( item ) {
	'use strict';
	var page_id = window.post_id;
	var url = item.href;
	var params = new URLSearchParams( url.search );
	params.set( 'utm_source', page_id );
	item.href = url.replace( /\?.*/, '?utm_source=' + page_id );
}
);
