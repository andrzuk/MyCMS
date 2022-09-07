/*
 * Chart.js / AJAX
 */

var users = new Array();
var days = new Array();
var factor_general = new Array();
var factor_1 = new Array();
var factor_2 = new Array();
var factor_3 = new Array();
var factor_4 = new Array();
var factor_5 = new Array();
var factor_6 = new Array();
var efficient_general = new Array();
var efficient_1 = new Array();
var efficient_2 = new Array();
var efficient_3 = new Array();
var efficient_4 = new Array();
var efficient_5 = new Array();
var efficient_6 = new Array();

function generateChartDates()
{
	var date_from = document.getElementById("date_from").value;
	var date_to = document.getElementById("date_to").value;
	var i = 0;
	var labels = new Array();
	var counters = new Array();
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var response_elements = xhttp.responseXML.getElementsByTagName("item");
			for (i = 0; i < response_elements.length; i++) {
				labels[i] = response_elements[i].getElementsByTagName("task_finish_date")[0].childNodes[0].nodeValue;
				counters[i] = response_elements[i].getElementsByTagName("date_counter")[0].childNodes[0].nodeValue;
			}
			var canvas = document.getElementById("canvas-dates");
			var ctx = canvas.getContext("2d");
			var barChartData = {
				labels: labels,
				datasets: [
					{
						fillColor: "rgba(230,130,50,0.5)",
						strokeColor: "rgba(230,130,50,0.8)",
						highlightFill: "rgba(230,130,50,0.75)",
						highlightStroke: "rgba(230,130,50,1)",
						data: counters
					},
				]
			}
			if (i > 0) {
				window.myBar = new Chart(ctx).Bar(barChartData, {
					animation: true,
					showTooltips: false,
					responsive: true
				});
			}
			else {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				var image = new Image();
				image.onload = function () {
					ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
				};
				image.src = "./img/empty_chart.png";
			}
		}
	};
	xhttp.open("GET", "./_dev/get_chart_data.php?date_from=" + date_from + "&date_to=" + date_to + "&type=dates", true);
	xhttp.send();
}
 
function generateChartTypes()
{
	var date_from = document.getElementById("date_from").value;
	var date_to = document.getElementById("date_to").value;
	var i = 0;
	var labels = new Array();
	var counters = new Array();
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var response_elements = xhttp.responseXML.getElementsByTagName("item");
			for (i = 0; i < response_elements.length; i++) {
				labels[i] = response_elements[i].getElementsByTagName("task_type")[0].childNodes[0].nodeValue;
				counters[i] = response_elements[i].getElementsByTagName("task_counter")[0].childNodes[0].nodeValue;
			}
			var canvas = document.getElementById("canvas-types");
			var ctx = canvas.getContext("2d");
			var barChartData = {
				labels: labels,
				datasets: [
					{
						fillColor: "rgba(151,187,205,0.5)",
						strokeColor: "rgba(151,187,205,0.8)",
						highlightFill: "rgba(151,187,205,0.75)",
						highlightStroke: "rgba(151,187,205,1)",
						data: counters
					},
				]
			}
			if (i > 0) {
				window.myBar = new Chart(ctx).Bar(barChartData, {
					animation: true,
					showTooltips: false,
					responsive: true
				});
			}
			else {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				var image = new Image();
				image.onload = function () {
					ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
				};
				image.src = "./img/empty_chart.png";
			}
		}
	};
	xhttp.open("GET", "./_dev/get_chart_data.php?date_from=" + date_from + "&date_to=" + date_to + "&type=types", true);
	xhttp.send();
}
 
function generateChartDays()
{
	var date_from = document.getElementById("date_from").value;
	var date_to = document.getElementById("date_to").value;
	var i = 0;
	var labels = new Array();
	var counters = new Array();
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var response_elements = xhttp.responseXML.getElementsByTagName("item");
			for (i = 0; i < response_elements.length; i++) {
				labels[i] = response_elements[i].getElementsByTagName("task_day")[0].childNodes[0].nodeValue;
				counters[i] = response_elements[i].getElementsByTagName("task_counter")[0].childNodes[0].nodeValue;
			}
			var canvas = document.getElementById("canvas-days");
			var ctx = canvas.getContext("2d");
			var barChartData = {
				labels: labels,
				datasets: [
					{
						fillColor: "rgba(102,156,42,0.5)",
						strokeColor: "rgba(102,156,42,0.8)",
						highlightFill: "rgba(102,156,42,0.75)",
						highlightStroke: "rgba(102,156,42,1)",
						data: counters
					},
				]
			}
			if (i > 0) {
				window.myBar = new Chart(ctx).Bar(barChartData, {
					animation: true,
					showTooltips: false,
					responsive: true
				});
			}
			else {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				var image = new Image();
				image.onload = function () {
					ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
				};
				image.src = "./img/empty_chart.png";
			}
		}
	};
	xhttp.open("GET", "./_dev/get_chart_data.php?date_from=" + date_from + "&date_to=" + date_to + "&type=days", true);
	xhttp.send();
}
 
function generateChartPointers()
{
	var date_from = $("#date_from").val();
	var date_to = $("#date_to").val();
	var client_id = $("#user").val();
	users = [];
	days = [];
	factor_general = [];
	factor_1 = [];
	factor_2 = [];
	factor_3 = [];
	factor_4 = [];
	factor_5 = [];
	factor_6 = [];
	efficient_general = [];
	efficient_1 = [];
	efficient_2 = [];
	efficient_3 = [];
	efficient_4 = [];
	efficient_5 = [];
	efficient_6 = [];

	$.ajax({
		type: 'GET',
		url: './_dev/get_pointers_data.php?date_from=' + date_from + '&date_to=' + date_to + '&client_id=' + client_id,
		success: function(data){
			/*
			$.each(data.children, function(i, response){
				$.each(response.children, function(j, items){
					$.each(items.children, function(k, item){
						if (item.nodeName == 'user') {
							users[j] = item.textContent;
						}
						if (item.nodeName == 'day') {
							days[j] = item.textContent;
						}
						if (item.nodeName == 'factor_general') {
							factor_general[j] = item.textContent;
						}
						if (item.nodeName == 'factor_1') {
							factor_1[j] = item.textContent;
						}
						if (item.nodeName == 'factor_2') {
							factor_2[j] = item.textContent;
						}
						if (item.nodeName == 'factor_3') {
							factor_3[j] = item.textContent;
						}
						if (item.nodeName == 'factor_4') {
							factor_4[j] = item.textContent;
						}
						if (item.nodeName == 'factor_5') {
							factor_5[j] = item.textContent;
						}
						if (item.nodeName == 'factor_6') {
							factor_6[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_general') {
							efficient_general[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_1') {
							efficient_1[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_2') {
							efficient_2[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_3') {
							efficient_3[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_4') {
							efficient_4[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_5') {
							efficient_5[j] = item.textContent;
						}
						if (item.nodeName == 'efficient_6') {
							efficient_6[j] = item.textContent;
						}
					});					
				});
			});
			*/
			var response_elements = data.getElementsByTagName("item");
			for (i = 0; i < response_elements.length; i++) {
				users[i] = response_elements[i].getElementsByTagName("user")[0].childNodes[0].nodeValue;
				days[i] = response_elements[i].getElementsByTagName("day")[0].childNodes[0].nodeValue;
				factor_general[i] = response_elements[i].getElementsByTagName("factor_general")[0].childNodes[0].nodeValue;
				factor_1[i] = response_elements[i].getElementsByTagName("factor_1")[0].childNodes[0].nodeValue;
				factor_2[i] = response_elements[i].getElementsByTagName("factor_2")[0].childNodes[0].nodeValue;
				factor_3[i] = response_elements[i].getElementsByTagName("factor_3")[0].childNodes[0].nodeValue;
				factor_4[i] = response_elements[i].getElementsByTagName("factor_4")[0].childNodes[0].nodeValue;
				factor_5[i] = response_elements[i].getElementsByTagName("factor_5")[0].childNodes[0].nodeValue;
				factor_6[i] = response_elements[i].getElementsByTagName("factor_6")[0].childNodes[0].nodeValue;
				efficient_general[i] = response_elements[i].getElementsByTagName("efficient_general")[0].childNodes[0].nodeValue;
				efficient_1[i] = response_elements[i].getElementsByTagName("efficient_1")[0].childNodes[0].nodeValue;
				efficient_2[i] = response_elements[i].getElementsByTagName("efficient_2")[0].childNodes[0].nodeValue;
				efficient_3[i] = response_elements[i].getElementsByTagName("efficient_3")[0].childNodes[0].nodeValue;
				efficient_4[i] = response_elements[i].getElementsByTagName("efficient_4")[0].childNodes[0].nodeValue;
				efficient_5[i] = response_elements[i].getElementsByTagName("efficient_5")[0].childNodes[0].nodeValue;
				efficient_6[i] = response_elements[i].getElementsByTagName("efficient_6")[0].childNodes[0].nodeValue;
			}
			var canvas = $("#canvas-pointers")[0];
			var ctx = canvas.getContext("2d");
			var barChartData = {
				labels: days,
				datasets: [
					{
						fillColor: "rgba(151,187,205,0.5)",
						strokeColor: "rgba(151,187,205,0.8)",
						highlightFill: "rgba(151,187,205,0.75)",
						highlightStroke: "rgba(151,187,205,1)",
						data: factor_general
					},
				]
			}
			if (days.length > 0 && factor_general.length > 0) {
				window.myBar = new Chart(ctx).Bar(barChartData, {
					animation: true,
					showTooltips: false,
					responsive: true
				});
			}
			else {
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				var image = new Image();
				image.onload = function () {
					ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
				};
				image.src = "./img/empty_chart.png";
			}
			$("#player").find("option").remove();
			$("#player").append($('<option>', {value: 0, text: '(-)'}));
			var inserted = 0;
			var sorted = new Array();
			sorted = users.slice();
			sorted.sort(function(first, last){ return first - last });
			$.each(sorted, function(i, value){
				if (value != inserted) {
					$("#player").append($('<option>', {value: value, text: value}));
					inserted = value;
				}
			});
		}
	});
}

function filterChartPointers()
{
	var counters = new Array();
	var filter_days = new Array();
	var filter_counters = new Array();

	var player = $("#player").val();
	var type = $("#type").val();
	var legends = {
		0: 'Wskaźnik globalny', 
		1: 'Wskaźnik uwagi', 
		2: 'Wskaźnik pamięci i uczenia', 
		3: 'Wskaźnik szybkości reakcji', 
		4: 'Wskaźnik funkcji zarządczych', 
		5: 'Wskaźnik mowy i języka', 
		6: 'Wskaźnik sekwencjonowania dźwięków',
		10: 'Efektywność globalna',
		11: 'Efektywność uwagi',
		12: 'Efektywność pamięci i uczenia', 
		13: 'Efektywność szybkości reakcji', 
		14: 'Efektywność funkcji zarządczych', 
		15: 'Efektywność mowy i języka', 
		16: 'Efektywność sekwencjonowania dźwięków'
	};
	
	$("#chart-legend").html(legends[type]);

	if (type == 0) counters = factor_general.slice();
	if (type == 1) counters = factor_1.slice();
	if (type == 2) counters = factor_2.slice();
	if (type == 3) counters = factor_3.slice();
	if (type == 4) counters = factor_4.slice();
	if (type == 5) counters = factor_5.slice();
	if (type == 6) counters = factor_6.slice();
	if (type == 10) counters = efficient_general.slice();
	if (type == 11) counters = efficient_1.slice();
	if (type == 12) counters = efficient_2.slice();
	if (type == 13) counters = efficient_3.slice();
	if (type == 14) counters = efficient_4.slice();
	if (type == 15) counters = efficient_5.slice();
	if (type == 16) counters = efficient_6.slice();
	
	$.each(users, function(i, value){
		if (player == 0 || value == player) {
			filter_days.push(days[i]);
			filter_counters.push(counters[i]);
		}
	});
	
	var canvas = $("#canvas-pointers")[0];
	var ctx = canvas.getContext("2d");
	var barChartData = {
		labels: filter_days,
		datasets: [
			{
				fillColor: "rgba(151,187,205,0.5)",
				strokeColor: "rgba(151,187,205,0.8)",
				highlightFill: "rgba(151,187,205,0.75)",
				highlightStroke: "rgba(151,187,205,1)",
				data: filter_counters
			},
		]
	}
	if (filter_days.length > 0 && filter_counters.length > 0) {
		window.myBar = new Chart(ctx).Bar(barChartData, {
			animation: true,
			showTooltips: false,
			responsive: true
		});
	}
	else {
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		var image = new Image();
		image.onload = function () {
			ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
		};
		image.src = "./img/empty_chart.png";
	}
}

function generateChartSelectedPoints(index, labels, series_points_1, series_points_2, series_points_3) 
{
	var canvas = $("#canvas-pointers-" + index)[0];
	var ctx = canvas.getContext("2d");
	var barChartData = {
		labels: labels,
		datasets: [
			{
				fillColor: "rgba(0,180,0,0.1)",
				strokeColor: "rgba(0,180,0,0.8)",
				highlightFill: "rgba(0,180,0,0.5)",
				highlightStroke: "rgba(0,180,0,1.0)",
				data: series_points_1
			},
			{
				fillColor: "rgba(255,0,0,0.1)",
				strokeColor: "rgba(255,0,0,0.8)",
				highlightFill: "rgba(255,0,0,0.5)",
				highlightStroke: "rgba(255,0,0,1.0)",
				data: series_points_2
			},
			{
				fillColor: "rgba(51,87,205,0.1)",
				strokeColor: "rgba(51,87,205,0.8)",
				highlightFill: "rgba(51,87,205,0.5)",
				highlightStroke: "rgba(51,87,205,1.0)",
				data: series_points_3
			},
		]
	}
	if (labels.length > 0) {
		window.myBar = new Chart(ctx).Line(barChartData, {
			animation: true,
			showTooltips: false,
			responsive: false,
		});
	}
	else {
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		var image = new Image();
		image.onload = function () {
			ctx.drawImage(image, (canvas.width - image.width) / 2, (canvas.height - image.height) / 2);
		};
		image.src = "./img/empty_chart.png";
	}	
}
