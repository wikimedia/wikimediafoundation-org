/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = gridAtts, // eslint-disable-line no-undef
		containerID = "#" + shortAtts["id"],
		container = $(containerID),
		containerW = container.width(),
		symbols = container.find(".grid-item.grid-symbol"),
		text1 = container.find(".grid-item.grid-text.grid-text-1"),
		text2 = container.find(".grid-item.grid-text.grid-text-2"),
		text3 = container.find(".grid-item.grid-text.grid-text-3"),
		allTexts = [text1, text2, text3];

 	function animateText(text, i, iter) {
 		var opacity = iter % 2,
 			delay = iter === 0 ? 6000 + i * 1200 : 9000;
 		if (containerW >= 768) {
			text
				.delay(delay)
				.animate({opacity: opacity}, {
					duration: 200,
					start: function() {
						text.find("h2").hide();
						text.css("z-index", 1);
					},
					complete: function() {
						var newIter = iter + 1;
						text.find("h2").fadeIn();
						if (opacity === 0) {
							text.css("z-index", -1);
						}
						animateText(text, i, newIter);
					}
				});

 		}
 	}

 	function animateSymbols() {
 		var rand = Math.floor(Math.random() * symbols.length);
 		$(symbols[rand]).toggleClass("reverse");
 		setTimeout(animateSymbols, 1200);
 	}

 	function initAnim() {
		for (var i = 0; i < allTexts.length; i++) {
			animateText(allTexts[i], i, 0);
		}
		animateSymbols();
 	}

 	symbols.mouseenter(function() {
 		$(this).toggleClass("reverse");
 	});

	setTimeout(initAnim, 4000);
});