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
		colorBlack = "#202122",
		colorAccent = "#36c",
		scrollAnimLength = 1200,
		stories = [{"x":0.56,"y":0.62},{"x":0.7,"y":0.52},{"x":0.51,"y":0.68},{"x":0.73,"y":0.38},{"x":0.29,"y":0.57},{"x":0.41,"y":0.61},{"x":0.48,"y":0.31},{"x":0.71,"y":0.60},{"x":0.65,"y":0.29},{"x":0.35,"y":0.32},{"x":0.24,"y":0.41},{"x":0.31,"y":0.69},{"x":0.57,"y":0.24},{"x":0.43,"y":0.23},{"x":0.28,"y":0.27}],
		storyContents = storyOverlay.find(".story-content"),
		randomData = [],
		apilimit = 5,
		randomDataLen = Math.max(langList.length * apilimit, 80),
		storiesLen = storyContents.length,
		blobR = 5,
		bigBlobR = blobR * 2,
		blobStroke = bigBlobR * 3,
		scene1 = 0.12,
		scene1_1 = 0.24,
		scene2 = 0.5,
		scene3 = 0.9,
		sceneTran = 0.1,
		linearUp = d3.scaleLinear().domain([scene2, scene2 + sceneTran]).range([0,1]),
		linearDown = d3.scaleLinear().domain([scene3 - sceneTran, scene3]).range([1,0]),
		zoomMax = 1.7,
		svg, g, y, blobs, x, zoom, storyBlobs, clickCue, fadedEdge,
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
		blobs = g.append("g");
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
			.data([stories[8]])
			.enter()
			.append("line");
		clickCue
			.selectAll("text")
			.data([stories[8]])
			.enter()
			.append("text")
			.text(shortAtts["click"])
			.attr("text-anchor", "start");
		clickCue
			.style("opacity", 0)
			.style("visibility", "hidden");
		storyBlobs = g.append("g").attr("transform-origin", "0 0 0");
		storyBlobs
			.selectAll("circle")
			.data(stories.slice(0,storiesLen))
			.enter()
			.append("circle")
			.attr("title", function(_, i) {return "Story " + (i + 1);})
			.style("fill", colorBlack)
			.style("stroke-width", blobStroke)
			.style("stroke", "rgba(255, 255, 255, 0)")
			.attr("r", blobR);

		container.find(".next-story").click(function() {
			if (currentStory < storiesLen - 1) {
				currentStory++;
				getStoryContent();
			}
		});

		container.find(".prev-story").click(function() {
			if (currentStory > 0) {
				currentStory--;
				getStoryContent();
			}
		});

		zoom = d3.zoom()
			.scaleExtent([1, zoomMax])
			.duration(0)
			.on("zoom", zoomed);

		cb();
	}

	function drawChart() {
		var currentHeight = window.innerHeight - header.height(),
			currentWidth = html.width();
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
		storyBlobs
			.selectAll("circle")
			.attr("class", "story-blob")
			.style("opacity", 0)
			.style("visibility", "hidden")
			.attr("cx", function(d) {return x(d.x);} )
			.attr("cy", function(d) {return y(d.y);} )
			.on("mouseover", function(){
				d3.select(this)
					.transition()
					.style("fill", colorAccent)
					.attr("r", bigBlobR * 2)
			})
			.on("mouseleave", function(){
				d3.select(this)
					.transition()
					.style("fill", colorBlack)
					.attr("r", bigBlobR)
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
		storyOverlay.find(".close").click(function(){
			body.css("overflow", "hidden auto");
			hide(storyOverlay);
		});
		fadedEdge
			.attr("width", currentWidth);
		// reset zoom
		g.call(
			zoom.transform,
			d3.zoomIdentity,
			d3.zoomTransform(g.node()).invert([currentWidth / 2, currentHeight / 2])
		);

		body.css("overflow", "hidden auto")
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
		if (currentStory === 0) {
			show(container.find(".next-story"));
			hide(container.find(".prev-story"));
		} else if (currentStory === storiesLen - 1) {
			hide(container.find(".next-story"));
			show(container.find(".prev-story"));
		} else {
			show(container.find(".next-story"));
			show(container.find(".prev-story"));
		}

	}

	function storyClick(d, i) {
		currentStory = i;
		getStoryContent();
		body.css("overflow", "hidden");
		show(storyOverlay);
		clickCue.transition().style("opacity", 0).style("visibility", "hidden");
	}

	function startEditAnim() {
		if (rEdits.length > 0) {
			// console.log(rEditAnimationI, rEdits[rEditAnimationI].title);
			rEditLabel.text(shortAtts.label + " " + rEdits[rEditAnimationI].wiki);
			rEditTitle.text(rEdits[rEditAnimationI].title);
			show(rEditTicker);
			blobs
				.selectAll("circle")
				.filter(function (_, i) { return i === rEditAnimationI;})
				.transition()
				.duration(500)
				.style("fill", colorAccent)
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

	function hideStories() {
		storyBlobs
			.selectAll("circle")
			.style("opacity", 0)
			.style("visibility", "hidden")
			.attr("r", blobR)
		clickCue
			.style("opacity", 0)
			.style("visibility", "hidden")
		blobs
			.style("opacity", 1)
	}

	function showStories(progress, min, max) {
		var opacity = calcOpacity(progress, min, max);
		storyBlobs
			.selectAll("circle")
			.style("opacity", opacity)
			.style("visibility", "visible")
			.attr("r", bigBlobR);
		clickCue
			.style("opacity", opacity)
			.style("visibility", "visible");
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
			zoomFactor = 1 + scrollTop/scrollAnimLength/2,
			inViewPos = notFixedContent.offset().top - window.innerHeight,
			notFixedContentScrolled = ((scrollTop - inViewPos) / window.innerHeight).toFixed(2),
			progress = scrollTop/(notFixedContent.offset().top - window.innerHeight);
		g.call(zoom.scaleTo, zoomFactor);

		if (progress < scene1) {
			show(intro1);
			hide(intro2);
			hide(heading);
			hideStories();
			stopEditAnim();
			hide(storyOverlay);
		} else if (progress < scene1_1) {
			hide(intro1);
			show(intro2);
			hide(heading);
			hideStories();
			stopEditAnim();
			hide(storyOverlay);
		} else if (progress < scene2) {
			hide(intro1);
			hide(intro2);
			show(heading);
			hideStories();
			startEditAnim();
			hide(storyOverlay);
		} else if (progress < scene3) {
			hide(intro1);
			hide(intro2);
			show(heading);
			showStories(progress, scene2, scene3);
			stopEditAnim();
		} else if (progress >= scene3) {
			hide(intro1);
			hide(intro2);
			show(heading);
			hideStories();
			stopEditAnim();
			hide(storyOverlay);
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
	})

	setupFakescroll();
	setupChart(scrollAnimation);
	drawChart();
	getRecentEdits(true);
});

