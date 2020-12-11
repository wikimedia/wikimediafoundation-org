/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = timelineAtts, // eslint-disable-line no-undef
		containerID = "#" + shortAtts["id"],
		container = $(containerID),
		milestonesWindow = container.find(".milestones-window"),
		milestonesContainer = container.find(".milestones"),
		milestones = container.find(".milestone"),
		total = milestones.length,
		currentMile = 0;

	function setTimelineWidth() {
		var w = Math.min(milestonesWindow.width(), 600);
		milestones.css("width", w + "px");
		milestonesContainer.css("width", w * total + "px");
		updateView();
	}

	function updateView() {
		var posX = -100/total * currentMile;
		milestonesContainer.css("transform", "translateX(" + posX + "%)");
		milestones.each(function(i) {
			if (i === currentMile) {
				$(this).css("opacity", 1);
			} else {
				$(this).css("opacity", 0);
			}
		})
		if (currentMile === 0) {
			container.find("#prev-milestone").removeClass("visible").addClass("hidden");
		} else {
			container.find("#prev-milestone").removeClass("hidden").addClass("visible");
		}
		if (currentMile === total - 1) {
			container.find("#next-milestone").removeClass("visible").addClass("hidden");
		} else {
			container.find("#next-milestone").removeClass("hidden").addClass("visible");
		}
	}

	container.find("#next-milestone").click(function(){
		currentMile++;
		if (currentMile < total) {
			updateView();
		}
	})

	container.find("#prev-milestone").click(function(){
		currentMile--;
		if (currentMile >= 0) {
			updateView();
		}
	})

	$( window ).resize( function() {
		setTimelineWidth();
	});

	setTimelineWidth();

});