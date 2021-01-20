/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = storiesAtts, // eslint-disable-line no-undef
		containerID = "#" + shortAtts["id"],
		container = $(containerID),
		storyOverlay = container.find(".story-carousel"),
		storyContents = storyOverlay.find(".story-content"),
		storiesLen = storyContents.length,
		currentStory = 0;

	function getRandom(min, max) {
		return (Math.random() * (max - min) + min).toFixed(2);
	}

	function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName;
		for (var i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? 0 : decodeURIComponent(sParameterName[1]);
			}
		}
		return 0;
	}

	function getStoryContent() {
		storyContents.hide();
		$(storyContents[currentStory]).fadeIn();
		container.find(".story-nav .index").text(currentStory + 1 + "/" + storiesLen);
	}

	function scrollToBeginning() {
	    $('html, body').animate({
	        scrollTop: container.offset().top - 100
	    }, 600);
	}

	container.find(".next-story").click(function() {
		if (currentStory < storiesLen - 1) {
			currentStory++;
		} else {
			currentStory = 0;
		}
		getStoryContent();
		scrollToBeginning();
	});

	container.find(".prev-story").click(function() {
		if (currentStory > 0) {
			currentStory--;
		} else {
			currentStory = storiesLen - 1;
		}
		getStoryContent();
		scrollToBeginning();
	});

	function shuffteStories() {
		for (var j = 0; j < storiesLen; j++) {
			var rand = Math.min(Math.floor(getRandom(0, storiesLen)), storiesLen - 1);
			storyContents.eq(rand).insertBefore(storyContents[0]);
		}
	}

	function init() {
		var storyParam = parseInt(getUrlParameter("s"), 10),
			sInRange = storyParam > 0 && storyParam <= storiesLen;
		if (sInRange) {
			console.log(storyParam);
			storyContents.eq(storyParam - 1).insertBefore(storyContents[0]);
		} else {
			shuffteStories();
		}
		storyContents = storyOverlay.find(".story-content"); // reset with new order
		getStoryContent();
	}

	init();
});

