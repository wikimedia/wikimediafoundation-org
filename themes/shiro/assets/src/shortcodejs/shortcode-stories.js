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

	for (var j = 0; j < storiesLen; j++) {
		var rand = Math.min(Math.floor(getRandom(0, storiesLen)), storiesLen - 1);
		storyContents.eq(rand).insertBefore(storyContents[0]);
	}
	storyContents = storyOverlay.find(".story-content"); // reset with new order
	getStoryContent();

});

