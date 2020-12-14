/* eslint-disable no-magic-numbers, one-var*/

jQuery(document).ready(function($) {

	'use strict';

	var shortAtts = recentEditsAtts, // eslint-disable-line no-undef
		containerID = "#" + shortAtts["id"],
		container = $(containerID),
		langList = shortAtts['lang_list'],
		langListLong = shortAtts['lang_list_long'],
		label = shortAtts['label'],
		rEditLabel = container.find(".label"),
		rEditTitle = container.find(".title"),
		accent = container.find(".accent"),
		apilimit = 5,
		rEdits = [],
		rEditAnimationI = 0;

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
			// console.log(isoEnd, "\n", isoStart, "\n", end.toLocaleTimeString() + " to " + start.toLocaleTimeString());
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
				// lazy random
				rEdits = rEdits.sort( function() { return Math.random() - 0.5 });
				startEditAnim();
				container.fadeIn();
			});
		} else {
			startEditAnim();
		}
	}

	function startEditAnim() {
		if (rEdits.length > 0) {
			rEditLabel.text(label + " " + rEdits[rEditAnimationI].wiki);
			rEditTitle.text(rEdits[rEditAnimationI].title);
			container.show();
			accent.css("border-width", "1px");
			accent.css("border-style", "solid");
			accent.attr("class", "accent wp20-color-" + (rEditAnimationI % 11 + 1));
			setTimeout(function(){
				rEditAnimationI++;
				if (rEditAnimationI < rEdits.length) {
					startEditAnim();
				} else {
					rEditAnimationI = 0;
					getRecentEdits();
				}
			}, 3000);
		}
	}

	getRecentEdits(true);
});