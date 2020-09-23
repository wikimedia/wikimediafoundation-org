/* eslint-disable */
/* global d3 */
/* eslint-disable no-magic-numbers */

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = timelineAtts, // eslint-disable-line no-undef
		containerID = "#" + timelineAtts["id"],
		container = $(containerID),
		startYear = 2001,
		endYear = 2021,
		dataRaw = [
			{ "date": "2001-01-15", "label": "Milestone 1", "description": "Proident in aliqua qui mollit proident nisi culpa id in sit laboris tempor in consectetur aliquip dolore in." },
			{ "date": "2003-04-01", "label": "Milestone 2", "description": "Officia id eiusmod ut pariatur in in esse aliquip in qui quis eiusmod elit.", "call": "Sign up for an Edit-a-thon" },
			{ "date": "2007-02-15", "label": "Milestone 3", "description": "Voluptate culpa veniam et dolor qui voluptate ut incididunt anim eu ut dolore nostrud et elit culpa occaecat labore." },
			{ "date": "2010-07-01", "label": "Milestone 4", "description": "Mollit et amet adipisicing aliqua aliqua cupidatat commodo reprehenderit." },
			{ "date": "2013-10-30", "label": "Milestone 5", "description": "Lorem ipsum nostrud aliquip cillum aliqua duis elit ullamco occaecat enim labore incididunt elit eiusmod." },
			{ "date": "2016-10-30", "label": "Milestone 6", "description": "Magna ex. Lorem ipsum mollit deserunt in est nulla sunt adipisicing duis anim irure." },
			{ "date": "2017-10-30", "label": "Milestone 7", "description": "Lorem ipsum velit cupidatat est occaecat. Veniam eiusmod incididunt sint dolore adipisicing voluptate consequat dolore eiusmod." },
			{ "date": "2018-10-30", "label": "Milestone 8", "description": "Veniam enim ut magna. Pariatur culpa veniam ut adipisicing eu sunt laboris tempor dolore ad." },
			{ "date": "2020-01-10", "label": "Milestone 9", "description": "Enim tempor excepteur dolor nulla id commodo elit enim ut minim tempor excepteur ad nisi." }
		];
	console.log("timeline", shortAtts);

	for (var i = 0; i < endYear - startYear + 1; i++) {
		container
			.append( $( '<div id="year-' + (startYear + i) + '" class="year"></div>' )
				.append( $('<div class="top-articles"></div>')
					.append( $( '<div class="top-edited"></div>') )
					.append( $( '<div class="top-viewed"></div>') )
				)
				.append( $( '<p class="year-label"></p>').text(startYear + i) )
				.append( $( '<div class="milestone"></div>'))
			);
	}

	for (var i = 0; i < dataRaw.length; i++) {
		var date = new Date(dataRaw[i].date),
			year = date.getFullYear().toString(),
			thisYear = $("#year-" + year);
		thisYear.addClass("highlight");
		thisYear.find(".milestone")
			.append( $("<h3></h3>").text(dataRaw[i].label) )
			.append( $("<p></p>").text(dataRaw[i].description) );
		// if (dataRaw[i].call) {
		// 	thisYear.find(".milestone").append( $('<p class="call"></p>').text(dataRaw[i].call) );
		// }
		thisYear.find(".top-viewed").append("<span></span>");
		thisYear.find(".top-edited").append("<span></span>");
	}

	function changeCategory() {
		$(".filter-timeline").find(".cat").removeClass("active");		
		$(this).addClass("active");
		container.animate({"opacity": 0}, 300, function(){
			container.animate({"opacity": 1}, 300);
		});
	}

	$(".filter-timeline").find(".cat").click(changeCategory);

});