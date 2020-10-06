/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var atts = topAtts, // eslint-disable-line no-undef
		select = $("#year-select"),
		contents = $(".top-data-content"),
		data = [];

	function populateData(year) {
		var filterD = data.filter(function(d) { return d.year === year; }),
			langs = filterD.map(function(d) { return d.wiki; })
		contents.each(function() {
			var langContainer = $(this),
				id = $(this).attr('id'),
				content = filterD.find(function(d) {return d.wiki === id; })
			langContainer.hide();
			if ( langs.indexOf(id) > -1 ) {
				langContainer.find(".heading").text(content.page_title.replaceAll("_", " "));
				langContainer.find(".desc").text(content["desc_" + atts.lang.replaceAll("wiki", "")].replaceAll('"', ""));
				langContainer.fadeIn();
			}
		});
	}

	function getSelectedYear() {
		var year = "";
	    $("#year-select option:selected").each(function() {
			year += $( this ).text() + " ";
	    });
	    populateData(parseInt(year, 10));
	}

	function setup() {
		contents.each(function(){
			$(this).hide();
		});
	}

	setup();
	$.getJSON(atts.path, function(json) {
		data = json;
		getSelectedYear();
	});

	select.change(getSelectedYear);
});

