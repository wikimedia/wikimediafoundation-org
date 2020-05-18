/* global d3 */
/* eslint-disable no-magic-numbers */

jQuery(document).ready(function($) {

	'use strict';

	function dataProfiles(id) {
		console.log(d3, id);
	}

	$(".d3-dataprofiles").each(function() {
		dataProfiles(d3, this.id);
	});


});