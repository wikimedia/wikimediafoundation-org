/* global d3 */
/* eslint-disable no-magic-numbers */

jQuery(document).ready(function($) {

	'use strict';

	function lineChart(id) {
		var $id = $("#" + id),
			mobileThresh = 762,
			isMobile = $(window).width() < mobileThresh,
			margin = { top: 30, right: isMobile ? 20 : 60, bottom: 60, left: 40 },
			origWidth = $id.width(),
			width = origWidth - margin.left - margin.right,
			origHeight = 300,
			height = origHeight - margin.top - margin.bottom,
			strokeWidth = 1.5,
			circleDiameter = 4,
			accentColorClass = $id.data("stroke-color"),
			timeFormatPrefs = $id.data("time-format") === "month" ? { month: "long" } : { month: "short", day: "numeric", year: "numeric" },
			parseTimeFormat = d3.timeParse("%Y-%m-%d"),
			dataRaw = $id.data("chart-raw"),
			data = dataRaw ? dataRaw.map(parseDates) : null;

		function parseDates(wp) {
			wp.date = parseTimeFormat(wp.date);
			return wp;
		}

		function callout(g, value, xPos, evnt) {
			if (!value) return g.style("display", "none");
			g
				.attr("class", "tooltip")
				.style("display", null)
				.style("pointer-events", "none");

			var borderRadius = 2,
				lineHeight = 1.2,
				rect = g.selectAll("rect")
					.data([null])
					.join("rect")
					.attr("rx", borderRadius)
					.attr("ry", borderRadius),
				text = g.selectAll("text")
					.data([null])
					.join("text")
					.call(function(t) {
						return t
							.selectAll("tspan")
							.data(value.split(/\n/))
							.join("tspan")
							.attr("x", 0)
							.attr("y", function(d, i) { return i * lineHeight + "em" })
							.style("font-weight", function(_, i) { return i ? null : "bold" })
							.text(function(d) { return d; })
					}),
				bbox = text.node().getBBox(),
				w = bbox.width,
				h = bbox.height,
				ttipMar = 10,
				totalMar = ttipMar * 2,
				shift = 2;

			g.selectAll("circle")
				.data([null])
				.join("circle")
				.attr("r", circleDiameter)
				.attr("stroke-width", strokeWidth)
				.attr("fill", evnt ? "none" : "white")

			rect.attr("width", w + totalMar).attr("height", h + totalMar)

			if (xPos + w > width) {
				text.attr("transform", "translate(" + (-w - totalMar) + "," + -((h - totalMar) / 2 - shift) + ")");
				rect.attr("x", -(w + ttipMar + totalMar)).attr("y", -(h + totalMar) / 2);
			} else {
				text.attr("transform", "translate(" + totalMar + "," + -((h - totalMar) / 2 - shift) + ")");
				rect.attr("x", ttipMar).attr("y", -(h + totalMar) / 2);
			}

			return g;
		}

		function addVis() {
			if (!data) {
				return;
			}
			var svg,
				ticks = { x: 0, y: 4 },
				x = d3.scaleTime()
					.nice()
					.domain(d3.extent(data, function(d) { return d.date; }))
					.range([0, width]),
				xAxis = function(g) {
					g
						.attr("transform", "translate(0," + height + ")")
						.call(d3.axisBottom(x)
							.ticks(d3.timeMonth, 1)
							.tickFormat(isMobile ? d3.timeFormat("%b") : d3.timeFormat("%B")))
						.call(function(h) { h.select(".domain").remove(); })
				},
				y = d3.scaleLinear()
					.domain([0, d3.max(data, function(d) { return d.value; })])
					.range([height, 0]),
				yAxis = function(g) {
					g
						.attr("class", "yAxis")
						.call(d3.axisLeft(y)
							.ticks(ticks.y)
							.tickFormat(d3.format(".2s"))
							.tickSize(-1 * width))
						.call(function(h) { h.select(".domain").remove(); })
				},
				bisectDate = function(mx) {
					var bisect = d3.bisector(function(d) { return d.date }).left,
						date = x.invert(mx),
						index = bisect(data, date, 1),
						a = data[index - 1],
						b = data[index];
					return date - a.date > b.date - date ? b : a;
				},
				tooltip,
				container = d3.select("#" + id),
				aspect = origWidth / origHeight;

			svg = container.append("svg")
				.attr("width", origWidth)
				.attr("height", origHeight)
				.attr("perserveAspectRatio", "xMidYMid meet")
				.attr("viewBox", "0 0 " + origWidth + " " + origHeight)
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			// lazy resize, keeps proportions
			$( window ).resize(function() {
				var targetWidth = container.node().getBoundingClientRect().width;
				container.selectAll("svg").attr("width", targetWidth);
				container.selectAll("svg").attr("height", targetWidth / aspect);
			});

			svg.append("g").call(xAxis);
			svg.append("g").call(yAxis);

			svg.append("path")
				.datum(data)
				.attr("fill", "none")
				.attr("stroke-width", strokeWidth)
				.attr("stroke", "black")
				.attr("class", "line " + accentColorClass)
				.attr("d", d3.line()
					.x(function(d) { return x(d.date) })
					.y(function(d) { return y(d.value) })
				);

			svg.append("g")
				.selectAll("circle")
				.data(data.filter(function(d) { return d.event; }))
				.enter("circle")
				.append("circle")
				.attr("class", "circle " + accentColorClass)
				.attr("r", circleDiameter)
				.attr("cx", function(d) { return x(d.date); })
				.attr("cy", function(d) { return y(d.value); });

			tooltip = svg.append("g");

			svg.append('rect')
				.attr("class", "overlay")
				.attr("pointer-events", "all")
				.attr('x', 0)
				.attr('y', 0)
				.attr('width', width)
				.attr('height', height)
				.on("touchmove mousemove", function() {
					var hoverD = bisectDate(d3.mouse(this)[0]),
						context = hoverD.date.toLocaleString(undefined, timeFormatPrefs) + "\n" + d3.format(",")(hoverD.value);
					tooltip
						.attr("transform", "translate(" + x(hoverD.date) + "," + y(hoverD.value) + ")")
						.call(callout, context, x(hoverD.date), hoverD.event);
					if (hoverD.event) {
						$("#label-date").text(hoverD.date.toLocaleString(undefined, timeFormatPrefs));
						$("#label-name").text(hoverD.event);
						$("#linechart-label").css("opacity", 1);
						$("#linechart-label").css("max-height", 10 + "rem");
					} else {
						$("#linechart-label").css("opacity", 0);
						$("#linechart-label").css("max-height", 2 + "rem");
					}
				})
				.on("touchend mouseleave", function() {
					tooltip.call(callout, null);
				});
		}

		addVis();

	}

	$(".d3-linechart").each(function() {
		lineChart(this.id);
	});


});