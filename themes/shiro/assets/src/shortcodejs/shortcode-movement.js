/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = movementAtts, // eslint-disable-line no-undef
		tooltip = $(".tooltip"),
		tooltipW = tooltip.width(),
		projectDesc = shortAtts.project_desc.split("|");

	function projectMouseenter() {
		var t = $(this),
			title = t.attr("data-title"),
			r = 20,
			top = t.offset().top - $(window).scrollTop() + r*2,
			left = t.offset().left - tooltipW/2,
			i = parseInt(t.attr("id").replace("project", ""), 10) - 1,
			fill = t.attr("data-color");
		t
			.attr("r", r)
			.css("fill", fill);
		tooltip.append($("<p></p>").css("font-weight", "bold").text(title));
		tooltip.append($("<p></p>").text(projectDesc[i]));
		tooltip
			.css("transform", "translate(" + left + "px, " + top + "px)")
			.removeClass("hidden").addClass("visible");
	}

	function projectMouseleave() {
		$(this)
			.attr("r", 15);
		tooltip
			.removeClass("visible").addClass("hidden")
			.text("");
	}

	$(".project-circle").mouseover(projectMouseenter);
	$(".project-circle").mouseleave(projectMouseleave);
	$(document).scroll(projectMouseleave);
});