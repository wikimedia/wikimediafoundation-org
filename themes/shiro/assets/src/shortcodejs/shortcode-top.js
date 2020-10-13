/* global d3 */
/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var atts = topAtts, // eslint-disable-line no-undef
		select = $("#year-select"),
		contents = $(".top-data-content"),
		data = [];

	function drawChart(daily, id) {
		var width = 300,
			w = width/daily.length,
			height = 40,
			h = 6,
			y, x, color, line, svg;
		y = d3.scaleLinear()
			.domain([0, d3.max(daily, function(d) {return d;})]).nice()
			.range([height, h]);
		x = d3.scaleLinear()
			.domain([0, daily.length])
			.range([0, width]);
		color = d3.scaleLinear()
			.domain([0, d3.max(daily, function(d) {return d;})]).nice()
			.range(["#f8f9fa", "#202122"]);

		line = d3.line()
			.defined(function(d) { return !isNaN(d);})
			.curve(d3.curveCardinal.tension(0.5))
			.x(function(d, i) { return x(i);})
			.y(function(d) { return y(d);});

		svg = d3.select(id)
			.append("svg")
			.attr("viewBox", [0, 0, width, height + h]);

		svg.append("g")
			.append("path")
			.datum(daily)
			.attr("fill", "none")
			.attr("stroke", "#72777D")
			.attr("stroke-width", 1)
			.attr("stroke-linejoin", "round")
			.attr("stroke-linecap", "round")
			.attr("d", line);

		svg
			.append("g")
			.selectAll("rect")
			.data(daily)
			.join("rect")
			.attr("width", w)
			.attr("height", h)
			.style("fill", function (d) { return color(d);})
			.attr("x", function(d, i) { return i * w;})
			.attr("y", height);
	}

	function populateData(year) {
		var filterD = data.filter(function(d) { return parseInt(d.year, 10) === year; }),
			langs = filterD.map(function(d) { return d.wiki_db; });
		console.log(filterD);
		contents.each(function() {
			var langContainer = $(this),
				id = $(this).attr('id'),
				content = filterD.find(function(d) {return d.wiki_db === id; });
			langContainer.hide();
			if ( langs.indexOf(id) > -1 ) {
				var desc = content["desc_" + atts.lang.replaceAll("wiki", "")].replaceAll('"', ""),
					heading = content.pagetitle.replaceAll("_", " "),
					imgurl = content.image,
					total = d3.format(",")(content.edits),
					daily = content.daily_edits.split("_").map(function(d) {return parseInt(d, 10);}),
					graphid = "#" + id + "-graph";
				$(graphid).empty();
				drawChart(daily, graphid);
				langContainer.find(".heading").text(heading);
				langContainer.find(".desc")
					.text(desc + " ")
					.append($("<a></a>")
						.attr("href", "/")
						.text(fetchWikiname(content.wiki_db))
					);
				langContainer.find(".article-image").css("background-image", "url(" + imgurl + ")");
				langContainer.find(".data p").text(total + " edits");
				langContainer.fadeIn();
			}
		});
	}

	function fetchWikiname(shortname) {
		var str = "";
		switch (shortname) {
		case "enwiki":
			str = "English Wikipedia";
			break;
		case "arwiki":
			str = "Arabic Wikipedia";
			break;
		case "dewiki":
			str = "German Wikipedia";
			break;
		case "eswiki":
			str = "Spanish Wikipedia";
			break;
		case "frwiki":
			str = "French Wikipedia";
			break;
		case "ruwiki":
			str = "Russian Wikipedia";
			break;
		case "zhwiki":
			str = "Chinese Wikipedia";
			break;
		default:
			str = "Wikipedia";
		}
		return str;
	}

	function getSelectedYear() {
		var year = "";
	    $("#year-select option:selected").each(function() {
			year += $( this ).text() + " ";
	    });
	    populateData(parseInt(year, 10));
	}

	function setup() {
		contents.each(function(){
			$(this).hide();
		});
	}

	setup();

	d3.csv(atts.url, function(d) {
		return d;
	}).then(function(res) {
		data = res;
		getSelectedYear();
	});

	select.change(getSelectedYear);
});

