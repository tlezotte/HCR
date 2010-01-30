$(document).ready(function(){
	/* ===== jQuery UI Calendar ===== */
	$.datepicker.setDefaults({showOn: 'both', buttonImageOnly: true, dateFormat: 'YMD-', buttonImage: '/Common/images/calendar.gif'});
	$('input.popupcalendar').datepicker();

	function findValueCallback(event, data, formatted) {
		$("<li>").text( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}
	
	function formatItem(row) {
		//var row = row.split("|");
		return row[0] + " (id: " + row[1] + ")";
	}
	
	function formatResult(row) {
		return row[0] + " " + row[1];
	}
	
//	$("#replacement").autocomplete("../data/autocomplete.php?t=employees", {
//		max: 10,
//		autoFill: true,
//		mustMatch: false,
//		matchContains: false,
//		scrollHeight: 220,
//		formatItem: formatItem,
//		formatResult: formatResult
//	});

	$("#replacement").autocomplete("../data/autocomplete.php?t=employees", {
		width: 320,
		max: 10,
		highlight: false,
		scroll: true,
		mustMatch: true,
		scrollHeight: 300,
		formatItem: function(data, i, n, value) {
			return "data: " + data + " i: " + i + " n: " + n + " value: " + value + "\n";
		},
		formatResult: function(data, value) {
			return data[1];
		}
	});
	
	$(":text, textarea").result(findValueCallback).next().click(function() {
		$(this).prev().search();
	});
	$("#replacement").result(function(event, data, formatted) {
		$(this).find("..+/input").val(data[1]);
	});
});						