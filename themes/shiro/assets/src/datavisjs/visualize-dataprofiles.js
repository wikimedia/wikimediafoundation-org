/* global d3 */
/* eslint-disable no-magic-numbers */

jQuery(document).ready(function($) {

	'use strict';

	function dataProfiles(id) {
		var $id = $("#" + id),
			margin = 10,
			strokeW = 2,
			numFormat = d3.format(","),
			width = $id.width(),
			profInRowMin = 2,
			idealX = 190,
			shift = { x: width < profInRowMin*idealX ? width/2 - margin : idealX, y: 125 },
			maxFeature1 = $id.data("max-feature-1") ? $id.data("max-feature-1") : 90,
			maxFeature2 = $id.data("max-feature-2") ? $id.data("max-feature-2") : 90,
			profInRow = Math.max(Math.floor(width / shift.x), profInRowMin),
			screenWidth = $("body").width(),
			minWidth = idealX * profInRow,
			smallBp = 500, // per _variables.scss
			masterunit = $id.data("chart-masterunit") ? $id.data("chart-masterunit") : 500000,
			horUnits = 5,
			moreHorUnits = width < minWidth ? 24 : 30,
			data = $id.data("chart-raw"),
			dataStart = $id.data("slice-start"),
			dataEnd = $id.data("slice-end"),
			// This is setup so properties/labels don't matter
			// But labels map to this order: circle, rectangle, ellipses
			labels = $id.data("chart-labels"),
			legendLabelDistance = 45,
			icons = $id.data("chart-icons"),
			except = $id.data("chart-except"),
			exceptI = 0,
			exceptLang = except ? data[labels[exceptI]] : "",
			exceptHeightMulti = 3.5,
			height = except ? shift.y * exceptHeightMulti : yPos(null, dataEnd - dataStart -1) + shift.y;

		function yPos(d, i) {
			var gutter = margin * 6,
				topmargin = except ? 0 : margin * 5;
			return topmargin + (shift.y + gutter) * Math.floor(i / profInRow);
		}

		function xPos(d, i) {
			return i % profInRow * shift.x;
		}

		function fAttr(sel, shape, i, attr) {
			return parseFloat(sel.selectAll(shape).nodes()[i].getAttribute(attr));
		}

		function addVis() {
			if (!data) {
				return;
			}

			function editorsConstruct(g) {
				return g
					.attr("transform", "translate(" + margin + "," + margin + ")")
					.attr("class", "profile-feature1")
					.selectAll("circle")
					.data(ddata)
					.join("circle")
					.attr("cx", function(d, i) {return except ? xPos(d, i) + Math.max(width/4, editorScale(d[labels[1]]) + margin) : xPos(d, i) + editorScale(d[labels[1]])})
					.attr("cy", function(d, i) {return yPos(d, i) + editorScale(d[labels[1]])})
					.attr("r", function(d) {return editorScale(d[labels[1]])})
			}

			function articlesConstruct(g, e) {
				return g
					.attr("transform", "translate(" + margin + "," + margin + ")")
					.attr("class", "profile-feature2")
					.selectAll("rect")
					.data(ddata)
					.join("rect")
					.attr("x", function(d, i) {
						var normalPosX = fAttr(e, "circle", i, "cx") + fAttr(e, "circle", i, "r") + margin/2,
							exceptPosX = width*0.4 + (width/2-articleScale(d[labels[2]])*0.75)/2,
							posX = except ? exceptPosX : normalPosX,
							smallPosX = fAttr(e, "circle", i, "cx") + fAttr(e, "circle", i, "r") + margin * 3;
						return width < minWidth && except ? smallPosX : posX;
					})
					.attr("y", function(d, i) {return yPos(d, i)})
					.attr("width", function(d) {return articleScale(d[labels[2]])*0.75})
					.attr("height", function(d) {return articleScale(d[labels[2]])})
					.attr("rx", function(d) {return articleScale(d[labels[2]])*0.08})
					.attr("ry", function(d) {return articleScale(d[labels[2]])*0.08});
			}

			function viewsConstruct(g, e, a) {
				g.attr("class", "profile-feature3");
				/* eslint-disable no-loop-func, one-var */
				for (var ix = 0; ix < ddata.length; ix++) {
					var ry = viewsUnitScale(masterunit) / 3 * 2,
						normalPosX = fAttr(a, "rect", ix, "x") + fAttr(a, "rect", ix, "width") + margin,
						normalPosY = fAttr(a, "rect", ix, "y") + ry,
						exceptPosX = (width - viewsXScale(moreHorUnits)) / 2, // center
						exceptPosY = fAttr(e, "circle", ix, "cx") + fAttr(e, "circle", ix, "r") + legendLabelDistance + margin * 2,
						transX = except ? exceptPosX : normalPosX,
						transY = except ? exceptPosY : normalPosY;

					g
						.append("g")
						.attr("class", "viewsContainer")
						.attr("transform", "translate(" + margin + "," + margin + ")")
						.selectAll("ellipse")
						.data(ddata[ix].unitArray)
						.join("ellipse")
						.attr("class", "views-" + ix)
						.attr("cx", function(d) {return transX + viewsXScale(d.x)})
						.attr("cy", function(d) {return transY + viewsYScale(d.y)})
						.attr("rx", function(d) {return viewsUnitScale(d.value)})
						.attr("ry", function(d) {return viewsUnitScale(d.value)/3*2});
					/* eslint-disable no-loop-func, one-var */
				}

				return g;
			}

			function labelConstruct(g, e, offset) {
				var labelX = except ? margin*exceptHeightMulti + margin : margin * 2,
					labelY = except ? height - margin : shift.y*0.75 + margin;
				return g
					.call(function(g1) {
						return g1
							.append("g")
							.attr("class", "main-labels")
							.selectAll("text")
							.data(ddata)
							.join("text")
							.attr("text-anchor", "start")
							.attr("dy", -5) // create some padding at the bottom
							.attr("x", function(d, i) {return screenWidth < smallBp ? xPos(d, i) + margin : xPos(d, i) + labelX})
							.attr("y", function(d, i) {return yPos(d, i) + labelY})
							.text(function(d) {return labels.length > 4 ? d[labels[4]] : d[labels[0]] })
					})
					.call(function(g2) {
						return g2
							.append("g")
							.attr("class", "main-label-ranks")
							.style("display", "none")
							.selectAll("text")
							.data(ddata)
							.join("text")
							.attr("text-anchor", "start")
							.attr("dy", -30) // above mainlabel
							.attr("x", function(d, i) {return screenWidth < smallBp ? xPos(d, i) + margin : xPos(d, i) + labelX})
							.attr("y", function(d, i) {return yPos(d, i) + labelY})
							.text(function(d, i) {return i + 1 + offset});
					})
			}


			function borderleft(g) {
				var borderHeight = except ? height : shift.y - margin,
					endX = except ? margin * exceptHeightMulti : margin;
				return g
					.attr("stroke-width", strokeW)
					.attr("class", "borderleft")
					.selectAll("line")
					.data(ddata)
					.join("line")
					.attr("x1", function(d, i) {return xPos(d, i) + strokeW})
					.attr("x2", function(d, i) {return screenWidth < smallBp ? xPos(d, i) + strokeW : xPos(d, i) + endX})
					.attr("y1", function(d, i) {return yPos(d, i)})
					.attr("y2", function(d, i) {return yPos(d, i) + borderHeight});
			}

			function legendConstruct(g, e, a, v) {
				var datalabelText,
					datalabelIcons,
					d0 = ddata[0],
					aObj = {
						x: fAttr(a, "rect", 0, "x"),
						y: fAttr(a, "rect", 0, "y"),
						w: fAttr(a, "rect", 0, "width"),
						h: fAttr(a, "rect", 0, "height"),
					},
					vx1 = fAttr(v, "ellipse", moreHorUnits-1, "cx"),
					vxCenter = fAttr(v, "ellipse", Math.floor(moreHorUnits/2) - 1, "cx"),
					vy1 = fAttr(v, "ellipse", moreHorUnits * (d0.Rows - 1), "cy"),
					lastRowOverHalf = moreHorUnits * d0.Rows % d0.unitArray.length > Math.ceil(moreHorUnits/2) + 1,
					marginBottom = 60,
					customLegend = [
						{
							x: fAttr(e, "circle", 0, "cx"),
							y: fAttr(e, "circle", 0, "cy") + fAttr(e, "circle", 0, "r") - margin,
							y2: height - marginBottom,
							h: legendLabelDistance,
							class: 1,
							highlight: true,
							name: [numFormat(d0[labels[1]]) + " " + labels[1]]
						}, {
							x: aObj.x + aObj.w * 0.5,
							y: aObj.y + aObj.h - margin,
							y2: height - marginBottom,
							h: legendLabelDistance,
							class: 2,
							highlight: true,
							name: [numFormat(d0[labels[2]]) + " " + labels[2]]
						}, {
							x: vxCenter,
							y: lastRowOverHalf ? vy1 : vy1 + margin,
							y2: height - marginBottom * 1.5,
							h: legendLabelDistance,
							class: 3,
							highlight: true,
							name: [numFormat(d0[labels[3]]) + " " + labels[3]]
						}, {
							x: vx1,
							y: vy1 - viewsUnitScale.range()[1] * 2,
							y2: height - marginBottom * 2,
							h: legendLabelDistance/2,
							class: 3,
							highlight: false,
							name: [numFormat(masterunit), labels[3]]
						}
					];

				g
					.append("g")
					.attr("transform", "translate(" + margin + "," + margin + ")")
					.attr("class", "data-label-lines")
					.selectAll("line")
					.data(customLegend)
					.join("line")
					.attr("class", function(d) {return "profile-feature" + d.class})
					.attr("x1", function(d) {return d.x})
					.attr("x2", function(d) {return d.x})
					.attr("y1", function(d) {return d.y})
					.attr("y2", function(d) {return d.y + d.h})
					.attr("stroke-dasharray", function(d) {return d.highlight ? "" : margin/3 + "," + margin/4});

				datalabelText = g
					.append("g")
					.attr("transform", "translate(" + margin + "," + margin + ")")
					.attr("class", "data-label-text")
					.selectAll("text")
					.data(customLegend)
					.join("text")
					.attr("class", function(d) {return "profile-feature" + d.class})
					.attr("x", function(d) {return d.x})
					.attr("y", function(d) {return d.y + d.h})
					.attr("dy", margin * 1.5)
					.attr("stroke-width", 0)
					.attr("font-weight", function(d) {return d.highlight ? "bold" : "normal"})
					.attr("text-anchor", "middle");

				datalabelText
					.append("tspan")
					.attr("x", function(d) {return d.x})
					.attr("dx", viewsUnitScale.range()[1])
					.text(function(d) {return d.name[0]});

				datalabelText
					.append("tspan")
					.attr("x", function(d) {return d.x})
					.attr("dy", 16)
					.attr("dx", viewsUnitScale.range()[1])
					.text(function(d) {return d.name[1]});

				datalabelIcons = g
					.append("g")
					.attr("transform", "translate(" + margin + "," + margin + ")")
					.attr("class", "data-label-icon");

				datalabelText.selectAll("tspan").each(function(dd, i) {
					if (i === 0 && dd.highlight) {
						var textWidth = this.getBBox().width;
						datalabelIcons.call(function(ltext) {
							return ltext
								.append("image")
								.attr("class", "profile-feature" + dd.class)
								.attr("xlink:href", icons[dd.class - 1])
								.attr("width", margin * 1.1)
								.attr("height", margin * 1.1)
								.attr("x", dd.x - textWidth/2 - margin)
								.attr("y", dd.y + dd.h + margin * 0.5);
						})
					}
				});
				return g;
			}

			function mappedViewsData(_data) {
				return _data.map(function(p, index) {
					var unitSum = Math.floor(p[labels[3]]/masterunit),
						remain = p[labels[3]] % masterunit,
						specHorUnits = index === exceptI ? moreHorUnits : horUnits,
						rows = Math.ceil(unitSum/specHorUnits);
					p.unitArray = [];
					for (var i = 0; i < rows; i++) { // eslint-disable-line one-var
						for (var h = 0; h < specHorUnits; h++) { // eslint-disable-line one-var
							p.unitArray.push({ x: h, y: i, value: masterunit })
						}
					}
					p.unitArray = p.unitArray.slice(0, unitSum); // only take the amount of spaces needed
					p.unitArray[p.unitArray.length-1].value = remain; // last space is the remainder, not a full masterunit
					p.Rows = rows;
					return p;
				})
			}

			function hover(g, index, e, a, v) { //eslint-disable-line max-params
				if (index === null) return g
					.transition()
					.attr("opacity", 0)
					.transition()
					.style("display", "none");

				var editXPos = fAttr(e, "circle", index, "cx"),
					artXPos = fAttr(a, "rect", index, "x"),
					artW = fAttr(a, "rect", index, "width"),
					posInRow = fAttr(v, ".views-" + index, Math.min(Math.floor(horUnits/2), ddata[index].unitArray.length - 1), "cx") + margin,
					size = margin,
					shiftSmScreens = width < minWidth ? 20 : 0,
					hoverLabels = [{
						value: ddata[index][labels[1]],
						x: Math.round(editXPos + margin),
						y1: yPos(null, index) + margin,
						h: 36,
						labelPos: [editXPos, yPos(null, index) - 36]
					},
					{
						value: ddata[index][labels[2]],
						x: Math.round(artXPos + margin + artW/2),
						y1: yPos(null, index) + margin,
						h: 21,
						labelPos: [artXPos + artW / 2, yPos(null, index) - 21]
					},
					{
						value: ddata[index][labels[3]],
						x: Math.round(posInRow),
						y1: yPos(null, index) + viewsUnitScale.range()[1]*2,
						h: 6,
						labelPos: [posInRow - margin - shiftSmScreens, yPos(null, index) - 6]
					}
					];

				g
					.transition()
					.attr("opacity", 1)
					.style("display", null)
					.style("pointer-events", "none")

				g.selectAll("line")
					.data(hoverLabels)
					.join("line")
					.attr("class", "hover-label-lines")
					.attr("x1", function(d) {return d.x})
					.attr("x2", function(d) {return d.x})
					.attr("y1", function(d) {return d.y1})
					.attr("y2", function(d) {return d.y1 - d.h})
					.style("display", function(d, i) {return checks[i].is(":checked") ? "block" : "none" });

				g.selectAll("text")
					.data(hoverLabels)
					.join("text")
					.attr("x", function(d) {return d.labelPos[0]})
					.attr("y", function(d) {return d.labelPos[1]})
					.attr("dy", margin*0.5)
					.attr("dx", margin*1.8)
					.text(function(d) {return numFormat(d.value)})
					.style("display", function(d, i) {return checks[i].is(":checked") ? "block" : "none" });

				if (labels.length > 4) g.append("text")
					.attr("x", screenWidth < smallBp ? xPos(0, index) : xPos(0, index) + margin)
					.attr("y", yPos(null, index) + shift.y)
					.attr("dy", -margin)
					.attr("dx", margin)
					.text(ddata[index][labels[0]]);

				g.selectAll("image")
					.data(hoverLabels)
					.join("image")
					.attr("xlink:href", function(d, i) {return icons[i]})
					.attr("width", size)
					.attr("height", size)
					.attr("x", function(d) {return d.labelPos[0] + margin*0.5})
					.attr("y", function(d) {return d.labelPos[1] - size*0.5})
					.style("display", function(d, i) {return checks[i].is(":checked") ? "block" : "none" });
				return g;
			}

			function checkBoxStates() {
				for (var i = 0; i < checks.length; i++) {
					var $elem = $(".profile-feature" + (i + 1));
					if (checks[i].is(":checked")) {
						$elem.fadeIn();
					} else {
						$elem.fadeOut();
					}
				}
				if (!checks[0].is(":checked") && !checks[1].is(":checked") && !checks[2].is(":checked")) {$(".main-label-ranks").fadeIn()}
				else {$(".main-label-ranks").fadeOut()}
			}

			var svg,
				editors,
				articles,
				views,
				tooltip,
				container = d3.select("#" + id),
				checks = [$("#feature1"), $("#feature2"), $("#feature3")],
				aspect = width / height,
				mappedData = mappedViewsData(data),
				ddata = mappedData.slice(dataStart, dataEnd),
				maxRows = d3.max(mappedData, function(d) { return d.Rows }),
				unitR = 4,
				editorScale = d3.scaleLinear()
					.domain([0, d3.max(data, function(d) { return d[labels[1]] })])
					.range([0, maxFeature1]), //min and max radius of circle
				articleScale = d3.scaleLinear()
					.domain([0, d3.max(data, function(d) { return d[labels[2]] })])
					.range([0, maxFeature2]), //min and max height of rect
				viewsUnitScale = d3.scaleLinear()
					.domain([0, masterunit])
					.range([0, unitR]), // min and max size of of 1 unit
				viewsYScale = d3.scaleLinear()
					.domain([0, maxRows])
					.range([0, maxRows * unitR * 2]), // min and max space for rows
				viewsXScale = d3.scaleLinear()
					.domain([0, horUnits])
					.range([0, horUnits * unitR * 2.5]), // min and max space for columns
				minHeight = editorScale.range()[1] * 2 + viewsYScale.range()[1] + (legendLabelDistance + margin) * 4;

			height = except && height < minHeight ? minHeight : height; // update
			exceptHeightMulti = height/shift.y; // update

			svg = container.append("svg")
				.attr("width", width)
				.attr("height", height)
				.attr("perserveAspectRatio", "xMidYMid meet")
				.attr("viewBox", "0 0 " + width + " " + height);

			// lazy resize, keeps proportions
			$( window ).resize(function(){
				var targetWidth = container.node().getBoundingClientRect().width;
				container.selectAll("svg").attr("width", targetWidth);
				container.selectAll("svg").attr("height", targetWidth / aspect);
			});

			editors = svg.append("g").call(editorsConstruct);
			articles = svg.append("g").call(articlesConstruct, editors);
			views = svg.append("g").call(viewsConstruct, editors, articles);

			svg.append("g").call(labelConstruct, editors, dataStart);
			svg.append("g").call(borderleft);

			if (except) {
				svg.append("g").call(legendConstruct, editors, articles, views);
			} else {
				tooltip = svg
					.append("g")
					.attr("class", "tooltip");

				svg
					.append("g")
					.attr("opacity", 0)
					.attr("class", "tooltipgrid")
					.selectAll("rect")
					.data(ddata)
					.join("rect")
					.attr("class", "hover")
					.attr("x", function(d, i) {return xPos(d, i)})
					.attr("y", function(d, i) {return yPos(d, i)})
					.attr("width", shift.x)
					.attr("height", shift.y)
					.on("touchstart mouseover", function(_, i) {
						if (ddata[i][labels[0]] === exceptLang) return;
						tooltip
							.call(hover, i, editors, articles, views);
					})
					.on("touchend mouseout", function() {tooltip.call(hover, null)});

				// applies to all, but only runs once
				$(".chart-options input[type='checkbox']").click(checkBoxStates);
				checkBoxStates();
			}
		}

		addVis();
	}

	$(".d3-dataprofiles").each(function() {
		dataProfiles(this.id);
	});


});