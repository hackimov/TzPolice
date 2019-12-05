var workdatasets = {};
var rezervdatasets = {};
var disable_series = [];

function loaddata()
{
	
	var script_name = $("#loaddata").attr('name');
	
	var i = {};
	i.json_data = 1;
	i.start = $('#start').val();
	i.finish = $('#finish').val();
	$.post("_modules/"+script_name+".php", i, setdata, "json");
	
	//setTimeout(loaddata, 300000);
	
}

function IntToColor(num) {
	
	var r = '#000000';
	
	switch (num) {
	case 0:
		r = '#FFFFFF'
		break
	case 1:
		r = '#D4FFD4'
		break
	case 2:
		r = '#FFFF00'
		break
	case 3:
		r = '#80FF00'
		break
	case 4:
		r = '#FF00FF'
		break
	case 5:
		r = '#FF0000'
		break
	case 6:
		r = '#8080FF'
		break
	case 7:
		r = '#FFAA00'
		break
	case 8:
		r = '#9FECFF'
		break
	case 9:
		r = '#FF80FF'
		break
	case 10:
		r = '#FF8080'
		break
	case 11:
		r = '#CC99FF'
		break
	case 12:
		r = '#B1FF11'
		break
	case 13:
		r = '#BBBB00'
		break
	case 14:
		r = '#B300B3'
		break
	case 15:
		r = '#AE0000'
		break
	case 16:
		r = '#009500'
		break
	case 17:
		r = '#408080'
		break
	default:
		r = '#000000'
		break
	}
	
	return r;
}

function plotAccordingToChoices() {

	var data = [];

	$.each(workdatasets, function(key, val) {
		
		// если серия в disable_series - обнуляем данные серии
		s_name = val.label.split('.').join('');
		
		if ($.inArray(s_name, disable_series) > -1) {
			val.color = '#777';
			val.data = [];			
		}
		
		data.push(workdatasets[key]);
		
	});
	
	var options = {
		
		series: {
			shadowSize: 0,
			lines: {
				steps: false
			}
		},
		
		legend: {
			container: $("#choices"),
			labelFormatter: function(label, series) {
				// series is the series object for the label
				return label+'</td><td>0';
			}
		},
		
		crosshair: {
				mode: "x"
		},
		grid: {
			hoverable: true,
			autoHighlight: false
		},
		xaxis: {
			mode: "time",
			timeformat: "%d.%m<br>%H:%M"
		}
	};
	
	
	if (data.length > 0) {
		$.plot("#placeholder", data, options);
	}
}

function setdata(datasets)
{
	
	workdatasets = $.extend(true, {}, datasets);
	
	var i = 0;
	$.each(workdatasets, function(key, val) {
		//val.color = IntToColor(i);
		val.color = i;
		++i;
	});
	
	rezervdatasets = $.extend(true, {}, workdatasets);
	
	plotAccordingToChoices();
	
}

function on_change(s_name)
{
	s_name = s_name.split('.').join('');
	
	if ($.inArray(s_name, disable_series) > -1) {
		disable_series.splice( $.inArray(s_name, disable_series), 1 );
	} else {
		$.merge(disable_series, [s_name]); 
	}
	
	workdatasets = $.extend(true, {}, rezervdatasets);	
	
	plotAccordingToChoices();
	
}

$(document).ready(function() {
	
	loaddata();
	
	$("#loaddata").live('click', (function() {
		loaddata(this.name);
		return false;
	}));
	
	$("#choices tr:has(td.legendColorBox)").live('click', (function() {
		on_change(this.childNodes[1].innerHTML);
		return false;
	}));
	

	$('.calendar').datePicker({
		createButton:false,
		clickInput:true
	});
	
	$("#placeholder").bind("plothover",  function (event, pos, item) {
		
		latestPosition = pos;
		
		
		//if (!updateLegendTimeout) {
		//	updateLegendTimeout = setTimeout(updateLegend, 50);
		//}
		
	});

	
});


