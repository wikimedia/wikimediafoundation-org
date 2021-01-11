/* global d3 */
/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var atts = topAtts, // eslint-disable-line no-undef
		container = $("#" + atts['id']),
		selectYear = $("#year-select"),
		nodata = container.find(".no-data"),
		contents = $(".top-data-content"),
		credits = $(".article-photo-credits-container"),
		line, svg,
		data = {"edits": [], "views": []};

	function drawChart(daily, id) {
		var width = 300,
			height = 40,
			h = 6,
			y, x;
		y = d3.scaleLinear()
			.domain([0, d3.max(daily, function(d) {return d;})]).nice()
			.range([height, h]);
		x = d3.scaleLinear()
			.domain([0, daily.length])
			.range([0, width]);

		line = d3.line()
			.defined(function(d) { return !isNaN(d);})
			.curve(d3.curveBasis)
			.x(function(d, i) { return x(i);})
			.y(function(d) { return y(d);});

		svg = d3.select(id)
			.append("svg")
			.attr("viewBox", [0, 0, width, height + h]);

		svg
			.append("g")
			.attr("class", "bg-line-graph")
			.append("line")
			.style("stroke", "#c8ccd1")
			.attr("x1", 0)
			.attr("x2", width)
			.attr("y1", height)
			.attr("y2", height);

		svg.append("g")
			.attr("class", "line-graph")
			.append("path")
			.datum(daily)
			.attr("fill", "none")
			.attr("stroke", "#72777D")
			.attr("stroke-width", 1.5)
			.attr("stroke-linejoin", "round")
			.attr("stroke-linecap", "round")
			.attr("d", line);
	}

	function populateData(filterD, o) {
		var wiki = o === "views" ? "wiki" : "wiki_db",
			langs = filterD.map(function(d) { return d[wiki]; }),
			unit = atts[o + "_label"],
			unit2 = o === "views" ? "pageviews" : "edits",
			daily = o === "views" ? "daily_views" : "daily_edits",
			creditsList = [];
		// console.log(filterD, o);
		credits.find(".article-photo-credits").text("");
		credits.hide();
		contents.each(function() {
			var langContainer = $(this),
				id = $(this).attr('id'),
				content = filterD.find(function(d) {return d[wiki] === id; }),
				creditInfo = "";
			nodata.hide();
			langContainer.hide();
			if ( langs.indexOf(id) > -1 ) {
				var desc = content["desc_" + atts['lang'].replace("wiki", "")].replace(/"/g, ""),
					heading = content.pagetitle.replace(/_/g, " "),
					filename = content.file_name.replace("File:", ""),
					// assume svgs in dataset converted to png, JPG are downloaded as jpg
					imgurl = atts['directory'] + filename.replace(".svg", ".png").replace(".JPG", ".jpg"),
					total = d3.format(",")(content[unit2]),
					dailyData = content[daily].split("_").map(function(d) {return parseInt(d, 10);}),
					graphid = "#" + id + "-graph";
				$(graphid).empty();
				drawChart(dailyData, graphid);
				langContainer.find(".heading").text(heading);
				langContainer.find(".desc").text(desc);
				if (content[wiki].replace("wiki", "").match(/^[a-zA-Z\-]{2,16}$/)) {
					langContainer.find(".details")
						.attr("href", "https://" + content[wiki].replace("wiki", "") +  ".wikipedia.org/wiki/" + content.pagetitle)
						.attr("target", "_blank");
				}
				langContainer.find(".data span").text(total + " " + unit);
				if (filename.length > 0 && imgurl.length > 0) {
					langContainer.find(".article-image")
						.removeClass("article-image-fallback")
						.css("background-image", 'url("' + imgurl + '")');
					langContainer.find(".article-image-link").attr("href", content.image_page);
					if (creditsList.indexOf(filename) === -1) {
						creditsList.push(filename);
						creditInfo += content.artist.length > 0 ? ", " + content.artist : "";
						creditInfo += content.license.length > 0 ? ", " + content.license : "";
						credits.find(".article-photo-credits")
							.append($("<div></div>")
								.append($("<a>")
									.text(filename)
									.attr("href", content.image_page))
								.append($("<span></span>")
									.text(creditInfo))
							);
					}
				} else {
					langContainer.find(".article-image-link > div").unwrap();
					langContainer.find(".article-image")
						.attr("style", "")
						.addClass("article-image-fallback");
				}
				langContainer.fadeIn();
				credits.show();
			} else {
				nodata.show();
			}
		});
	}

	function evalOption() {
		var radio = container.find("input:checked").val();
		if (data[radio].length > 0) {
			getSelectedYear(radio);
		} else {
			getData(radio);
		}
		selectYear.find("option").each(function() {
			var y = parseInt($(this).val(), 10),
				l = data[radio].filter(function(d) {return parseInt(d.year, 10) === y;}).length;
			if (l > 0) {
				$(this).removeAttr("disabled");
			} else {
				$(this).attr("disabled", "true");
			}
		});
	}

	function getData(type) {
		var url = '';
		if (type === "edits") {
			url = atts['url_edits'].indexOf(".csv") > -1 ? atts['url_edits'] : '';
			d3.csv(url, function(d) {
				return d;
			}).then(function(res) {
				data.edits = res;
				evalOption();
			});
		} else {
			url = atts['url_views'].indexOf(".csv") > -1 ? atts['url_views'] : '';
			d3.csv(url, function(d) {
				return d;
			}).then(function(res) {
				data.views = res;
				evalOption();
			});
		}
	}

	function getSelectedYear(o) {
		var year = "",
			filterD = [],
			option = typeof o === "string" ? o : container.find("input:checked").val();
	    $("#year-select option:selected").each(function() {
			year += $( this ).text() + " ";
	    });

	    filterD = data[option].filter(function(d) { return parseInt(d.year, 10) === parseInt(year, 10); });
	    populateData(filterD, option);
	}

	function setup() {
		contents.each(function(){
			$(this).hide();
		});
	}

	setup();
	evalOption();

	selectYear.change(getSelectedYear);
	container.find("input").change(evalOption);
});

