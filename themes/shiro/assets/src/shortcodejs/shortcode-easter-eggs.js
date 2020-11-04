/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var atts = eggsAtts, // eslint-disable-line no-undef
		targetWord = atts['search'],
		containers = '.top-articles p, .milestone p, .movement p, .section p',
		targets = containers.replaceAll(" p", " p > span") + ', .top-articles p > strong > span, .milestone p > strong > span',
		eggContent = $(".easter-egg-container"),
		hiIndexArr = atts['highlight_index'].split("|").map(function(d){ return parseInt(d, 10) - 1; }), // -1, input e.g. 1, true i = 0
		indexArr = atts['index_list'].split("|").map(function(d){ return parseInt(d, 10) - 1; }), // see above
		pattern = new RegExp('(' + targetWord + ')([\\s’])', 'ig'), // target whole words globally and case insensitive
		replaceWith = '<span>$1</span>$2'; // wrap in the tag

	function showEggContent(target, index) {
		var t = $(target),
			allFacts = eggContent.find(".easter-egg-content"),
			fact = $( allFacts.slice(index, index + 1) );
		allFacts.hide();
		fact.show();
		if (fact.text().length > 0) {
			var	tTop = t.offset().top,
				eggH = eggContent.outerHeight(),
				eggW = eggContent.outerWidth(),
				fitBelow = eggH < window.innerHeight - (tTop - $(window).scrollTop()),
				top = fitBelow ? tTop : tTop - eggH,
				left = t.offset().left,
				yShift = fitBelow ? t.height() * 1.5 : -t.height() * 0.5,
				xShift = eggW > window.innerWidth - left ? window.innerWidth - eggW - 10 - left : 0;
			eggContent
				.css("top", top + "px")
				.css("left", left + "px")
				.css("transform", "translate(" + xShift + "px," + yShift + "px)")
				.fadeIn();
		}
	}

	function hoverOrHide() {
		if ($('.easter-egg-container:hover').length === 0) {
			eggContent.hide();
		}
	}

	function useTargets(sel1) {
		var highlight = sel1.filter(function(i) { return hiIndexArr.indexOf(i) > -1; }),
			notHighlight = sel1.filter(function(i) { return indexArr.indexOf(i) > -1; });
		highlight.addClass("easter-egg egg-highlight");
		highlight.mouseover(function(ep) {
			notHighlight.addClass("easter-egg");
			highlight.off("mouseover");
			var easterEggs = $(".easter-egg");
			showEggContent(ep.target, easterEggs.index(this));
			easterEggs.mouseover(function(e) {
				showEggContent(e.target, easterEggs.index(this));
			});
			easterEggs.mouseleave(function() {
				setTimeout(hoverOrHide, 200);
			});
			eggContent.mouseleave(function() {
				eggContent.hide();
			});
		});
	}

	$(containers).each(function(){
		if ($(this).text().indexOf(targetWord) > -1) {
			$(this).html($(this).html().replace(pattern,replaceWith));
		}
	});
	useTargets($(targets));

});

