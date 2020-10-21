/* global d3 */
/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = collageAtts, // eslint-disable-line no-undef
		langList = ["EN", "DE", "ZH", "AR", "FR", "ES", "RU"],
		langListLong = ["in English Wikipedia", "in German Wikipedia", "in Chinese Wikipedia", "in Arabic Wikipedia", "in French Wikipedia", "in Spanish Wikipedia", "in Russian Wikipedia"],
		containerID = "#" + shortAtts["id"],
		container = $(containerID),
		notFixedContent = container.parent().siblings().first(),
		fakeScroll = container.parent().find(".fake-scroll"),
		body = $("body"),
		html = $("html"),
		heading = container.find("h1"),
		intro1 = container.find("#intro-1"),
		intro2 = container.find("#intro-2"),
		rEditTicker = container.find(".recent-edits"),
		rEditLabel = rEditTicker.find(".label"),
		rEditTitle = rEditTicker.find(".title"),
		storyOverlay = container.find(".story-overlay"),
		header = $("header"),
		initWidth = html.width(),
		initHeight = window.innerHeight,
		mobileWidth = 500,
		colorBlack = "#202122",
		scrollAnimLength = 4000,
		stories = [{"x":0.56,"y":0.62},{"x":0.7,"y":0.52},{"x":0.51,"y":0.68},{"x":0.73,"y":0.38},{"x":0.28,"y":0.58},{"x":0.41,"y":0.59},{"x":0.48,"y":0.31},{"x":0.71,"y":0.60},{"x":0.65,"y":0.29},{"x":0.35,"y":0.33},{"x":0.24,"y":0.41},{"x":0.34,"y":0.69},{"x":0.57,"y":0.24},{"x":0.4,"y":0.23},{"x":0.28,"y":0.27},{"x":0.29,"y":0.48}, {"x":0.66,"y":0.69}, {"x":0.75,"y":0.46},{"x":0.74,"y":0.32},{"x":0.49,"y":0.2}],
		storyColors = shortAtts['story_rgba'].split("|"),
		cueStoryI = 8,
		showCue = true,
		storyContents = storyOverlay.find(".story-content"),
		randomData = [],
		apilimit = 5,
		randomDataLen = Math.max(langList.length * apilimit, 70),
		storiesLen = storyContents.length,
		blobR = 5,
		bigBlobR = blobR * 2,
		blobStroke = bigBlobR * 3,
		scene1 = 0.1,
		scene1_1 = 0.2,
		scene2 = 0.4,
		scene3 = 0.92,
		sceneTran = 0.1,
		linearUp = d3.scaleLinear().domain([scene2, scene2 + sceneTran]).range([0,1]),
		linearDown = d3.scaleLinear().domain([scene3 - sceneTran, scene3]).range([1,0]),
		zoomFactorMax = 1.55,
		svg, g, y, blobs, x, zoom, storyBlobs, clickCue, pulse, fadedEdge, ornaments,
		ornamentArr = [{
			"path": "M2.144 15.73C3.24 10.737 5.915-.236 13.021 5.197c3.698 2.829 6.35 11.588 12.43 8.287 3.736-2.029 9.713-14.497 14.674-10.704 3.657 2.797 7.793 11.583 13.638 9.495C56.88 11.164 65.928.963 68.955 3.99c6.312 6.311 7.397 9.127 15.883 2.417 8.494-6.717 7.235-2.077 13.638 3.97 2.544 2.403 9.622.091 10.876-2.416",
			"x"	: 0.1,
			"y" : 0.15,
			"r": 90,
		}, {
			"path": "M2.144 15.73C3.24 10.737 5.915-.236 13.021 5.197c3.698 2.829 6.35 11.588 12.43 8.287 3.736-2.029 9.713-14.497 14.674-10.704 3.657 2.797 7.793 11.583 13.638 9.495C56.88 11.164 65.928.963 68.955 3.99c6.312 6.311 7.397 9.127 15.883 2.417 8.494-6.717 7.235-2.077 13.638 3.97 2.544 2.403 9.622.091 10.876-2.416",
			"x"	: 0.62,
			"y" : 0.66,
			"r": 90,
		}, {
			"path": "M41.286 5.24C31.346-1.987 23.23 1.895 22.64 15.773c-.23 5.407.69 9.012 4.661 12.775 2.71 2.567 4.086-.46 2.762-3.108C28.99 23.29 18.014 18.493 10 21.5-.5 25.44.425 40.745 7.103 44.084",
			"x"	: 0.79,
			"y" : 0.87,
			"r": 0,
		}, {
			"path": "M41.286 5.24C31.346-1.987 23.23 1.895 22.64 15.773c-.23 5.407.69 9.012 4.661 12.775 2.71 2.567 4.086-.46 2.762-3.108C28.99 23.29 18.014 18.493 10 21.5-.5 25.44.425 40.745 7.103 44.084",
			"x"	: 0.5,
			"y" : 0.05,
			"r": 85,
		}, {
			"path": "M2 2c1.3 3.96 8.346 24.812 15.63 15.01 1.913-2.574 1.124-9.324-3.16-8.69-3.538.524-4.95 7.897-5.181 10.62-.542 6.372.877 13.801 5.356 18.61C23.46 47.013 42.616 42.798 51 35.18",
			"x"	: 0.85,
			"y" : 0.1,
			"r": 0,
		}, {
			"path": "M2 2c1.3 3.96 8.346 24.812 15.63 15.01 1.913-2.574 1.124-9.324-3.16-8.69-3.538.524-4.95 7.897-5.181 10.62-.542 6.372.877 13.801 5.356 18.61C23.46 47.013 42.616 42.798 51 35.18",
			"x"	: 0.15,
			"y" : 0.85,
			"r": -100,
		}],
		rEditAnimationI = 0,
		rEdits = [],
		currentStory = 0;

	while (randomData.length < randomDataLen) {
		var randx = getRandom(0,1),
			randy = getRandom(0,1);
		if (0.35 < randx && randx < 0.6 && 0.35 < randy && randy < 0.6) {
			continue;
		} else {
			randomData.push({x:randx,y:randy});
		}
	}

	function getRandom(min, max) {
		return (Math.random() * (max - min) + min).toFixed(2);
	}

	function distance(a, b) {
		var center = {x: 0.5, y: 0.5},
			ax = parseFloat(a.x),
			bx = parseFloat(b.x),
			ay = parseFloat(a.y),
			by = parseFloat(b.y),
			distancea = (ax - center.x) * (ax - center.x) + (ay - center.y) * (ay - center.y),
			distanceb = (bx - center.x) * (bx - center.x) + (by - center.y) * (by - center.y);
		return distancea - distanceb;
	}

	function recentEditUrl(lang, isoStart, isoEnd) {
		// https://www.mediawiki.org/wiki/API:RecentChanges
		var url = "https://" + lang.toLowerCase() + ".wikipedia.org/w/api.php",
			limit = apilimit,
			params = {
				action: "query",
				list: "recentchanges",
				rcnamespace: "0",
				rcprop: "title|flags|timestamp",
				rcstart: isoStart,
				rcend: isoEnd,
				rctype: "edit",
				rcshow: "!bot",
				rclimit: limit,
				format: "json"
			};

		url += "?origin=*";
		Object.keys(params).forEach(function(key){url += "&" + key + "=" + params[key];});
		return url;
	}

	function getRecentEdits(force) {
		if (document.hasFocus() || force) {
			var start = new Date(),
				isoStart = start.toISOString(),
				minutes = 60,
				end = new Date(start.getTime() - minutes*60000),
				isoEnd = end.toISOString();
			console.log(isoEnd, "\n", isoStart, "\n", end.toLocaleTimeString() + " to " + start.toLocaleTimeString());
			rEdits = [];
			$.when(
				$.ajax(recentEditUrl(langList[0], isoStart, isoEnd)),
				$.ajax(recentEditUrl(langList[1], isoStart, isoEnd)),
				$.ajax(recentEditUrl(langList[2], isoStart, isoEnd)),
				$.ajax(recentEditUrl(langList[3], isoStart, isoEnd)),
				$.ajax(recentEditUrl(langList[4], isoStart, isoEnd)),
				$.ajax(recentEditUrl(langList[5], isoStart, isoEnd)),
				$.ajax(recentEditUrl(langList[6], isoStart, isoEnd))
			).always(function(res0, res1, res2, res3, res4, res5, res6) { // eslint-disable-line max-params
				if (res0[1] === "success") { res0[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[0];
					rEdits.push(rc); }); }
				if (res1[1] === "success") { res1[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[1];
					rEdits.push(rc); }); }
				if (res2[1] === "success") { res2[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[2];
					rEdits.push(rc); }); }
				if (res3[1] === "success") { res3[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[3];
					rEdits.push(rc); }); }
				if (res4[1] === "success") { res4[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[4];
					rEdits.push(rc); }); }
				if (res5[1] === "success") { res5[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[5];
					rEdits.push(rc); }); }
				if (res6[1] === "success") { res6[0].query.recentchanges.forEach(function(rc) {
					rc.wiki = langListLong[6];
					rEdits.push(rc); }); }
				d3.shuffle(rEdits);
				scrollAnimation();
			});
		} else {
			scrollAnimation();
		}
	}

	function setupChart(cb) {
		// collage will not allow additional content (e.g. eyebrow link, best not to set parent page)
		$('.header-main').hide();
		svg = d3.select(containerID)
			.append("svg");
		g = svg.append("g").attr("transform-origin", "0 0 0");
		y = d3.scaleLinear()
			.domain([0, d3.max(randomData, function(d) {return d.y;})]);
		x = d3.scaleLinear()
			.domain([0, 1]);
		blobs = g.append("g").attr("class", "blobs-g");
		blobs
			.selectAll("circle")
			.data(randomData)
			.enter()
			.append("circle")
			.attr("class", function(d) {return d.x + " " + d.y;})
			.style("fill", colorBlack)
			.attr("r", blobR);
		var svgDefs = svg.append("defs"),
			mainGradient = svgDefs.append("linearGradient")
				.attr("id", "mainGradient")
				.attr("gradientTransform", "rotate(90)");
		mainGradient.append("stop")
			.attr("class", "stop-0")
			.attr("offset", "0");
		mainGradient.append("stop")
			.attr("class", "stop-1")
			.attr("offset", "1");
		fadedEdge = svg.append("rect")
			.classed("filled", true)
			.attr("x", 0)
			.attr("y", 0)
			.attr("width", initWidth)
			.attr("height", bigBlobR*2);
		clickCue = g.append("g").attr("class", "click-cue");
		clickCue
			.selectAll("line")
			.data([stories[cueStoryI]])
			.enter()
			.append("line");
		clickCue
			.selectAll("text")
			.data([stories[cueStoryI]])
			.enter()
			.append("text")
			.text(shortAtts["click"])
			.attr("text-anchor", "start");
		clickCue
			.style("opacity", 0)
			.style("visibility", "hidden");
		pulse = g.append("g").attr("class", "pulse-g").style("opacity", 0);
		pulse
			.append("circle")
			.attr("class", "pulse")
			.attr("cx", "0%")
			.attr("cy", "0%")
			.attr("r", bigBlobR)
			.style("opacity", 0);
		storyBlobs = g.append("g").attr("class", "story-blobs");
		storyBlobs
			.selectAll("circle")
			.data(stories.slice(0,storiesLen))
			.enter()
			.append("circle")
			.attr("title", function(_, i) {return "Story " + (i + 1);})
			.attr("data-color", function(_, i) {return storyColors[i] ? "rgba" + storyColors[i] : colorBlack;})
			.style("fill", colorBlack)
			.style("stroke-width", blobStroke)
			.style("stroke", "rgba(255, 255, 255, 0)")
			.attr("r", blobR);
		storyContents.each(function(i){
			$(this).find(".story-image").css("border", "4px solid rgba" + storyColors[i]);
		});

		ornaments = g.append("g").attr("class", "ornaments-g");
		ornaments
			.selectAll("path")
			.data(ornamentArr)
			.enter()
			.append("path")
			.attr("class", "ornament")
			.attr("d", function(d) {return d.path;})
			.style("stroke-width", 3)
			.style("stroke", colorBlack)
			.style("fill", "none")
			.style("stroke-linecap", "round")
			.style("stroke-linejoin", "round")

		container.find(".next-story").click(function() {
			if (currentStory < storiesLen - 1) {
				currentStory++;
			} else {
				currentStory = 0;
			}
			getStoryContent();
		});

		container.find(".prev-story").click(function() {
			if (currentStory > 0) {
				currentStory--;
			} else {
				currentStory = storiesLen - 1;
			}
			getStoryContent();
		});

		zoom = d3.zoom()
			.scaleExtent([1, 2])
			.duration(0)
			.on("zoom", zoomed);

		cb();
	}

	function drawChart() {
		var currentHeight = window.innerHeight - header.height(),
			currentWidth = html.width(),
			scale = window.innerWidth > mobileWidth ? 0.6 : 0.4;
		svg
			.attr("height", currentHeight)
			.attr("width", currentWidth)
			.attr("viewBox", "0 0 " + currentWidth + " " + currentHeight)
		y.range([bigBlobR, currentHeight - bigBlobR]);
		x.range([bigBlobR, currentWidth - bigBlobR]);
		blobs
			.selectAll("circle")
			.style("opacity", 0)
			.attr("cx", function(d) {return x(d.x);} )
			.attr("cy", function(d) {return y(d.y);} )
			.transition()
			.delay(function(d,i){ return i * 10 })
			.style("opacity", function() {return getRandom(0.1, 0.9);})
		pulse
			.attr("transform", "translate(" + x(stories[cueStoryI].x) + ", " + y(stories[cueStoryI].y) + ")")
			.style("opacity", 1)
		storyBlobs
			.selectAll("circle")
			.attr("class", "story-blob story-unread")
			.style("opacity", 0)
			.style("visibility", "hidden")
			.attr("cx", function(d) {return x(d.x);} )
			.attr("cy", function(d) {return y(d.y);} )
			.on("mouseover", function(){
				var fill = d3.select(this).attr("data-color");
				d3.select(this)
					.transition()
					.style("fill", fill)
					.attr("r", bigBlobR * 2);
				pulse.transition().style("opacity", 0).style("visibility", "hidden");
			})
			.on("mouseleave", function(){
				var fill = d3.select(this).attr("class").indexOf("story-read") > -1 ? d3.select(this).attr("data-color") : colorBlack;
				d3.select(this)
					.transition()
					.style("fill", fill)
					.attr("r", bigBlobR);
				pulse.transition().style("opacity", 1).style("visibility", "visible")
			})
			.on("click", storyClick);
		clickCue
			.selectAll("line")
			.attr("x1", function(d) {return x(d.x) + blobR;} )
			.attr("x2", function(d) {return x(d.x) + blobR*3;} )
			.attr("y1", function(d) {return y(d.y) - blobR*2;} )
			.attr("y2", function(d) {return y(d.y) - blobR*5;} );
		clickCue
			.selectAll("text")
			.attr("x", function(d) {return x(d.x) + blobR*3;} )
			.attr("y", function(d) {return y(d.y) - blobR*5;} )
			.attr("dy", "-5px");
		storyOverlay.find(".close").click(closeStoryModal);
		body.keyup(function(e) {
			if (e.key === "Escape") {
				closeStoryModal();
			}
		});
		fadedEdge
			.attr("width", currentWidth);
		ornaments
			.selectAll("path")
			.attr("transform", function(d, i) {return "translate(" + x(ornamentArr[i].x) + ", " + y(ornamentArr[i].y - 0.001) + ") rotate(" + ornamentArr[i].r + ") scale(" + scale + ")";})
			.attr("stroke-dasharray", "500 500")
			.attr("stroke-dashoffset", 500)
			.transition()
			.duration(1400)
			.attr("transform", function(d, i) {return "translate(" + x(ornamentArr[i].x) + ", " + y(ornamentArr[i].y) + ") rotate(" + ornamentArr[i].r + ") scale(" + scale + ")";})
			.delay(function(d,i){ return randomDataLen * 10 + i * 200 })
			.attr("stroke-dashoffset", 0)
		// reset zoom
		g.call(
			zoom.transform,
			d3.zoomIdentity,
			d3.zoomTransform(g.node()).invert([currentWidth / 2, currentHeight / 2])
		);

		body.css("overflow", "hidden auto")
	}

	function closeStoryModal() {
		body.css("overflow", "hidden auto");
		hide(storyOverlay);
		container.focus();
		showStories();
	}

	function setupFakescroll() {
		var fakeHeight = scrollAnimLength * 2;
		fakeScroll.css("height", fakeHeight / window.innerHeight * 100 + "vh");
		notFixedContent.css({
			"position": "relative",
			"background-color": "#ffffff",
			"padding-top": "2.5rem",
			"padding-bottom": "0.5rem"
		});
	}

	function getStoryContent() {
		// show and hide via display none instead of visibility
		storyContents.hide();
		$(storyContents[currentStory]).show();
		storyRead(currentStory);
		container.find(".story-nav .index").text(currentStory + 1 + "/" + storiesLen);
		storyOverlay.find(".story-content-container").scrollTop(0);
	}

	function storyClick(d, i) {
		currentStory = i;
		getStoryContent();
		body.css("overflow", "hidden");
		show(storyOverlay);
		storyRead(i);
		clickCue.transition().style("opacity", 0).style("visibility", "hidden");
		pulse.transition().style("opacity", 0).style("visibility", "hidden");
	}

	function storyRead(i) {
		var thisBlob = storyBlobs
				.selectAll("circle")
				.filter(function(d, j) {return j === i;}),
			fill = thisBlob.attr("data-color");
		thisBlob
			.style("fill", fill)
			.attr("class", "story-blob story-read");
		if (i === cueStoryI) {
			showCue = false;
		}
	}

	function startEditAnim() {
		if (rEdits.length > 0) {
			rEditLabel.text(shortAtts.label + " " + rEdits[rEditAnimationI].wiki);
			rEditTitle.text(rEdits[rEditAnimationI].title);
			show(rEditTicker);
			blobs
				.selectAll("circle")
				.sort(distance)
				.filter(function (_, i) {return i === rEditAnimationI;})
				.transition()
				.duration(500)
				.style("fill", "rgba" + storyColors[rEditAnimationI % storyColors.length])
				.style("opacity", 1)
				.attr("r", bigBlobR)
				.transition()
				.delay(1500)
				.style("fill", colorBlack)
				.style("opacity", getRandom(0.1, 0.9))
				.attr("r", blobR)
				.on("end", function() {
					rEditAnimationI++;
					if (rEditAnimationI < rEdits.length) {
						startEditAnim();
					} else {
						rEditAnimationI = 0;
						getRecentEdits();
					}
				});
		}
	}

	function stopEditAnim() {
		hide(rEditTicker);
		blobs
			.selectAll("circle")
			.filter(function (_, i) { return i === rEditAnimationI;})
			.interrupt()
			.style("fill", colorBlack)
			.style("opacity", 0.5)
			.attr("r", blobR);
	}

	function calcOpacity(p, min, max) {
		if (p < min + 0.1) {
			return linearUp(p);
		} else if (p > max - 0.1) {
			return linearDown(p);
		} else {
			return 1;
		}
	}

	function unreadStory() {
		var cx, cy;
		d3.select(".story-unread").each(function() {
			cx = d3.select(this).attr("cx");
			cy = d3.select(this).attr("cy");
		});
		return [cx, cy];
	}

	function hideStories() {
		storyBlobs
			.selectAll("circle")
			.style("opacity", 0)
			.style("visibility", "hidden")
			.attr("r", blobR);
		clickCue
			.style("opacity", 0)
			.style("visibility", "hidden");
		pulse
			.style("opacity", 0)
			.style("visibility", "hidden");
		blobs
			.style("opacity", 1);
	}

	function showStories(progress, min, max) {
		var opacity = calcOpacity(progress, min, max);
		storyBlobs
			.selectAll("circle")
			.style("opacity", opacity)
			.style("visibility", "visible")
			.attr("r", bigBlobR);
		if (showCue) {
			clickCue
				.style("opacity", opacity)
				.style("filter", "blur(" + (1-opacity) * 2 + "px)")
				.style("visibility", "visible");
			pulse
				.style("opacity", opacity)
				.style("visibility", "visible");
		} else {
			pulse
				.style("opacity", opacity)
				.style("visibility", "visible")
				.attr("transform", "translate(" + unreadStory()[0] + ", " + unreadStory()[1] + ")" );
		}
		blobs
			.style("opacity", Math.max(0.1, 1 - opacity));
	}

	function hide(elem) {
		elem.removeClass("visible").addClass("hidden");
	}

	function show(elem) {
		elem.removeClass("hidden").addClass("visible");
	}

	function scrollAnimation() {
		container.show();
		var scrollTop = $(window).scrollTop(),
			zoomFactor = Math.min(1 + scrollTop/scrollAnimLength/2, zoomFactorMax),
			inViewPos = notFixedContent.offset().top - window.innerHeight,
			notFixedContentScrolled = ((scrollTop - inViewPos) / window.innerHeight).toFixed(2),
			totalScroll = notFixedContent.offset().top - window.innerHeight,
			progress = scrollTop/totalScroll;

		if (progress < scene1) {
			g.call(zoom.scaleTo, zoomFactor);
			show(intro1);
			hide(intro2);
			hide(heading);
			hideStories();
			ornaments.style("visibility", "visible")
			stopEditAnim();
		} else if (progress < scene1_1) {
			g.call(zoom.scaleTo, zoomFactor);
			hide(intro1);
			show(intro2);
			hide(heading);
			hideStories();
			ornaments.style("visibility", "visible")
			stopEditAnim();
		} else if (progress < scene2) {
			g.call(zoom.scaleTo, zoomFactor);
			hide(intro1);
			hide(intro2);
			show(heading);
			hideStories();
			ornaments.style("visibility", "hidden")
			startEditAnim();
		} else if (progress < scene3) {
			g.call(zoom.scaleTo, zoomFactor);
			hide(intro1);
			hide(intro2);
			show(heading);
			showStories(progress, scene2, scene3);
			ornaments.style("visibility", "hidden")
			stopEditAnim();
		} else if (progress >= scene3) {
			g.call(zoom.scaleTo, zoomFactor - Math.max(0, progress - scene3));
			hide(intro1);
			hide(intro2);
			show(heading);
			hideStories();
			ornaments.style("visibility", "hidden")
			stopEditAnim();
			container.css("opacity", Math.max(0, 1 - notFixedContentScrolled) );
		}
	}

	function zoomed() {
		var transform = d3.event.transform;
		g.attr("transform", "translate(" + transform.x + "," + transform.y + ") scale(" + transform.k + ")");
	}

	$( window ).scroll( function() {
		if ($(window).scrollTop() >= notFixedContent.offset().top) {
			container.hide();
		} else {
			requestAnimationFrame(scrollAnimation);
		}
	});

	$( window ).resize( function() {
		if (initWidth !== html.width() || initHeight !== window.innerHeight) {
			initWidth = html.width();
			initHeight = window.innerHeight;
			requestAnimationFrame( function() {
				drawChart();
				startEditAnim();
				scrollAnimation();
			});
		}
	});

	setupFakescroll();
	setupChart(scrollAnimation);
	drawChart();
	getRecentEdits(true);
});

