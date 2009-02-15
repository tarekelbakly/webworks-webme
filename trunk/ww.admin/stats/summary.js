$(function () {
var d=window.visitors;

// first correct the timestamps - they are recorded as the daily
// midnights in UTC+0100, but Flot always displays dates in UTC
// so we have to add one hour to hit the midnights in the plot
for (var i = 0; i < d.length; ++i)
	  d[i][0] += 60 * 60 * 1000;

	// helper for returning the weekends in a period
	function weekendAreas(axes) {
		var markings = [];
		var d = new Date(axes.xaxis.min);
		// go to the first Saturday
		d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
		d.setUTCSeconds(0);
		d.setUTCMinutes(0);
		d.setUTCHours(0);
		var i = d.getTime();
		do {
			// when we don't set yaxis the rectangle automatically
			// extends to infinity upwards and downwards
			markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
			i += 7 * 24 * 60 * 60 * 1000;
		} while (i < axes.xaxis.max);

		return markings;
	}
	
	var options = {
		xaxis: { mode: "time" },
		selection: { mode: "x" },
		grid: { markings: weekendAreas },
		legend: { position: 'nw' }
	};
	
	var plot = $.plot($("#placeholder"), [
		{data:d, label:'visitors'},
		{data:window.page_requests, label:'requests', yaxis: 2}
	], options);
	
	var overview = $.plot($("#overview"), [d,{data:window.page_requests,yaxis:2}], {
		lines: { show: true, lineWidth: 1 },
		shadowSize: 0,
		xaxis: { ticks: [], mode: "time" },
		yaxis: { ticks: [], min: 0, max: window.max_visitors},
		yaxis2: { ticks: [], min: 0, max: window.max_page_requests},
		selection: { mode: "x" }
	});

	// now connect the two
	
	$("#placeholder").bind("plotselected", function (event, ranges) {
		// do the zooming
		plot = $.plot($("#placeholder"), [d],
					  $.extend(true, {}, options, {
						  xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
					  }));

		// don't fire event on the overview to prevent eternal loop
		overview.setSelection(ranges, true);
	});
	
	$("#overview").bind("plotselected", function (event, ranges) {
		plot.setSelection(ranges);
	});
});
