/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


function vote() {
	var vote = $("#vote").val();
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name + "/vote",
		type: 'GET',
		dataType: 'json',
		data: {
			vote: vote
		},
		success: function() {
			location.reload(true);
		},
		error: function(error) {
			console.error(error);
		}
	});
}

function pollDayChanged() {
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name,
		type: 'GET',
		dataType: 'json',
		success: function(data) {
			if (preDay == undefined)
				preDay = data.game.day.num_day;
			if (data.game.day.num_day != preDay)
				location.reload(true);
			if (data.game.status != 101)
				location.reload(true);
		},
		error: function() {
			location.reload(true);
		}
	});
}

$(function() {
	if (!pollFreq)
		pollFreq = 5000;

	$(".show-role").click(function() {
		var status = $(this).attr("data-status");
		if (status == "invisible") {
			$(".show-role div").first().stop().animate({
				"margin-top": 0
			}, 500);
			$(this).attr("data-status", "visible");
		} else {
			$(".show-role div").first().stop().animate({
				"margin-top": -30
			}, 500);
			$(this).attr("data-status", "invisible");
		}
	});

	setInterval(pollDayChanged, pollFreq);
});