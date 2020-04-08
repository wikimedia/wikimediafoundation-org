/* global d3 */
/*eslint no-magic-numbers: ["error", { "ignore": [0, 1, -1] }]*/

jQuery(document).ready(function($) {

	'use strict';

	function lineChart(id) {
		var $id = $("#" + id),
			margin = { top: 30, right: 60, bottom: 60, left: 60 },
			width = $id.width() - margin.left - margin.right,
			orgiHeight = 300,
			height = orgiHeight - margin.top - margin.bottom,
			accentColorClass = $id.data("stroke-color"),
			bisectDate = d3.bisector(function(d) { return d.date; }).left,
			tickTimeFormat = $id.data("time-format") === "month" ? d3.timeFormat("%B") : d3.timeFormat("%b %d, %Y"),
			parseTimeFormat = d3.timeParse("%Y-%m-%d");

		function addVisual(data) {

			if (!data) {
				return;
			}

			var svg = d3.select("#" + id)
					.append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")"),
				x = d3.scaleTime()
					.domain(d3.extent(data, function(d) { return d.date; }))
					.range([0, width]),
				xAxis = d3.axisBottom(x)
					.ticks(d3.timeMonth, 1)
					.tickFormat(d3.timeFormat("%B")),
				y = d3.scaleLinear()
					.domain([0, d3.max(data, function(d) { return d.value; })])
					.range([height, 0]),
				yAxis = d3.axisLeft(y)
					.ticks(4) // eslint-disable-line no-magic-numbers
					.tickFormat(d3.format(".2s"))
					.tickSize(-1 * width);

			svg.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis);

			svg.append("g")
				.attr("class", "yAxis")
				.call(yAxis)
				.call(function(g) { g.select(".domain").remove(); });

			svg.append("path")
				.datum(data)
				.attr("fill", "none")
				.attr("stroke-width", 1.5) // eslint-disable-line no-magic-numbers
				.attr("stroke", "black")
				.attr("class", "line " + accentColorClass)
				.attr("d", d3.line()
					.x(function(d) { return x(d.date) })
					.y(function(d) { return y(d.value) })
				)

			svg.selectAll("dot")
				.data(data)
				.enter().append("circle")
				.attr("r", 1) // eslint-disable-line no-magic-numbers
				.attr("cx", function(d) { return x(d.date); })
				.attr("cy", function(d) { return y(d.value); })
				.attr("class", "circle " + accentColorClass);

			var circleDiameter = 4, // eslint-disable-line one-var
				focuscircle = svg.append("g")
					.append("circle")
					.attr("r", circleDiameter)
					.attr("class", "circle " + accentColorClass)
					.style("display", "none"),
				focus = svg.append("g")
					.attr("class", "focus")
					.style("display", "none"),
				tooltipWidth = 100,
				tooltipMargin = 10;

			focus.append("rect")
				.attr("class", "tooltip")
				.attr("width", tooltipWidth)
				.attr("height", 50) // eslint-disable-line no-magic-numbers
				.attr("x", tooltipMargin)
				.attr("y", -22) // eslint-disable-line no-magic-numbers
				.attr("rx", 4) // eslint-disable-line no-magic-numbers
				.attr("ry", 4); // eslint-disable-line no-magic-numbers

			focus.append("text")
				.attr("class", "tooltip-date")
				.attr("x", 18) // eslint-disable-line no-magic-numbers
				.attr("y", -2); // eslint-disable-line no-magic-numbers

			focus.append("text")
				.attr("class", "tooltip-value")
				.attr("x", 18) // eslint-disable-line no-magic-numbers
				.attr("y", 18); // eslint-disable-line no-magic-numbers

			svg.append("rect")
				.attr("class", "overlay")
				.attr("width", width)
				.attr("height", height)
				.on("mouseover", function() {
					focus.style("display", null);
					focuscircle.style("display", null);
				})
				.on("mouseout", function() {
					focus.style("display", "none");
					focuscircle.style("display", "none");
				})
				.on("mousemove", mousemove);

			function mousemove() {
				var x0 = x.invert(d3.mouse(this)[0]),
					i = bisectDate(data, x0, 1),
					d0 = data[i - 1],
					d1 = data[i],
					d = x0 - d0.date > d1.date - x0 ? d1 : d0,
					lastDate = data[data.length - 1].date,
					shift = tooltipWidth + circleDiameter + circleDiameter + tooltipMargin,
					calcX = x(d.date) + shift;
				if (calcX > x(lastDate)) {
					focus.attr("transform", "translate(" + (x(d.date) - shift) + "," + y(d.value) + ")");
				} else {
					focus.attr("transform", "translate(" + x(d.date) + "," + y(d.value) + ")");
				}
				focuscircle.attr("transform", "translate(" + x(d.date) + "," + y(d.value) + ")");
				focus.select(".tooltip-date").text(tickTimeFormat(d.date));
				focus.select(".tooltip-value").text(d3.format(",")(d.value));
			}

		}

		var data = $id.data("chart-raw"); // eslint-disable-line one-var

		for (var i = data.length - 1; i >= 0; i--) { // eslint-disable-line one-var
			data[i].date = parseTimeFormat(data[i].date);
		}

		addVisual(data);

	}

	$(".d3-chart").each(function() {
		lineChart(this.id);
	});


});