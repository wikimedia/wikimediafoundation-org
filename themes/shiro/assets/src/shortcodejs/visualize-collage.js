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
		heading = container.find("h1"),
		intro = container.find(".intro"),
		rEditTicker = container.find(".recent-edits"),
		rEditLabel = rEditTicker.find(".label"),
		rEditTitle = rEditTicker.find(".title"),
		storyOverlay = container.find(".story-overlay"),
		header = $("header").height(),
		initWidth = window.innerWidth,
		colorBlack = "#202122",
		colorAccent = "#36c",
		scrollAnimLength = 1200,
		stories = [{"x":0.56,"y":0.62, "name": "Name 1"},{"x":0.68,"y":0.52, "name": "Name 2"},{"x":0.51,"y":0.68, "name": "Name 3"},{"x":0.70,"y":0.38, "name": "Name 4"},{"x":0.29,"y":0.57, "name": "Name 5"},{"x":0.41,"y":0.61, "name": "Name 6"},{"x":0.48,"y":0.27, "name": "Name 7"},{"x":0.71,"y":0.60, "name": "Name 8"},{"x":0.65,"y":0.29, "name": "Name 9"},{"x":0.35,"y":0.32, "name": "Name 10"},{"x":0.27,"y":0.38, "name": "Name 11"},{"x":0.31,"y":0.69, "name": "Name 12"},{"x":0.57,"y":0.24, "name": "Name 13"},{"x":0.43,"y":0.20, "name": "Name 14"},{"x":0.30,"y":0.25, "name": "Name 15"}],
		randomData = [],
		apilimit = 5,
		randomDataLen = Math.max(langList.length * apilimit, 80),
		storiesLen = 15,
		blobR = 5,
		bigBlobR = blobR * 2,
		blobStroke = bigBlobR * 3,
		zoomMax = 1.7,
		svg, g, y, blobs, x, zoom, storyBlobs, clickCue, fadedEdge,
		rEditAnimationI = 0,
		rEdits = [];

	console.log("collage", shortAtts);

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
			.style("opacity", 0);
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
		zoom = d3.zoom()
			.scaleExtent([1, zoomMax])
			.duration(0)
			.on("zoom", zoomed);

		cb();
	}

	function drawChart() {
		var currentHeight = window.innerHeight - header,
			currentWidth = window.innerWidth;
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
			storyOverlay.fadeOut();
		});
		fadedEdge
			.attr("width", currentWidth);
		// reset zoom
		g.call(
			zoom.transform,
			d3.zoomIdentity,
			d3.zoomTransform(g.node()).invert([currentWidth / 2, currentHeight / 2])
		);
	}

	function fakescroll() {
		var fakeHeight = scrollAnimLength * 2;
		fakeScroll.css("height", fakeHeight / window.innerHeight * 100 + "vh");
		notFixedContent.css({
			"position": "relative",
			"background-color": "#ffffff",
			"padding-top": "2.5rem",
			"padding-bottom": "0.5rem"
		})
	}

	function storyClick() {
		storyOverlay.find("h2").text(d3.select(this).data()[0].name);
		storyOverlay.find(".image").css({"top": getRandom(15,25) + "%"})
		storyOverlay.fadeIn();
		clickCue.transition().style("opacity", 0);
	}

	function startEditAnim() {
		if (rEdits.length > 0) {
			console.log(rEditAnimationI, rEdits[rEditAnimationI].title);
			rEditLabel.text(shortAtts.label + " " + rEdits[rEditAnimationI].wiki);
			rEditTitle.text(rEdits[rEditAnimationI].title);
			rEditTicker.fadeIn();
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
		rEditTicker.hide();
		blobs
			.selectAll("circle")
			.filter(function (_, i) { return i === rEditAnimationI;})
			.interrupt()
			.style("fill", colorBlack)
			.style("opacity", 0.5)
			.attr("r", blobR);
	}

	function hideStories() {
		storyBlobs
			.selectAll("circle")
			.transition()
			.style("opacity", 0)
			.attr("r", blobR)
		clickCue
			.transition()
			.style("opacity", 0)
		blobs
			.transition()
			.style("opacity", 1)
	}

	function showStories() {
		storyBlobs
			.selectAll("circle")
			.transition()
			.delay(function(d,i){ return i * 5 })
			.style("opacity", 1)
			.attr("r", bigBlobR)
		clickCue
			.transition()
			.delay(stories.length * 25)
			.style("opacity", 1)
		blobs
			.transition()
			.style("opacity", 0.1)
	}

	function scrollAnimation() {
		container.show();
		var scrollTop = $(window).scrollTop(),
			zoomFactor = 1 + scrollTop/scrollAnimLength/2,
			inViewPos = notFixedContent.offset().top - window.innerHeight,
			notFixedContentScrolled = ((scrollTop - inViewPos) / window.innerHeight).toFixed(2),
			progress = scrollTop/(notFixedContent.offset().top - window.innerHeight);
		g.call(zoom.scaleTo, zoomFactor);

		if (progress < 0.2) {
			// console.log("animation 1");
			intro.fadeIn();
			heading.hide();
			hideStories();
			stopEditAnim();
			storyOverlay.fadeOut();
		} else if (progress < 0.4) {
			// console.log("animation 2");
			intro.hide();
			heading.fadeIn();
			hideStories();
			startEditAnim();
			storyOverlay.fadeOut();
		} else if (progress < 0.8) {
			// console.log("animation 3");
			intro.hide();
			heading.fadeIn();
			showStories(); // FIX map opacity to scroll progress
			stopEditAnim();
		} else if (progress >= 0.8) {
			// console.log("animation 4");
			intro.hide();
			heading.fadeIn();
			hideStories();
			stopEditAnim();
			storyOverlay.fadeOut();
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
		if (initWidth !== window.innerWidth) {
			requestAnimationFrame( function() {
				drawChart();
				startEditAnim();
				scrollAnimation();
			});
		}
	})

	fakescroll();
	setupChart(scrollAnimation);
	drawChart();
	getRecentEdits(true);
});

