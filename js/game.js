/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


function vote() {
	var vote = $("#vote").val();
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/vote",
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
		url: APIdir + "/game/"+room_name+"/"+game_name,
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

if (!pollFreq)
	pollFreq = 5000;

setInterval(pollDayChanged, pollFreq);