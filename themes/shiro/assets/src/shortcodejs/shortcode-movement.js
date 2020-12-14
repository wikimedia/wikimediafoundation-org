/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = movementAtts, // eslint-disable-line no-undef
		container = $("#" + shortAtts['id']),
		tooltip = container.find(".tooltip"),
		tooltipW = tooltip.width();

	function projectMouseenter() {
		var t = $(this),
			r = 20,
			top = t.offset().top - $(window).scrollTop() + r*2,
			left = t.offset().left - tooltipW/2,
			i = parseInt(t.attr("id").replace("project", ""), 10);
		tooltip.find("p").hide();
		tooltip.find("p:nth-child(" + i + ")").show();
		t.attr("r", r).addClass("active");
		tooltip
			.css("transform", "translate(" + left + "px, " + top + "px)")
			.removeClass("hidden").addClass("visible");
	}

	function projectMouseleave() {
		$(this).attr("r", 15);
		tooltip.removeClass("visible").addClass("hidden");
	}

	$(".project-circle").mouseover(projectMouseenter);
	$(".project-circle").mouseleave(projectMouseleave);
	$(document).scroll(projectMouseleave);
});